window.BaseHref = "$DomainName", 
window.cookieConsentPrompt = '<div id="cookie-consent">' +
    '$CookieConsentDescriptionJS.RAW' +
    '<div class="cookie-consent-buttons">' +
        '<a class="button" href="#" id="sweetas">' +
            '$CookieConsentAgreeButtonLabel' +
        '</a><a href="#" id="yeahnah">' +
            '$CookieConsentDeclineButtonLabel' +
        '</a>' +
    '</div>' +
'</div>' +
'<a id="gotCookies" href="#" onclick="document.getElementById(\\'cookie-consent\\').classList.toggle(\\'open\\'); return false;"><img src="resources/vendor/timezoneone/silverstripe-gdpr-basics/client/images/cookie.svg" height="35px" width="35px" alt="" /></a>';
