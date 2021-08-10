SilverStripe 4 Module for adding a user prompt to request cookie consent.

Once installed, it needs to be activated in Settings, under the GDPR tab.

When Activated, the silverstripe-essentials GTM tag will be removed, so you'll need to add the GTM container ID and Google Analtics ID to the GDPR tab.

When the page loads, or the user changes their consent, the cookied consent is checked and an event is fired  

If consent has been granted a "CookieConsentGranted" event will fire, if consent is not granted a "CookieConsentDenied" event will fire, so you can add event listeners for these.

