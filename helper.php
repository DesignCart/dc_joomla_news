<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_dc_news
 *
 * @copyright   (C) 2026 Design Cart / Paweł Nosko
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Component\Content\Site\Helper\RouteHelper;

/**
 * Helper class for Design Cart News module.
 */
class ModDcNewsHelper
{
    /**
     * Fetch latest articles based on module params.
     *
     * @param   \Joomla\Registry\Registry  $params  Module parameters.
     *
     * @return  array
     */
    public static function getItems($params): array
    {
        $db = Factory::getContainer()->get('DatabaseDriver');
        $app = Factory::getApplication();
        $user = $app->getIdentity();
        $date = Factory::getDate()->toSql();

        $count = max(1, (int) $params->get('count', 6));
        $categoryIds = (array) $params->get('catid', []);
        $featuredOnly = (int) $params->get('featured_only', 0) === 1;

        $query = $db->getQuery(true)
            ->select(
                [
                    'a.id',
                    'a.title',
                    'a.alias',
                    'a.introtext',
                    'a.images',
                    'a.catid',
                    'a.language',
                    'a.created',
                    'a.publish_up',
                    'a.access',
                    'a.featured',
                    'c.title AS category_title',
                    'c.alias AS category_alias',
                ]
            )
            ->from($db->quoteName('#__content', 'a'))
            ->join('INNER', $db->quoteName('#__categories', 'c') . ' ON c.id = a.catid')
            ->where('a.state = 1')
            ->where('c.published = 1')
            ->where('a.access IN (' . implode(',', $user->getAuthorisedViewLevels()) . ')')
            ->where('c.access IN (' . implode(',', $user->getAuthorisedViewLevels()) . ')')
            ->where('(a.publish_up IS NULL OR a.publish_up <= ' . $db->quote($date) . ')')
            ->where('(a.publish_down IS NULL OR a.publish_down >= ' . $db->quote($date) . ')');

        if (!empty($categoryIds)) {
            $categoryIds = array_map('intval', $categoryIds);
            $query->where('a.catid IN (' . implode(',', $categoryIds) . ')');
        }

        if ($featuredOnly) {
            $query->where('a.featured = 1');
        }

        $query->order('a.created DESC');

        $db->setQuery($query, 0, $count);
        $items = (array) $db->loadObjectList();

        if (!$items) {
            return [];
        }

        foreach ($items as $item) {
            $item->link = RouteHelper::getArticleRoute($item->id, $item->catid, $item->language);
            $item->image = self::extractImage($item->images);
            $item->tags = self::getArticleTags((int) $item->id);
        }

        return $items;
    }

    /**
     * Extract intro/full image from content images JSON.
     *
     * @param   string  $images  JSON encoded images.
     *
     * @return  string
     */
    private static function extractImage(string $images): string
    {
        if ($images === '') {
            return '';
        }

        $decoded = json_decode($images, true);

        if (!is_array($decoded)) {
            return '';
        }

        return (string) ($decoded['image_intro'] ?? $decoded['image_fulltext'] ?? '');
    }

    /**
     * Get tags assigned to article.
     *
     * @param   int  $contentId  Content item id.
     *
     * @return  array
     */
    private static function getArticleTags(int $contentId): array
    {
        $db = Factory::getContainer()->get('DatabaseDriver');
        $app = Factory::getApplication();
        $user = $app->getIdentity();

        $query = $db->getQuery(true)
            ->select(
                [
                    't.id',
                    't.title',
                    't.alias',
                    't.language',
                ]
            )
            ->from($db->quoteName('#__tags', 't'))
            ->join('INNER', $db->quoteName('#__contentitem_tag_map', 'm') . ' ON m.tag_id = t.id')
            ->where('m.content_item_id = ' . $contentId)
            ->where('m.type_alias = ' . $db->quote('com_content.article'))
            ->where('t.published = 1')
            ->where('t.access IN (' . implode(',', $user->getAuthorisedViewLevels()) . ')')
            ->order('t.title ASC');

        $db->setQuery($query);

        return (array) $db->loadObjectList();
    }
}
