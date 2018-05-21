<?php

class CookieConsent extends Extension {

    public function onBeforeInit(){
        $this->siteConfig = SiteConfig::current_site_config();
    }

    public function onAfterInit(){
        if ($this->siteConfig->GDPRIsActive) {

            $cookieConsentPrompt = new ArrayData([
                'DomainName'=> $_SERVER['HTTP_HOST'],
                'CookieConsentDescription' => $this->siteConfig->CookieConsentDescription ? $this->siteConfig->CookieConsentDescription : 'This website uses cookies',
                'CookieConsentAgreeButtonLabel' => $this->siteConfig->CookieConsentAgreeButtonLabel ? $this->siteConfig->CookieConsentAgreeButtonLabel : 'Accept',
                'CookieConsentDeclineButtonLabel' => $this->siteConfig->CookieConsentDeclineButtonLabel ? $this->siteConfig->CookieConsentDeclineButtonLabel : 'Decline'
            ]);

            Requirements::insertHeadTags(
                '<script>
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
                        (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
                        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
                        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
                        })(window,document,'script','dataLayer','" . $this->siteConfig->GTMCode . "');
                    });
                    </script>"
                );
            }

            if($this->siteConfig->GACode){
                Requirements::insertHeadTags(
                    "<script>
                    document.addEventListener('CookieConsentDenied', function(){
                        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
                        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
                        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
                        })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

                        ga('create', '".$this->siteConfig->GACode."', 'auto');

                        ga('send', 'pageview', { 'anonymizeIp': true });
                    });
                    </script>"
                );
            }

            Requirements::customScript($cookieConsentPrompt->renderWith('cookieConsentPrompt'));

            $colorPalette = new ArrayData([
                'PrimaryColor' => '#D65922'
            ]);

            Requirements::customCSS($colorPalette->renderWith('Style'));

            Requirements::javascript('gdpr-assistant/javascript/cookie-permission.js');

        }
    }

}