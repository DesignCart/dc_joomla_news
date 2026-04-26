<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_dc_news
 *
 * @copyright   (C) 2026 Design Cart / Paweł Nosko
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

require_once __DIR__ . '/helper.php';

$items = ModDcNewsHelper::getItems($params);

Factory::getApplication()->getDocument()->addStyleSheet(Uri::root(true) . '/modules/mod_dc_news/tmpl/assets/css/site.css');

require ModuleHelper::getLayoutPath('mod_dc_news', $params->get('layout', 'default'));
