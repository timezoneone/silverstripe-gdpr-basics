window.dataLayer = window.dataLayer || [];

function gtag() {
  window.dataLayer.push(arguments);
}

function setConsentGranted() {
  if (consentListeners.length) {
    consentListeners.forEach(function(callback) {
      callback('granted')
    });
  } else {
    gtag('consent', 'update', {
      ad_storage: 'granted',
      analytics_storage: 'granted'
    });
  }
}

function setConsentDenied() {
  if (consentListeners.length) {
    consentListeners.forEach(function(callback) {
      callback('denied');
    });
  } else {
    gtag('consent', 'update', {
      ad_storage: 'denied',
      analytics_storage: 'denied'
    });
  }
}

// Initialise gtag
gtag('js', new Date());

if (window.gaConf.tagManagerId) {
  gtag('config', window.gaConf.tagManagerId);
}

if (window.gaConf.analyticsId) {
  gtag('config', window.gaConf.analyticsId);
}

//CookieConsentGranted Event
var CookieConsentGranted = new Event(
  'CookieConsentGranted',
  {
    bubbles: true,
    cancelable: true
  }
);

//CookieConsentDenied Event
var CookieConsentDenied = new Event(
  'CookieConsentDenied',
  {
    bubbles: true,
    cancelable: true
  }
);

function getCookie(cname) {
  var name = cname + '=';
  var ca = document.cookie.split(';');

  for (var i = 0; i < ca.length; i++) {
    var c = ca[i].trim();
    if (c.indexOf(name) == 0) {
      return c.substring(name.length, c.length);
    }
  }

  return '';
}

function setCookie(name, value, days) {
  var today = new Date();
  var expire = new Date();

  if (days == null || days == 0) {
    days = 1;
  }
  expire.setTime(today.getTime() + 3600000 * 24 * days);

  document.cookie =
    name + '=' + decodeURIComponent(value) +
    ';expires=' + expire.toGMTString() +
    ';path=/;domain=' + window.BaseHref;
}

function checkCookieConsent() {
  return getCookie('cookieConsent') === 'granted';
}

function getCookieConsent() {
  var cookieConsentPrompt = document
    .createRange()
    .createContextualFragment(window.cookieConsentPrompt);

  document.body.appendChild(cookieConsentPrompt);

  var permissionPrompt = document.getElementById('cookie-consent'),
    agreeButton = document.getElementById('sweetas'),
    declineButton = document.getElementById('yeahnah');

  //check for cookieConsent cookie
  window.cookieConsent = getCookie('cookieConsent');

  if (window.cookieConsent === '') {
    permissionPrompt.classList.add('open');
  }

  // if user clicks agrees set a cookieConsent cookie
  // set cookie to read "granted"...
  agreeButton.addEventListener('click', function (e) {
    e.preventDefault();

    // if permission hasn't previously been granted
    // fire the 'CookieConsentGranted' event...
    if (!checkCookieConsent()) {
      document.dispatchEvent(CookieConsentGranted);
    }

    setCookie('cookieConsent', 'granted', 365, '');
    agreeButton.innerHTML = 'Thank you!';

    setTimeout(function () {
      // update button
      permissionPrompt.classList.remove('open');
      permissionPrompt.setAttribute('aria-hidden', 'true');
      agreeButton.innerHTML = "That's fine";
    }, 500);

    return false;
  });

  //if user clicks "no thanks" set a "donottrack" cookie
  declineButton.addEventListener('click', function (e) {
    e.preventDefault();

    // if permission wasn't already denied,
    // fire the 'CookieConsentDenied' event...
    if (checkCookieConsent()) {
      document.dispatchEvent(CookieConsentDenied);
    }

    setCookie('cookieConsent', 'false', 365, '');
    declineButton.innerHTML = 'You got it!';

    setTimeout(function () {
      // update button
      permissionPrompt.classList.remove('open');
      permissionPrompt.setAttribute('aria-hidden', 'true');
      declineButton.innerHTML = 'No thanks';
    }, 500);

    return false;
  });
}

// function set in TimeZoneOne\GDPR\Extension\CookieConsent::onAfterInit
waitForAllTheThings(getCookieConsent);

document.addEventListener('CookieConsentGranted', function () {
  document.body.classList.add('CookieConsentGranted');
  document.body.classList.remove('CookieConsentDenied');

  setConsentGranted();
  window.gaConf.gaHasFired = true;
});

document.addEventListener('CookieConsentDenied', function () {
  document.body.classList.add('CookieConsentDenied');
  document.body.classList.remove('CookieConsentGranted');

  setConsentDenied();
  window.gaConf.gaHasFired = true;
});
