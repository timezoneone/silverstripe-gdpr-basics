window.dataLayer = window.dataLayer || [];

function gtag() {
  dataLayer.push(arguments);
}

function analyticsConsentGranted() {
  gtag('consent', 'update', {
    ad_storage: 'granted',
    analytics_storage: 'granted'
  });
}

gtag('js', new Date());
gtag('config', window.ga.gaCode);


//CookieConsentGranted Event
let CookieConsentGranted;
if (document.createEvent) {
  CookieConsentGranted = document.createEvent('HTMLEvents');
  CookieConsentGranted.initEvent('CookieConsentGranted', true, true);
} else {
  CookieConsentGranted = document.createEventObject();
  CookieConsentGranted.eventType = 'CookieConsentGranted';
}
CookieConsentGranted.eventName = 'CookieConsentGranted';

//CookieConsentDenied Event
let CookieConsentDenied;
if (document.createEvent) {
  CookieConsentDenied = document.createEvent('HTMLEvents');
  CookieConsentDenied.initEvent('CookieConsentDenied', true, true);
} else {
  CookieConsentDenied = document.createEventObject();
  CookieConsentDenied.eventType = 'CookieConsentDenied';
}
CookieConsentDenied.eventName = 'CookieConsentDenied';

function getCookie(cname) {
  let name = cname + '=';
  let ca = document.cookie.split(';');

  for (let i = 0; i < ca.length; i++) {
    let c = ca[i].trim();
    if (c.indexOf(name) == 0) {
      return c.substring(name.length, c.length);
    }
  }

  return '';
}

function setCookie(name, value, days) {
  const today = new Date();
  const expire = new Date();

  if (days == null || days == 0) {
    days = 1;
  }
  expire.setTime(today.getTime() + 3600000 * 24 * days);

  document.cookie =
    `${name}=${escape(value)};` +
    `expires=${expire.toGMTString()};` +
    'path=/;' +
    `domain=${window.BaseHref}`;
}

function checkCookieConsent() {
  return getCookie('cookieConsent') === 'granted';
}

function getCookieConsent() {
  const cookieConsentPrompt = document
    .createRange()
    .createContextualFragment(window.cookieConsentPrompt);

  document.body.appendChild(cookieConsentPrompt);

  const permissionPrompt = document.getElementById('cookie-consent'),
    agreeButton = document.getElementById('sweetas'),
    declineButton = document.getElementById('yeahnah');

  //check for cookieConsent cookie
  window.cookieConsent = getCookie('cookieConsent');

  if (window.cookieConsent === '') {
    gtag('consent', 'default', {
      ad_storage: 'denied',
      analytics_storage: 'denied',
    });

    permissionPrompt.classList.add('open');
  }

  let eventToFire;
  if (checkCookieConsent()) {
    eventToFire = CookieConsentGranted;
  } else {
    eventToFire = CookieConsentDenied;
  }

  if (document.createEvent) {
    document.dispatchEvent(eventToFire);
  } else {
    document.fireEvent('on' + event.eventType, eventToFire);
  }

  // if user clicks agrees set a cookieConsent cookie
  // set cookie to read "granted"...
  agreeButton.addEventListener('click', function (e) {
    e.preventDefault();

    // if permission hasn't previously been granted
    // fire the 'CookieConsentGranted' event...
    if (!checkCookieConsent()) {
      if (document.createEvent) {
        document.dispatchEvent(CookieConsentGranted);
      } else {
        document.fireEvent('on' + event.eventType, CookieConsentGranted);
      }
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
      if (document.createEvent) {
        document.dispatchEvent(CookieConsentDenied);
      } else {
        document.fireEvent('on' + event.eventType, CookieConsentDenied);
      }
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

  analyticsConsentGranted();
  window.ga.gaHasFired = true;
});

document.addEventListener('CookieConsentDenied', function () {
  document.body.classList.add('CookieConsentDenied');
  document.body.classList.remove('CookieConsentGranted');
  window.ga.gaHasFired = true;
});
