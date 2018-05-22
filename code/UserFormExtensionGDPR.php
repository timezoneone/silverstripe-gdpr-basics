<?php

class UserFormExtensionGDPR extends Extension {

    public function updateFormFields(&$fields){

        $siteConfig = SiteConfig::current_site_config();

        if ($siteConfig->GDPRIsActive) {

            if($siteConfig->PrivacyPolicyDescription || $siteConfig->PrivacyPolicyPage){

                $desc = $siteConfig->PrivacyPolicyDescription;
                $privacyPage = $siteConfig->PrivacyPolicyPage();

                $html =  $desc ? $desc : '';

                //if link is included in the description, we don't need to include it again...
                if($privacyPage && !strpos($desc, $privacyPage->Link())){
                    $html .= '<p><a href="'.$privacyPage->Link().'">'.$privacyPage->Title.'</a></p>';
                }

                $fields->push(LiteralField::create('PrivacyMessage',$html));

            }
        }
    }
}