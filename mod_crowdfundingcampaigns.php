<?php
/**
 * @package      Crowdfunding
 * @subpackage   Modules
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

use Crowdfunding\Facade\Joomla as JoomlaFacade;

// no direct access
defined('_JEXEC') or die;

$moduleclassSfx = htmlspecialchars($params->get('moduleclass_sfx'));

jimport('Prism.init');
jimport('Crowdfunding.init');

$limitResults = ((int)$params->get('results_limit', 5) <= 0) ? 5 : (int)$params->get('results_limit', 5);
$period       = ((int)$params->get('period', 3) <= 0) ? 3 : (int)$params->get('period', 3);

$options = array(
    'limit'   => $limitResults,
    'published' => Prism\Constants::PUBLISHED,
    'approved' => Prism\Constants::APPROVED,
    'order_column' => 'a.title',
    'order_direction' => 'ASC',
    'period' => $period
);

switch ($params->get('statistic', 'successfully_funded')) {
    case 'unsuccessfully_funded':
        $projects = new Crowdfunding\Project\Statistic\UnsuccessfullyFunded(JFactory::getDbo());
        break;

    case 'ending_soon':
        $projects = new Crowdfunding\Project\Statistic\EndingSoon(JFactory::getDbo());
        break;

    default:
        $projects = new Crowdfunding\Project\Statistic\SuccessfullyFunded(JFactory::getDbo());
        break;
}

$projects->load($options);
$projects = $projects->toObjects();

$componentParams = JComponentHelper::getParams('com_crowdfunding');
/** @var  $componentParams Joomla\Registry\Registry */

// Get options
$displayInfo        = $params->get('show_info', Prism\Constants::DISPLAY);
$displayDescription = $params->get('show_description', $componentParams->get('show_description', Prism\Constants::DISPLAY));
$displayCreator     = $params->get('show_author', $componentParams->get('show_author', Prism\Constants::DISPLAY));
$displayReadon      = $params->get('show_readon', Prism\Constants::DO_NOT_DISPLAY);
$displaySeeProjects = $params->get('show_see_projects', Prism\Constants::DO_NOT_DISPLAY);
$titleLength        = $params->get('title_length', $componentParams->get('title_length'));
$descriptionLength  = $params->get('description_length', $componentParams->get('description_length'));

$imagesDirectory    = $componentParams->get('images_directory', 'images/crowdfunding');
$dateFormat         = $componentParams->get('date_format_views', JText::_('DATE_FORMAT_LC3'));

if ($displayInfo) {
    $currency        = JoomlaFacade::getCurrency();
    $moneyFormatter  = JoomlaFacade::getMoneyFormatter();
}

// Display user social profile ( integrate ).
if ($displayCreator) {
    $socialProfiles = null;

    // Get a social platform for integration
    $socialPlatform = $componentParams->get('integration_social_platform');
    if ($socialPlatform !== null && $socialPlatform !== '') {
        $usersIds       = Prism\Utilities\ArrayHelper::getIds($projects, 'user_id');
        $socialProfiles = CrowdfundingHelper::prepareIntegration($socialPlatform, $usersIds);
    }
}

if (count($projects) > 0) {
    require JModuleHelper::getLayoutPath('mod_crowdfundingcampaigns', $params->get('layout', 'default'));
}
