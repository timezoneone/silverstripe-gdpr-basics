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
        $config = $this->siteConfig;
        if (
            SiteConfigGDPR::is_enable_for_request() &&
            ($config->GTMCode || $config->GACode)
        ) {
            $tagManagerId = $config->GTMCode;
            if ($tagManagerId) {
                Requirements::insertHeadTags(
                    $this->renderGoogleTagManagerScriptTag($tagManagerId)
                );
            }

            $analyticsId = $config->GACode;
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

    private function renderGoogleTagManagerScriptTag($tagManagerId)
    {
        return HTML::createTag('script', [
            'async' => true,
            'src' => "https://www.googletagmanager.com/gtag/js?id={$tagManagerId}",
        ]);
    }

    public function renderGoogleAnalyticsScriptTag($analyticsId)
    {
        $content =
            // https://developers.google.com/analytics/devguides/collection/analyticsjs#alternative_async_tag
            /** @lang JavaScript */
            "window.ga=window.ga||function(){(ga.q=ga.q||[]).push(arguments)};ga.l=+new Date;
            ga('create', '{$analyticsId}', 'auto');
            ga('send', 'pageview');";

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
        $content =
            /** @lang JavaScript */
            "window.gaConf = {
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
                }";

        return HTML::createTag(
            'script',
            ['type' => 'application/javascript'],
            $content
        );
    }
}
