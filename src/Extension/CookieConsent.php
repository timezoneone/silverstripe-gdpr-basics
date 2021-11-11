<?php

namespace TimeZoneOne\GDPR\Extension;

use SilverStripe\View\HTML;
use SilverStripe\Core\Extension;
use SilverStripe\View\ArrayData;
use SilverStripe\View\Requirements;
use SilverStripe\Control\Controller;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\SiteConfig\SiteConfig;

class CookieConsent extends Extension
{
    public function onBeforeInit()
    {
        $this->siteConfig = SiteConfig::current_site_config();
    }

    public function getGtmId()
    {
        return $this->siteConfig->GTMCode;
    }

    public function getGaId()
    {
        return $this->siteConfig->GACode;
    }

    public function onAfterInit()
    {
        // Don't run in nested Controller
        if ($this->owner !== Controller::curr()) {
            return;
        }

        $config = $this->siteConfig;
        $tagManagerId = $this->getGtmId();
        $analyticsId = $this->getGaId();

        if (!$config->GDPRIsActive) {
            if ($tagManagerId) {
                Requirements::insertHeadTags(
                    $this->renderGoogleTagManagerScriptTag($tagManagerId)
                );
            }

            return;
        }

        if (SiteConfigGDPR::is_enable_for_request()
        && ($tagManagerId || $analyticsId)) {
            if ($tagManagerId) {
                Requirements::insertHeadTags(
                    $this->renderGoogleTagManagerScriptTag($tagManagerId)
                );
            }

            if ($analyticsId) {
                Requirements::insertHeadTags(
                    $this->renderGoogleAnalyticsScriptTag($analyticsId)
                );
            }

            // our custom tag manager loader
            Requirements::insertHeadTags(
                $this->renderGoogleLoaderScriptTag($tagManagerId, $analyticsId)
            );

            // prompt
            Requirements::customScript($this->renderCookieConsentPrompt());
            // some styling
            Requirements::customCSS($this->renderStyle());
            // our minified js
            Requirements::javascript(
                'timezoneone/silverstripe-gdpr-basics: client/dist/cookie-permission.min.js'
            );
        }
    }

    public function gtmNoscript()
    {
        $tagManagerId = $this->getGtmId();

        $noscript = $this->renderGoogleTagManagerNoscriptTag($tagManagerId);

        return DBField::create_field('HTMLText', $noscript);
    }

    public function GDPR()
    {
        return SiteConfigGDPR::is_enable_for_request();
    }

    private function renderCookieConsentPrompt()
    {
        $CookieConsentDescription = $this->siteConfig->CookieConsentDescription
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

        return (new ArrayData([
            'DomainName' => $_SERVER['HTTP_HOST'],
            'CookieConsentDescription' => $CookieConsentDescription,
            'CookieConsentDescriptionJS' => $CookieConsentDescriptionJS,
            'CookieConsentAgreeButtonLabel' => $CookieConsentAgreeButtonLabel,
            'CookieConsentDeclineButtonLabel' => $CookieConsentDeclineButtonLabel,
        ]))->renderWith('cookieConsentPrompt');
    }

    private function renderStyle()
    {
        $primaryColor = $this->siteConfig->PrimaryColor
            ? "#{$this->siteConfig->PrimaryColor}"
            : '#D65922';

        return (new ArrayData([
            'PrimaryColor' => $primaryColor,
        ]))->renderWith('Style');
    }

    private function renderGoogleTagManagerScriptTag($gtmId)
    {
        $content = <<<JS
            (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
            new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
            })(window,document,'script','dataLayer', '$gtmId');
        JS;

        return HTML::createTag('script', [], $content);
    }

    private function renderGoogleTagManagerNoscriptTag($gtmId)
    {
        $iframe = HTML::createTag(
            'iframe',
            [
                "src" => "https://www.googletagmanager.com/ns.html?id=$gtmId",
                "height" => 0,
                "width" => 0,
                "style" => 'display:none;visibility:hidden'
            ]
        );

        return HTML::createTag('noscript', [], $iframe);
    }

    public function renderGoogleAnalyticsScriptTag($analyticsId)
    {
        // https://developers.google.com/analytics/devguides/collection/analyticsjs#alternative_async_tag
        $content = <<<JS
            window.ga=window.ga||function(){(ga.q=ga.q||[]).push(arguments)};ga.l=+new Date;
            ga('create', '{$analyticsId}', 'auto');
            ga('send', 'pageview');
        JS;

        return HTML::createTag(
            'script',
            [
                'type' => 'application/javascript',
                'src' => 'https://www.google-analytics.com/analytics.js',
                'async' => true,
            ],
            $content
        );
    }

    private function renderGoogleLoaderScriptTag($tagManagerId, $analyticsId)
    {
        $content = <<<JS
            window.gaConf = {
                gaHasFired: false,
                tagManagerId: '{$tagManagerId}',
                analyticsId: '{$analyticsId}'
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
        JS;

        return HTML::createTag(
            'script',
            [
                'type' => 'application/javascript'
            ],
            $content
        );
    }
}
