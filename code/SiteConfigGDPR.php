<?php

class SiteConfigGDPR extends DataExtension {

    private static $db = array(
        'GDPRIsActive' => 'Boolean',
        'CookieConsentDescription' => 'HTMLText',
        'CookieConsentAgreeButtonLabel' => 'Varchar(255)',
        'CookieConsentDeclineButtonLabel' => 'Varchar(255)',
        'GTMCode' => 'Varchar(16)',
        'GACode' => 'Varchar(16)',
        'PrimaryColor' => 'Color'
    );

    public function updateCMSFields(FieldList $fields) {

        $fields->addFieldToTab("Root", new Tab('GDPR'));

        $fields->addFieldsToTab('Root.GDPR', array(
            CheckboxField::create('GDPRIsActive','Is Active'),
            DisplayLogicWrapper::create(
                TextField::create('GTMCode','Google Tag Manager ID'),
                TextField::create('GACode','Google Analytics ID')
                    ->setDescription('Anonymized Google Analytics is used when visitor has not accepted cookies.'),
                HTMLEditorField::create(
                    'CookieConsentDescription', 
                    'Cookie Consent Description', 
                    $this->owner->CookieConsentDescription, 
                    'gdpr-basic'
                ),
                TextField::create('CookieConsentAgreeButtonLabel'),
                TextField::create('CookieConsentDeclineButtonLabel'),
                ColorField::create('PrimaryColor')
            )->displayIf('GDPRIsActive')->isChecked()->end()
        ));

    }
}