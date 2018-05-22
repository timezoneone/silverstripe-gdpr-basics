<?php

class UserFormExtensionGDPR extends Extension {

    public function updateFormFields(&$fields){
        
        $siteConfig = SiteConfig::current_site_config();

        if ($siteConfig->GDPRIsActive) {

            //don't insert the Privacy Policy disclosure if the form already has its own..
            $formHasPolicy = (bool)PrivacyPolicyField::get()->filter('ParentID',Controller::curr()->ID)->Count() > 0;

            if(!$formHasPolicy && ($siteConfig->PrivacyPolicyDisclosure || $siteConfig->PrivacyPolicyPage)){

                $desc = $siteConfig->PrivacyPolicyDisclosure;
                $privacyPage = $siteConfig->PrivacyPolicyPage();

                $html =  $desc ? $desc : '';

                //if link to privacy policy is not included in the Disclosure, add it...
                if($privacyPage && !strpos($desc, $privacyPage->Link())){
                    $html .= '<p><a href="'.$privacyPage->Link().'">'.$privacyPage->Title.'</a></p>';
                }

                $fields->push(LiteralField::create('PrivacyMessage',$html));

            }
        }
    }
}