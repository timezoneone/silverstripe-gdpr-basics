<?php
/**
 * Created by priyashantha@silverstripers.com
 * Date: 5/22/18
 * Time: 2:15 PM
 */

class DeleteUserformSubmissionsTask extends BuildTask {

    public $title = 'Delete user form submission for given a time period.';

    public function run($request)
    {
        $months = SiteConfig::current_site_config()->DeleteUserformSubmissionsAfter;
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