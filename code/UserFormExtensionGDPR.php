<?php

class UserFormExtensionGDPR extends Extension {

    public function updateFormFields(&$fields){

        $siteConfig = SiteConfig::current_site_config();
        if ($siteConfig->GDPRIsActive && $siteConfig->PrivacyPolicyDescription) {
            $fields->push(LiteralField::create('PrivacyMessage',$siteConfig->PrivacyPolicyDescription));
        }

    }
}