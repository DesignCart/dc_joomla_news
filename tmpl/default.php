<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_dc_news
 *
 * @copyright   (C) 2026 Design Cart / Paweł Nosko
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

$showTitle = (int) $params->get('show_title', 1) === 1;
$titleTag = strtolower((string) $params->get('title_tag', 'h3'));
$showIntro = (int) $params->get('show_intro', 1) === 1;
$showReadmore = (int) $params->get('show_readmore', 1) === 1;
$readmoreText = trim((string) $params->get('readmore_text', Text::_('MOD_DC_NEWS_READ_MORE')));
$showTags = (int) $params->get('show_tags', 1) === 1;
$showImage = (int) $params->get('show_image', 1) === 1;
$imagePosition = (string) $params->get('image_position', 'top');
$imageWidthValue = (int) $params->get('image_width_value', 38);
$imageWidthUnit = (string) $params->get('image_width_unit', '%');
$gridColumns = max(1, min(6, (int) $params->get('grid_columns', 3)));
$imageShape = (string) $params->get('image_shape', '1:1');

$ratioClassMap = [
    '1:1' => 'mod-dc-news__image-wrap--ratio-1-1',
    '1:2' => 'mod-dc-news__image-wrap--ratio-1-2',
    '2:3' => 'mod-dc-news__image-wrap--ratio-2-3',
    '1:3' => 'mod-dc-news__image-wrap--ratio-1-3',
    '3:1' => 'mod-dc-news__image-wrap--ratio-3-1',
    '3:2' => 'mod-dc-news__image-wrap--ratio-3-2',
    '2:1' => 'mod-dc-news__image-wrap--ratio-2-1',
];

$isCircle = $imageShape === 'circle';
$ratioClass = $ratioClassMap[$imageShape] ?? 'mod-dc-news__image-wrap--ratio-1-1';
$isLeftLayout = $imagePosition === 'left';
$itemLayoutStyle = $isLeftLayout ? 'flex-direction:row !important;' : 'flex-direction:column !important;';

if (!in_array($imageWidthUnit, ['%', 'px'], true)) {
    $imageWidthUnit = '%';
}

if ($imageWidthUnit === '%') {
    $imageWidthValue = max(1, min(100, $imageWidthValue));
} else {
    $imageWidthValue = max(1, min(2000, $imageWidthValue));
}

$imageWidthCssValue = $imageWidthValue . $imageWidthUnit;
$imageLinkInlineStyle = $isLeftLayout ? 'flex:0 0 ' . $imageWidthCssValue . ' !important;max-width:' . $imageWidthCssValue . ' !important;' : '';

$imageWrapInlineStyle = '';

if ($isCircle) {
    $imageWrapInlineStyle = 'aspect-ratio:1/1 !important;border-radius:50% !important;';
} else {
    $imageWrapInlineStyle = 'aspect-ratio:' . htmlspecialchars($imageShape, ENT_QUOTES, 'UTF-8') . ' !important;';
}

$moduleClass = 'mod-dc-news-' . (int) $module->id;

$titleUpper = (int) $params->get('title_uppercase', 0) === 1 ? 'uppercase' : 'none';
$introUpper = (int) $params->get('intro_uppercase', 0) === 1 ? 'uppercase' : 'none';
$tagUpper = (int) $params->get('tag_uppercase', 0) === 1 ? 'uppercase' : 'none';
$buttonUpper = (int) $params->get('button_uppercase', 0) === 1 ? 'uppercase' : 'none';

if (!in_array($titleTag, ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'], true)) {
    $titleTag = 'h3';
}

$styleVars = [
    '--dc-news-columns:' . $gridColumns,
    '--dc-news-item-bg:' . htmlspecialchars((string) $params->get('item_bg_color', '#ffffff'), ENT_QUOTES, 'UTF-8'),
    '--dc-news-title-color:' . htmlspecialchars((string) $params->get('title_color', '#111111'), ENT_QUOTES, 'UTF-8'),
    '--dc-news-title-size:' . (int) $params->get('title_font_size', 20) . 'px',
    '--dc-news-title-weight:' . (int) $params->get('title_font_weight', 700),
    '--dc-news-title-transform:' . $titleUpper,
    '--dc-news-intro-color:' . htmlspecialchars((string) $params->get('intro_color', '#555555'), ENT_QUOTES, 'UTF-8'),
    '--dc-news-intro-size:' . (int) $params->get('intro_font_size', 15) . 'px',
    '--dc-news-intro-weight:' . (int) $params->get('intro_font_weight', 400),
    '--dc-news-intro-transform:' . $introUpper,
    '--dc-news-tag-color:' . htmlspecialchars((string) $params->get('tag_color', '#ffffff'), ENT_QUOTES, 'UTF-8'),
    '--dc-news-tag-size:' . (int) $params->get('tag_font_size', 12) . 'px',
    '--dc-news-tag-weight:' . (int) $params->get('tag_font_weight', 600),
    '--dc-news-tag-transform:' . $tagUpper,
    '--dc-news-tag-bg:' . htmlspecialchars((string) $params->get('tag_bg_color', '#1e73be'), ENT_QUOTES, 'UTF-8'),
    '--dc-news-tag-color-hover:' . htmlspecialchars((string) $params->get('tag_color_hover', '#ffffff'), ENT_QUOTES, 'UTF-8'),
    '--dc-news-tag-bg-hover:' . htmlspecialchars((string) $params->get('tag_bg_color_hover', '#155a91'), ENT_QUOTES, 'UTF-8'),
    '--dc-news-btn-color:' . htmlspecialchars((string) $params->get('button_color', '#ffffff'), ENT_QUOTES, 'UTF-8'),
    '--dc-news-btn-size:' . (int) $params->get('button_font_size', 14) . 'px',
    '--dc-news-btn-weight:' . (int) $params->get('button_font_weight', 700),
    '--dc-news-btn-transform:' . $buttonUpper,
    '--dc-news-btn-bg:' . htmlspecialchars((string) $params->get('button_bg_color', '#000000'), ENT_QUOTES, 'UTF-8'),
    '--dc-news-btn-color-hover:' . htmlspecialchars((string) $params->get('button_color_hover', '#ffffff'), ENT_QUOTES, 'UTF-8'),
    '--dc-news-btn-bg-hover:' . htmlspecialchars((string) $params->get('button_bg_color_hover', '#333333'), ENT_QUOTES, 'UTF-8'),
];
?>
<div class="mod-dc-news <?php echo $moduleClass; ?>" style="<?php echo implode(';', $styleVars); ?>">
    <div class="mod-dc-news__grid mod-dc-news__grid--img-<?php echo htmlspecialchars($imagePosition, ENT_QUOTES, 'UTF-8'); ?>">
        <?php foreach ($items as $item) : ?>
            <article class="mod-dc-news__item mod-dc-news__item--img-<?php echo htmlspecialchars($imagePosition, ENT_QUOTES, 'UTF-8'); ?>" style="<?php echo $itemLayoutStyle; ?>">
                <?php if ($showImage && !empty($item->image)) : ?>
                    <a class="mod-dc-news__image-link" href="<?php echo Route::_($item->link); ?>" style="<?php echo $imageLinkInlineStyle; ?>">
                        <span class="mod-dc-news__image-wrap <?php echo $isCircle ? 'mod-dc-news__image-wrap--circle' : $ratioClass; ?>" style="<?php echo $imageWrapInlineStyle; ?>">
                            <?php echo HTMLHelper::_('image', $item->image, htmlspecialchars($item->title, ENT_QUOTES, 'UTF-8'), ['class' => 'mod-dc-news__image', 'loading' => 'lazy']); ?>
                        </span>
                    </a>
                <?php endif; ?>

                <div class="mod-dc-news__content">
                    <?php if ($showTitle) : ?>
                        <<?php echo $titleTag; ?> class="mod-dc-news__title">
                            <a href="<?php echo Route::_($item->link); ?>">
                                <?php echo htmlspecialchars($item->title, ENT_QUOTES, 'UTF-8'); ?>
                            </a>
                        </<?php echo $titleTag; ?>>
                    <?php endif; ?>

                    <?php if ($showIntro && !empty($item->introtext)) : ?>
                        <div class="mod-dc-news__intro">
                            <?php echo $item->introtext; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($showTags && !empty($item->tags)) : ?>
                        <div class="mod-dc-news__tags">
                            <?php foreach ($item->tags as $tag) : ?>
                                <span class="mod-dc-news__tag">
                                    <?php echo htmlspecialchars($tag->title, ENT_QUOTES, 'UTF-8'); ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($showReadmore) : ?>
                        <a class="mod-dc-news__button" href="<?php echo Route::_($item->link); ?>">
                            <?php echo htmlspecialchars($readmoreText, ENT_QUOTES, 'UTF-8'); ?>
                        </a>
                    <?php endif; ?>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</div>
