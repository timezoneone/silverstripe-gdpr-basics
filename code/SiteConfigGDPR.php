<?php

class SiteConfigGDPR extends DataExtension {

    private static $db = array(
        'GDPRIsActive' => 'Boolean',
        'CookieConsentDescription' => 'HTMLText',
        'CookieConsentAgreeButtonLabel' => 'Varchar(255)',
        'CookieConsentDeclineButtonLabel' => 'Varchar(255)',
        'PrivacyPolicyDisclosure' => 'HTMLText',
        'GTMCode' => 'Varchar(16)',
        'GACode' => 'Varchar(16)',
        'PrimaryColor' => 'Color'
    );

    private static $has_one = array(
        'PrivacyPolicyPage' => 'Page',
        'CookiePolicyPage' => 'Page'
    );


    public function updateCMSFields(FieldList $fields) {

        $fields->addFieldToTab("Root", new Tab('GDPR'));

        if($this->owner->CookiePolicyPage()->Exists()){
            $CookiePolicyPageLink = '<a class="policy-link" href="'.$this->owner->CookiePolicyPage()->Link().'" target="_blank">View Cookie Policy Page</a>';
        }else{
            $CookiePolicyPageLink = '<a class="ss-ui-button cms-content-addpage-button tool-button font-icon-plus ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only cookie-policy-link" href="admin/pages/add/" role="button" aria-disabled="false"><span class="ui-button-text">Add new page</span></a>';
        }


        if($this->owner->PrivacyPolicyPage()->Exists()){
            $PrivacyPolicyPageLink = '<a class="policy-link" href="'.$this->owner->PrivacyPolicyPage()->Link().'" target="_blank">View Privacy Policy Page</a>';
        }else{
            $PrivacyPolicyPageLink = '<a class="ss-ui-button cms-content-addpage-button tool-button font-icon-plus ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only cookie-policy-link" href="admin/pages/add/" role="button" aria-disabled="false"><span class="ui-button-text">Add new page</span></a>';
        }

        $fields->addFieldsToTab('Root.GDPR', array(
            CheckboxField::create('GDPRIsActive','Is Active'),
            DisplayLogicWrapper::create(array(
                TextField::create('GTMCode','Google Tag Manager ID')
                    ->setDescription('Google Tag Manager is only used if user agrees to tracking cookies'),
                TextField::create('GACode','Google Analytics ID')
                    ->setDescription('Anonymized Google Analytics is used when visitor has not accepted cookies.'),
                ToggleCompositeField::create('CookiePolicy','Cookie Policy', array(
                    FieldGroup::create('',array(
                        DropdownField::create(
                            'CookiePolicyPageID', 
                            'Cookie Policy Page', 
                            SiteTree::get()->exclude(array('ClassName'=>'BlogPost', 'ClassName'=>'TOPage'))->map('ID', 'Title')
                            )->setEmptyString(''),
                        LiteralField::create('CookiePolicyPageLink',$CookiePolicyPageLink)
                    )),
                    HTMLEditorField::create(
                        'CookieConsentDescription', 
                        'Cookie Consent Description', 
                        $this->owner->CookieConsentDescription, 
                        'gdpr-basic'
                    )->setRows(4)
                    ->setDescription('A brief description of what you use cookies. Don\'t forget to include a link to your full cookie policy.'),
                    TextField::create('CookieConsentAgreeButtonLabel'),
                    TextField::create('CookieConsentDeclineButtonLabel'),
                    ColorField::create('PrimaryColor', 'Cookie Notice Color')
                )),
                ToggleCompositeField::create('PrivacyPolicy','Privacy Policy', array(
                    FieldGroup::create('',array(
                        DropdownField::create(
                            'PrivacyPolicyPageID', 
                            'Privacy Policy Page', 
                            SiteTree::get()->exclude(array('ClassName'=>'BlogPost', 'ClassName'=>'TOPage'))->map('ID', 'Title')
                            )->setEmptyString(''),
                        LiteralField::create('PrivacyPolicyPageLink',$PrivacyPolicyPageLink)
                    )),
                    HTMLEditorField::create(
                        'PrivacyPolicyDisclosure', 
                        'Basic Privacy Policy Disclosure', 
                        $this->owner->PrivacyPolicyDisclosure, 
                        'gdpr-basic'
                    )->setRows(4)
                    ->setDescription('A brief description of why you collect data. This will appear on all forms. Don\'t forget to include a link to your full privacy policy.')
                ))
            ))->displayIf('GDPRIsActive')->isChecked()->end()
        ));
    }
}