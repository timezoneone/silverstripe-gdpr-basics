<?php


namespace TimeZoneOne\GDPR\Extension;

use SilverStripe\Control\Controller;
use SilverStripe\Core\Extension;
use SilverStripe\Forms\LiteralField;
use SilverStripe\SiteConfig\SiteConfig;
use TimeZoneOne\GDPR\Model\PrivacyPolicyField;
use SilverStripe\View\Parsers\ShortcodeParser;

class UserFormExtensionGDPR extends Extension
{

    public function updateFormFields(&$fields)
    {

        $siteConfig = SiteConfig::current_site_config();

        if (SiteConfigGDPR::is_enable_for_request()) {
            //don't insert the Privacy Policy disclosure if the form already has its own..
            $formHasPolicy = (bool)PrivacyPolicyField::get()->filter('ParentID',Controller::curr()->ID)->Count() > 0;
            if(!$formHasPolicy && $siteConfig->PrivacyPolicyDisclosure){
                $fields->push(LiteralField::create('PrivacyMessage', ShortcodeParser::get_active()->parse($siteConfig->PrivacyPolicyDisclosure)));
            }
        }
    }
}
