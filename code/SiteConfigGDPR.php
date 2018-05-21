<?php

class SiteConfigGDPR extends DataExtension {

    private static $db = array(
        'GDPRIsActive' => 'Boolean',
        'CookieConsentDescription' => 'HTMLText',
        'CookieConsentAgreeButtonLabel' => 'Varchar(255)',
        'CookieConsentDeclineButtonLabel' => 'Varchar(255)',
        'GTMCode' => 'Varchar(16)',
        'GACode' => 'Varchar(16)',
    );

    public function updateCMSFields(FieldList $fields) {

        $fields->addFieldToTab("Root", new Tab('GDPR'));

        $fields->addFieldsToTab('Root.GDPR', array(
            CheckboxField::create('GDPRIsActive','Is Active'),
            TextField::create('GTMCode','Google Tag Manager ID'),
            TextField::create('GACode','Google Analytics ID')->setDescription('Used when visitor has not accepted cookies. Only fill this field if IP address is anonymized in Google Analytics'),
            HtmlEditorField::create('CookieConsentDescription'),
            TextField::create('CookieConsentAgreeButtonLabel'),
            TextField::create('CookieConsentDeclineButtonLabel')
        ));

    }
}