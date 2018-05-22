<?php

class UserFormExtensionGDPR extends Extension {

    public function updateFormFields(&$fields){

        $siteConfig = SiteConfig::current_site_config();

        if ($siteConfig->GDPRIsActive) {

            if($siteConfig->PrivacyPolicyDisclosure || $siteConfig->PrivacyPolicyPage){

                $desc = $siteConfig->PrivacyPolicyDisclosure;
                $privacyPage = $siteConfig->PrivacyPolicyPage();

                $html =  $desc ? $desc : '';

                //if link is included in the Disclosure, we don't need to include it again...
                if($privacyPage && !strpos($desc, $privacyPage->Link())){
                    $html .= '<p><a href="'.$privacyPage->Link().'">'.$privacyPage->Title.'</a></p>';
                }

                $fields->push(LiteralField::create('PrivacyMessage',$html));

            }
        }
    }
}