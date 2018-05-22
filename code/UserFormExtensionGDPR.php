<?php

class UserFormExtensionGDPR extends Extension {

    public function updateFormFields(&$fields){

        $siteConfig = SiteConfig::current_site_config();

        if ($siteConfig->GDPRIsActive) {

            if($siteConfig->PrivacyPolicyDescription || $siteConfig->PrivacyPolicyPage){

                $html = $siteConfig->PrivacyPolicyDescription ? $siteConfig->PrivacyPolicyDescription : '';
                $html .= $siteConfig->PrivacyPolicyPage() ? '<p><a href="'.$siteConfig->PrivacyPolicyPage()->Link().'">'.$siteConfig->PrivacyPolicyPage()->Title.'</a></p>' : '';

                $fields->push(LiteralField::create('PrivacyMessage',$html));

            }
        }
    }
}