//CookieConsentGranted Event
var CookieConsentGranted; 
if (document.createEvent) {
    CookieConsentGranted = document.createEvent("HTMLEvents");
    CookieConsentGranted.initEvent("CookieConsentGranted", true, true);
} else {
    CookieConsentGranted = document.createEventObject();
    CookieConsentGranted.eventType = "CookieConsentGranted";
}
CookieConsentGranted.eventName = "CookieConsentGranted";

//CookieConsentDenied Event
var CookieConsentDenied; 
if (document.createEvent) {
    CookieConsentDenied = document.createEvent("HTMLEvents");
    CookieConsentDenied.initEvent("CookieConsentDenied", true, true);
} else {
    CookieConsentDenied = document.createEventObject();
    CookieConsentDenied.eventType = "CookieConsentDenied";
}
CookieConsentDenied.eventName = "CookieConsentDenied";

function getCookie(cname){
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++){
      var c = ca[i].trim();
      if (c.indexOf(name)==0) return c.substring(name.length,c.length);
    }
    return "";
}

var deleteCookies = function(){
    var pairs = document.cookie.split(";");
    for (var i=0; i<pairs.length; i++){
        var pair = pairs[i].split("=");
        document.cookie = pair[0] + "=''; expires=-1;path=/;domain="+window.BaseHref;
    }
}

function SetCookie(name,value,days) {
    var today = new Date();
    var expire = new Date();
    if (days==null || days==0) days=1;
    expire.setTime(today.getTime() + 3600000*24*days);
    document.cookie = name+"="+escape(value) + ";expires="+expire.toGMTString()+";path=/;domain="+window.BaseHref;
}

function checkCookieConsent(){
    return getCookie('cookieConsent') === 'granted' ? true : false;
}

function getCookieConsent(){

    var cookieConsentPrompt = document.createRange().createContextualFragment(window.cookieConsentPrompt);

    document.body.appendChild(cookieConsentPrompt);

    var permissionPrompt = document.getElementById('cookie-consent'), 
        agreeButton = document.getElementById('sweetas'), 
        declineButton = document.getElementById('yeahnah');

    //check for cookieConsent cookie
    window.cookieConsent = getCookie('cookieConsent');

    if(window.cookieConsent == ''){
        permissionPrompt.classList.add('open');
    }else{
        var eventToFire;
        if(checkCookieConsent()){
            eventToFire = CookieConsentGranted;
        }else{
            eventToFire = CookieConsentDenied;
        }

        if (document.createEvent) {
            document.dispatchEvent(eventToFire);
        } else {
            document.fireEvent("on" + event.eventType, eventToFire);
        }
    }

    //if user clicks agrees set a cookieConsent cookie 
    // set cookie to read "granted"...
    agreeButton.addEventListener('click', function(e){
        e.preventDefault();
        //if permission hasn't previously been granted fire the 'CookieConsentGranted' event...
        if(!checkCookieConsent()){
            if (document.createEvent) {
                document.dispatchEvent(CookieConsentGranted);
            } else {
                document.fireEvent("on" + event.eventType, CookieConsentGranted);
            }
        }
        SetCookie('cookieConsent','granted',365,'');
        agreeButton.innerHTML = 'Thank you!';
        setTimeout(function(){
            permissionPrompt.classList.remove('open');
            permissionPrompt.setAttribute('aria-hidden', 'true');
            agreeButton.innerHTML = 'That\'s fine'
        },500);
        return false;
    }); 

    //if user clicks "no thanks" set a donottrack cookie
    declineButton.addEventListener('click', function(e){
        e.preventDefault();
        //if permission wan't already denied, fire the 'CookieConsentDenied' event...
        if(!checkCookieConsent()){
            if (document.createEvent) {
                document.dispatchEvent(CookieConsentDenied);
            } else {
                document.fireEvent("on" + event.eventType, CookieConsentDenied);
            }
        }
        //delete existing cookies...
        deleteCookies();
        SetCookie('cookieConsent','false',365,'');
        declineButton.innerHTML = 'You got it!';
        setTimeout(function(){
            permissionPrompt.classList.remove('open');
            permissionPrompt.setAttribute('aria-hidden', 'true');
            declineButton.innerHTML = 'No thanks';
        },500);
        return false;
    }); 

}

waitForAllTheThings(getCookieConsent);




