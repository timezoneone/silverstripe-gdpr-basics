<?php



class SiteConfigGDPR extends DataExtension {

    private static $db = array(
        'GDPRIsActive' => 'Boolean',
        'DeleteUserformSubmissionsAfter' => 'Enum("none, 1, 2, 3, 6, 12, 24, 36")', // months
        'CookieConsentDescription' => 'HTMLText',
        'CookieConsentAgreeButtonLabel' => 'Varchar(255)',
        'CookieConsentDeclineButtonLabel' => 'Varchar(255)',
        'PrivacyPolicyDisclosure' => 'HTMLText',
        'GTMCode' => 'Varchar(16)',
        'GACode' => 'Varchar(16)',
        'PrimaryColor' => 'Color',
        'DataControlFormsActive' => 'Boolean(1)',
    );

    private static $has_one = array(
        'PrivacyPolicyPage' => 'Page',
        'CookiePolicyPage' => 'Page',
        'DataProtectionOfficer' => 'Member'
    );

    private static $enabled_cache = null;

    // Two letter continents EU, AS, OC
    private static $restrict_to_continents = array();

    private static $geo_lite_db = 'assets/GeoLite2-City.mmdb';

    public function updateCMSFields(FieldList $fields) {

        $fields->addFieldToTab("Root", new Tab('GDPR'));

        if($this->owner->CookiePolicyPage()->Exists()){
            $CookiePolicyPageLink = '<a class="policy-link" href="'.$this->owner->CookiePolicyPage()->Link().'" target="_blank">View Cookie Policy Page</a>';
        }else{
            $CookiePolicyPageLink = '<a class="ss-ui-button cms-content-addpage-button tool-button font-icon-plus ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only policy-link" href="admin/pages/add/" role="button" aria-disabled="false"><span class="ui-button-text">Add new page</span></a>';
        }

        if($this->owner->PrivacyPolicyPage()->Exists()){
            $PrivacyPolicyPageLink = '<a class="policy-link" href="'.$this->owner->PrivacyPolicyPage()->Link().'" target="_blank">View Privacy Policy Page</a>';
        }else{
            $PrivacyPolicyPageLink = '<a class="ss-ui-button cms-content-addpage-button tool-button font-icon-plus ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only policy-link" href="admin/pages/add/" role="button" aria-disabled="false"><span class="ui-button-text">Add new page</span></a>';
        }

        $fields->addFieldsToTab('Root.GDPR', array(
            CheckboxField::create('GDPRIsActive','Is Active'),
            CheckboxField::create('DataControlFormsActive','Use data control forms')
                ->setDescription('Adds a form for users to make requests for their data. The form will appear at <a href="'.SiteConfigGDPR::siteURL().'/data-control" traget="_blank">'.SiteConfigGDPR::siteURL().'/data-control</a>. If active, include a link to this page in your privacy Policy'),
            DisplayLogicWrapper::create(array(
                DropdownField::create(
                    'DataProtectionOfficerID', 
                    'Data Protection Officer', 
                    Member::get()->filter(array('Groups.Code' => 'Administrators'))->map('ID', 'Email')
                    )->setEmptyString(''),
                TextField::create('GTMCode','Google Tag Manager ID')
                    ->setDescription('Google Tag Manager is only used if user agrees to tracking cookies'),
                TextField::create('GACode','Google Analytics ID')
                    ->setDescription('Anonymized Google Analytics is used when visitor has not accepted cookies.'),
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
                    )),
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

    public static function siteURL(){
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $domainName = $_SERVER['HTTP_HOST'].'/';
        return $protocol.$domainName;
    }

    public static function is_enable_for_request()
    {
        if(!is_null(self::$enabled_cache)) {
            return self::$enabled_cache;
        }

        self::$enabled_cache = false;
        if(SiteConfig::current_site_config()->GDPRIsActive) {
            self::$enabled_cache = true;
            $continents = Config::inst()->get('SiteConfigGDPR', 'restrict_to_continents');
            if(!empty($continents)) {
                self::$enabled_cache = false;
                $controller = Controller::has_curr() ? Controller::curr() : null;
                $db = BASE_PATH . DIRECTORY_SEPARATOR . Config::inst()->get('SiteConfigGDPR', 'geo_lite_db');
                if($controller && file_exists($db)) {
                    $ip = $controller->getRequest()->getIP();

                    $ip = '138.68.129.79'; // uk

                    $reader = new \MaxMind\Db\Reader($db);
                    $record = $reader->get($ip);
                    if($record && is_array($record) && isset($record['continent'])) {
                        self::$enabled_cache = in_array($record['continent']['code'], $continents);
                    }
                }
            }
        }
        return self::$enabled_cache;
    }

}
