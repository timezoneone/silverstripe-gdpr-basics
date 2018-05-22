<?php

class SiteConfigGDPR extends DataExtension {

    private static $db = array(
        'GDPRIsActive' => 'Boolean',
        'DeleteUserformSubmissionsAfter' => 'Enum("none, 1, 2, 3, 6, 12, 24, 36")', // months
        'CookieConsentDescription' => 'HTMLText',
        'CookieConsentAgreeButtonLabel' => 'Varchar(255)',
        'CookieConsentDeclineButtonLabel' => 'Varchar(255)',
        'GTMCode' => 'Varchar(16)',
        'GACode' => 'Varchar(16)',
        'PrimaryColor' => 'Color',
    );

    public function updateCMSFields(FieldList $fields) {
        $fields->addFieldsToTab('Root.GDPR', array(
            CheckboxField::create('GDPRIsActive','Is Active'),
            DisplayLogicWrapper::create(
                array(
                    DropdownField::create(
                        'DeleteUserformSubmissionsAfter',
                        'Number of months to retain userforms data',
                        array(
                            'none' => 'Do not automatically clear',
                            1 => '1 month',
                            2 => '2 months',
                            3 => '3 months',
                            6 => '6 months',
                            12 => '12 months',
                            24 => '24 months',
                            36 => '36 months',
                        )
                    ),
                    TextField::create('GTMCode','Google Tag Manager ID')
                        ->setDescription('Google Tag Manager is only used if user agrees to tracking cookies'),
                    TextField::create('GACode','Google Analytics ID')
                        ->setDescription('Anonymized Google Analytics is used when visitor has not accepted cookies.'),
                    ToggleCompositeField::create('CookiePolicy','Cookie Policy', 
                        array(
                            HTMLEditorField::create(
                                'CookieConsentDescription', 
                                'Cookie Consent Description', 
                                $this->owner->CookieConsentDescription, 
                                'gdpr-basic'
                            )->setRows(4),
                            TextField::create('CookieConsentAgreeButtonLabel'),
                            TextField::create('CookieConsentDeclineButtonLabel'),
                            ColorField::create('PrimaryColor', 'Cookie Notice Color')
                        )
                    )
                )
            )->displayIf('GDPRIsActive')->isChecked()->end()
        ));

    }
}
