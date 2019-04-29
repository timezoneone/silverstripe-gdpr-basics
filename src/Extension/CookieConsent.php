<?php

namespace TimeZoneOne\GDPR\Extension;

use SilverStripe\Core\Extension;
use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\View\ArrayData;
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

            $cookieConsentPrompt = new ArrayData([
                'DomainName'=> $_SERVER['HTTP_HOST'],
                'CookieConsentDescription' => $this->siteConfig->CookieConsentDescription ? addslashes($this->siteConfig->CookieConsentDescription) : 'This website uses cookies',
                'CookieConsentAgreeButtonLabel' => $this->siteConfig->CookieConsentAgreeButtonLabel ? addslashes($this->siteConfig->CookieConsentAgreeButtonLabel) : 'Accept',
                'CookieConsentDeclineButtonLabel' => $this->siteConfig->CookieConsentDeclineButtonLabel ? addslashes($this->siteConfig->CookieConsentDeclineButtonLabel) : 'Decline'
            ]);

            Requirements::insertHeadTags(
                '<script>
                    var gaHasFired = false;
                    function waitForAllTheThings(fn) { 
                        if (document.attachEvent ? document.readyState === "complete" : document.readyState !== "loading"){
                            fn();
                        } else {
                            document.addEventListener(\'DOMContentLoaded\', fn);
                        }
                    }
                </script>'
            );

            if($this->siteConfig->GTMCode){
                Requirements::insertHeadTags(
                    "<script>
                    document.addEventListener('CookieConsentGranted', function(){
                        if(!gaHasFired){
                            (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                            new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
                            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
                            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
                            })(window,document,'script','dataLayer','" . $this->siteConfig->GTMCode . "');
                            gaHasFired = true;
                        }
                    });
                    </script>"
                );
            }

            if($this->siteConfig->GACode){
                Requirements::insertHeadTags(
                    "<script>
                    document.addEventListener('CookieConsentDenied', function(){
                        if(!gaHasFired){
                            var head = document.head;
                            var script = document.createElement('script');
                            script.type = 'text/javascript';
                            script.src = 'https://www.googletagmanager.com/gtag/js?id=".$this->siteConfig->GACode."';
                            head.appendChild(script);

                            window.dataLayer = window.dataLayer || [];
                            function gtag(){dataLayer.push(arguments);}
                            gtag('js', new Date());
                            gtag('config', '".$this->siteConfig->GACode."', { 'anonymize_ip': true });
                            gaHasFired = true;
                        }
                    });
                    </script>"
                );
            }

            Requirements::customScript($cookieConsentPrompt->renderWith('cookieConsentPrompt'));

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
