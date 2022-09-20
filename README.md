# TZO Silverstripe GDPR Basics

SilverStripe 4 Module for adding a user prompt to request cookie consent.

Once installed, it needs to be activated in *Settings*, under the *GDPR* tab.

When the user changes their consent an event is fired.

If consent has been granted a "CookieConsentGranted" event will fire, if consent is not granted a "CookieConsentDenied" event will fire, so you can add event listeners for these.

**Requires a Consent Initialisation tag to be added to GTM to work without any custom site code.**

----

## Installation Instructions

### Install via Composer

```
composer require timezoneone/silverstripe-gdpr-basics
```

### Add GTM noscript tag to opening `<body>`

```
<body>
<% if $GtmId %>
  $gtmNoscript
<% end_if %>
```

### Add the Consent tag to the Consent Initialisation trigger in GTM

1. Download the [TZO Silverstripe GDPR Basics Template](https://raw.githubusercontent.com/timezoneone/silverstripe-gdpr-basics/master/docs/TZO_Silverstripe_GDPR_Basics.tpl)
1. Create a new *GTM Tag Template*
1. Import the *TZO Silverstripe GDPR Basics* Template
1. Save the *Template*
1. Add a new *GTM Tag*
1. Use the custom *TZO Silverstripe GDPR Basics* *Tag Type*
1. Use a Consent Initialization *Firing Trigger*
