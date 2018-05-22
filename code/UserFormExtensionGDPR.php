<?php

class UserFormExtensionGDPR extends Extension {

    public function updateFormFields(&$fields){
        
        $siteConfig = SiteConfig::current_site_config();

        if ($siteConfig->GDPRIsActive) {

            //don't insert the Privacy Policy disclosure if the form already has its own..
            $formHasPolicy = (bool)PrivacyPolicyField::get()->filter('ParentID',Controller::curr()->ID)->Count() > 0;

            if(!$formHasPolicy && $siteConfig->PrivacyPolicyDisclosure){

                $fields->push(LiteralField::create('PrivacyMessage',$siteConfig->PrivacyPolicyDisclosure;));

            }
        }
    }
}