<?php


namespace TimeZoneOne\GDPR\Task;

use SilverStripe\Dev\BuildTask;
use SilverStripe\SiteConfig\SiteConfig;


class DeleteUserformSubmissionsTask extends BuildTask
{

    public $title = 'GDPR - Delete user form data';

    public $description = 'Delete user form submissions older than the number of months selected in site settings.';

    public function run($request)
    {
        $months = SiteConfig::current_site_config()->DeleteUserformSubmissionsAfter;
        $isGdprActive = SiteConfig::current_site_config()->GDPRIsActive;
        if (!$isGdprActive) {
            echo '<h3>GDPR is disabled in site settings. No submissions were deleted.</h3>';
            exit();
        }
        if (is_numeric($months)) {
            $dtForSelectedValue = date('Y-m-d H:i:s', strtotime('-'.$months. ' months'));
            $submissionsToBeDeleted = SubmittedForm::get()->filter('Created:LessThan', $dtForSelectedValue);
            if ($countToBeDeleted = $submissionsToBeDeleted->count()) {
                foreach ($submissionsToBeDeleted as $submission) {
                    foreach ($submission->Values() as $value) {
                        $value->delete();
                    }
                    $submission->delete();
                }
                echo '<h3>Deleted '.$countToBeDeleted.' submission(s)</h3>';
            } else {
                echo '<h3>No submissions found before \''.$dtForSelectedValue.'\' to be deleted.</h3>';
            }
        } else {
            echo '<h3>Option \'Do not automatically clear\' is selected in site settings. No submissions were deleted.</h3>';
        }
    }

}
