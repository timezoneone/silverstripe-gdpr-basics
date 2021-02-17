<?php

namespace TimeZoneOne\GDPR\Extension;

use SilverStripe\Core\Extension;
use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\View\ArrayData;
use SilverStripe\View\HTML;
use SilverStripe\View\Requirements;

class CookieConsent extends Extension
{

    public function onBeforeInit()
    {
        $this->siteConfig = SiteConfig::current_site_config();
    }

    public function onAfterInit()
    {
        if (SiteConfigGDPR::is_enable_for_request()) {
            $tagManagerId = $this->siteConfig->GTMCode;

            $CookieConsentDescription = $this->siteConfig
                ->CookieConsentDescription
                ? addslashes($this->siteConfig->CookieConsentDescription)
                : 'This website uses cookies';

            $CookieConsentDescriptionJS = str_replace(
                "\n",
                '',
                $this->siteConfig
                    ->dbObject('CookieConsentDescription')
                    ->forTemplate()
                    ? addslashes(
                    $this->siteConfig
                        ->dbObject('CookieConsentDescription')
                        ->forTemplate()
                )
                    : 'This website uses cookies'
            );

            $CookieConsentAgreeButtonLabel = $this->siteConfig
                ->CookieConsentAgreeButtonLabel
                ? addslashes($this->siteConfig->CookieConsentAgreeButtonLabel)
                : 'Accept';

            $CookieConsentDeclineButtonLabel = $this->siteConfig
                ->CookieConsentDeclineButtonLabel
                ? addslashes($this->siteConfig->CookieConsentDeclineButtonLabel)
                : 'Decline';

            $cookieConsentPrompt = new ArrayData([
                 'DomainName' => $_SERVER['HTTP_HOST'],
                 'CookieConsentDescription' => $CookieConsentDescription,
                 'CookieConsentDescriptionJS' => $CookieConsentDescriptionJS,
                 'CookieConsentAgreeButtonLabel' => $CookieConsentAgreeButtonLabel,
                 'CookieConsentDeclineButtonLabel' => $CookieConsentDeclineButtonLabel,
            ]);

            Requirements::insertHeadTags(
                HTML::createTag('script', [
                    'async' => true,
                    'src' => "https://www.googletagmanager.com/gtag/js?id={$tagManagerId}",
                ])
            );

            Requirements::insertHeadTags(
                "<script>
                    window.ga = {
                        gaHasFired: false,
                        gaCode: '{$tagManagerId}',
                    };
                    
                    function waitForAllTheThings(fn) {
                        var isDocReady = document.attachEvent
                            ? document.readyState === 'complete'
                            : document.readyState !== 'loading';

                        if (isDocReady){
                            fn();
                        } else {
                            document.addEventListener('DOMContentLoaded', fn);
                        }
                    }
                </script>"
            );

            Requirements::customScript(
                $cookieConsentPrompt->renderWith('cookieConsentPrompt')
            );

            $colorPalette = new ArrayData([
                'PrimaryColor' => $this->siteConfig->PrimaryColor ? '#'.$this->siteConfig->PrimaryColor : '#D65922'
            ]);
            Requirements::customCSS($colorPalette->renderWith('Style'));
            Requirements::javascript('timezoneone/silverstripe-gdpr-basics: client/dist/cookie-permission.min.js');
        }
    }

    public function GDPR()
    {
        return SiteConfigGDPR::is_enable_for_request();
    }

}
