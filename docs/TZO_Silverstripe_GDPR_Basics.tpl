___INFO___

{
  "type": "TAG",
  "id": "cvt_temp_public_id",
  "version": 1,
  "securityGroups": [],
  "displayName": "TZO Silverstripe GDPR Basics",
  "brand": {
    "id": "brand_dummy",
    "displayName": ""
  },
  "description": "GTM Template to use with timezoneone/silverstripe-gdpr-basics",
  "containerContexts": [
    "WEB"
  ]
}


___TEMPLATE_PARAMETERS___

[
  {
    "type": "SELECT",
    "name": "defaultConsent",
    "selectItems": [
      {
        "value": "granted",
        "displayValue": "Granted"
      },
      {
        "value": "denied",
        "displayValue": "Denied"
      }
    ],
    "simpleValueType": true,
    "defaultValue": "denied",
    "displayName": "Default Consent"
  }
]


___SANDBOXED_JS_FOR_WEB_TEMPLATE___

const log = require('logToConsole');
const setDefaultConsentState = require('setDefaultConsentState');
const updateConsentState = require('updateConsentState');
const getCookieValues = require('getCookieValues');
const callInWindow = require('callInWindow');
const COOKIE_NAME = 'cookieConsent';

/**
 * Accepts 'granted' or 'denied'
 */
const onUpdateUserConsent = (consent) => {
  log('onUpdateUserConsent');
  log(consent);

  if (consent !== 'granted') {

    updateConsentState({
      ad_storage: 'denied',
      analytics_storage: 'denied'
    });

  } else {

    updateConsentState({
      ad_storage: 'granted',
      analytics_storage: 'granted'
    });

  }

};

/**
* Checks whether or not user consent is granted
*/
const checkCookieConsent = () => getCookieValues(COOKIE_NAME)[0] === 'granted';

/*
 * Executes the default command, sets the developer ID,
 * and sets up the consent update callback
 */
const main = (data) => {
  log('checkCookieConsent');
  log(getCookieValues(COOKIE_NAME)[0]);
  log(checkCookieConsent());
  log(!checkCookieConsent());
  
  // Set default consent state
  setDefaultConsentState({
    ad_storage: data.defaultConsent,
    analytics_storage: data.defaultConsent
  });

  // Update consent state based on the cookie value
  if (checkCookieConsent()) {

    onUpdateUserConsent('granted');

  }

  /**
  * Calls the window.updateUserConsent() function on the page
  * and passes in onUpdateUserConsent() as a callback
  */
  callInWindow('addUpdateUserConsentListener', onUpdateUserConsent);

};

main(data);
data.gtmOnSuccess();


___WEB_PERMISSIONS___

[
  {
    "instance": {
      "key": {
        "publicId": "logging",
        "versionId": "1"
      },
      "param": [
        {
          "key": "environments",
          "value": {
            "type": 1,
            "string": "debug"
          }
        }
      ]
    },
    "isRequired": true
  },
  {
    "instance": {
      "key": {
        "publicId": "access_globals",
        "versionId": "1"
      },
      "param": [
        {
          "key": "keys",
          "value": {
            "type": 2,
            "listItem": [
              {
                "type": 3,
                "mapKey": [
                  {
                    "type": 1,
                    "string": "key"
                  },
                  {
                    "type": 1,
                    "string": "read"
                  },
                  {
                    "type": 1,
                    "string": "write"
                  },
                  {
                    "type": 1,
                    "string": "execute"
                  }
                ],
                "mapValue": [
                  {
                    "type": 1,
                    "string": "addUpdateUserConsentListener"
                  },
                  {
                    "type": 8,
                    "boolean": false
                  },
                  {
                    "type": 8,
                    "boolean": false
                  },
                  {
                    "type": 8,
                    "boolean": true
                  }
                ]
              }
            ]
          }
        }
      ]
    },
    "clientAnnotations": {
      "isEditedByUser": true
    },
    "isRequired": true
  },
  {
    "instance": {
      "key": {
        "publicId": "get_cookies",
        "versionId": "1"
      },
      "param": [
        {
          "key": "cookieAccess",
          "value": {
            "type": 1,
            "string": "specific"
          }
        },
        {
          "key": "cookieNames",
          "value": {
            "type": 2,
            "listItem": [
              {
                "type": 1,
                "string": "cookieConsent"
              }
            ]
          }
        }
      ]
    },
    "clientAnnotations": {
      "isEditedByUser": true
    },
    "isRequired": true
  },
  {
    "instance": {
      "key": {
        "publicId": "access_consent",
        "versionId": "1"
      },
      "param": [
        {
          "key": "consentTypes",
          "value": {
            "type": 2,
            "listItem": [
              {
                "type": 3,
                "mapKey": [
                  {
                    "type": 1,
                    "string": "consentType"
                  },
                  {
                    "type": 1,
                    "string": "read"
                  },
                  {
                    "type": 1,
                    "string": "write"
                  }
                ],
                "mapValue": [
                  {
                    "type": 1,
                    "string": "ad_storage"
                  },
                  {
                    "type": 8,
                    "boolean": false
                  },
                  {
                    "type": 8,
                    "boolean": true
                  }
                ]
              },
              {
                "type": 3,
                "mapKey": [
                  {
                    "type": 1,
                    "string": "consentType"
                  },
                  {
                    "type": 1,
                    "string": "read"
                  },
                  {
                    "type": 1,
                    "string": "write"
                  }
                ],
                "mapValue": [
                  {
                    "type": 1,
                    "string": "analytics_storage"
                  },
                  {
                    "type": 8,
                    "boolean": false
                  },
                  {
                    "type": 8,
                    "boolean": true
                  }
                ]
              }
            ]
          }
        }
      ]
    },
    "clientAnnotations": {
      "isEditedByUser": true
    },
    "isRequired": true
  }
]


___TESTS___

scenarios: []


___NOTES___

Created on 22/07/2022, 10:02:42


