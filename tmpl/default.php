<?php
/**
 * @package      Crowdfunding
 * @subpackage   Modules
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

use \Prism\Money\Money;

// no direct access
defined('_JEXEC') or die;

/**
 * @var Joomla\Registry\Registry                     $params
 * @var Joomla\Registry\Registry                     $componentParams
 * @var \Prism\Money\Formatter\IntlDecimalFormatter  $moneyFormatter
 * @var array                                        $projects
 * @var Prism\Integration\Profiles\ProfilesInterface $socialProfiles
 * @var string                                       $imagesDirectory
 * @var int                                          $titleLength
 * @var int                                          $descriptionLength
 * @var bool                                         $displayCreator
 * @var bool                                         $displayDescription
 * @var bool                                         $displayInfo
 * @var bool                                         $displayReadon
 * @var bool                                         $displaySeeProjects
 * @var string                                       $startingDate
 * @var string                                       $endingDate
 * @var string                                       $dateFormat
 */

$doc = JFactory::getDocument();
$doc->addStyleSheet('modules/mod_crowdfundingcampaigns/css/style.css');
?>
<div class="cf-modcampaigns<?php echo $moduleclassSfx; ?>">
    <div class="row">
        <div class="col-md-12">
            <?php foreach ($projects as $project) {
                $title       = JHtmlString::truncate($project->title, $titleLength);
                $description = JHtmlString::truncate($project->short_desc, $descriptionLength);

                // Route project link
                $projectLink = JRoute::_(CrowdfundingHelperRoute::getDetailsRoute($project->slug, $project->catslug));

                $image = CrowdfundingHelper::getImage($params->get('image_type', 'square'), $project, $componentParams, $imagesDirectory);

                $projectClass = 'cf-v-project-100';
                if (count($image) > 0) {
                    $projectClass = 'cf-v-project-80';
                }

                // Prepare social profile.
                $profileName = '';
                if ($displayCreator) {
                    $socialProfile = (!$socialProfiles) ? null : $socialProfiles->getLink($project->user_id);
                    $profileName   = JHtml::_('crowdfunding.socialProfileLink', $socialProfile, $project->user_name);
                }

                // Prepare information about project funding state.
                if ($displayInfo) {
                    if (!empty($project->funding_days)) {
                        $fundingStartDate     = new Crowdfunding\Date($project->funding_start);
                        $fundingEndDate       = $fundingStartDate->calculateEndDate($project->funding_days);
                        $project->funding_end = $fundingEndDate->format(Prism\Constants::DATE_FORMAT_SQL_DATE);
                    }
                    $startingDate = JHtml::_('crowdfunding.date', $project->funding_start, $dateFormat);
                    $endingDate   = JHtml::_('crowdfunding.date', $project->funding_end, $dateFormat);
                }
                ?>
                <div>
                    <?php if (count($image) > 0) { ?>
                        <div class="cf-v-image">
                            <?php if ($params->get('image_link', 0)) { ?>
                            <a href="<?php echo $projectLink; ?>">
                            <?php } ?>
                                <img src="<?php echo $image['image']; ?>" alt="<?php echo htmlspecialchars($title, ENT_QUOTES); ?>" width="<?php echo $image['width']; ?>" height="<?php echo $image['height']; ?>"/>
                            <?php if ($params->get('image_link', 0)) { ?>
                            </a>
                            <?php } ?>
                        </div>
                    <?php } ?>

                    <div class="<?php echo $projectClass; ?>">
                        <h5>
                            <a href="<?php echo $projectLink; ?>">
                                <?php echo htmlspecialchars($title, ENT_QUOTES); ?>
                            </a>
                        </h5>
                        <?php if ($displayCreator) { ?>
                            <span class="font-xsmall">
                        <?php echo JText::sprintf('MOD_CROWDFUNDINGCAMPAIGNS_BY_S', $profileName); ?>
                    </span>
                        <?php } ?>

                        <?php if ($displayDescription) { ?>
                            <p class="font-small"><?php echo htmlspecialchars($description, ENT_QUOTES); ?>
                                <?php if ($displayReadon) { ?>
                                    <a href="<?php echo $projectLink; ?>"
                                       rel="nofollow"><?php echo JText::_('MOD_CROWDFUNDINGCAMPAIGNS_READON'); ?></a>
                                <?php } ?>
                            </p>
                        <?php } ?>

                        <?php if ($displayInfo) { ?>
                            <p class="font-xsmall bolder"><?php echo JText::sprintf('MOD_CROWDFUNDINGCAMPAIGNS_STARTED_ON', $startingDate, $endingDate); ?></p>
                            <p class="font-xsmall bolder">
                                <?php echo JText::sprintf('MOD_CROWDFUNDINGCAMPAIGNS_GOAL', $moneyFormatter->formatCurrency(new Money($project->goal, $currency)), $moneyFormatter->formatCurrency(new Money($project->funded, $currency))); ?>
                            </p>
                        <?php } ?>
                    </div>
                    <div class="clearfix"></div>
                </div>
            <?php } ?>
        </div>
    </div>
    <?php if ($displaySeeProjects) { ?>
        <div class="row">
            <div class="col-md-12 center">
                <a href="<?php echo JRoute::_(CrowdfundingHelperRoute::getDiscoverRoute()); ?>" rel="nofollow"
                   class="btn btn-primary btn-lg"><?php echo JText::_('MOD_CROWDFUNDINGCAMPAIGNS_SEE_PROJECTS'); ?></a>
            </div>
        </div>
    <?php } ?>
</div>