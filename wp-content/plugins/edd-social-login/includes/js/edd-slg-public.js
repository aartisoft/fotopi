jQuery(document).ready(function ($) {


	if ($(".edd-slg-social-login-googleplus").length > 0) {
		var auth2;
		var googleUser = {};
		if (EDDSlg.google_client_id != "") {
			var edd_slg_start_app = function () {
				gapi.load('auth2', function () {
					auth2 = gapi.auth2.init({
						client_id: EDDSlg.google_client_id,
						cookiepolicy: 'single_host_origin',

					});
					$('.edd-slg-social-login-googleplus').each(function (i, j) {
						edd_slg_google_signin(j);
					});
				});
			};
			edd_slg_start_app();
		}else{
			var object = $('.edd-slg-social-login-googleplus');
			var errorel = $(object).parents('.edd-slg-social-container').find('.edd-slg-login-error');

			errorel.hide();
			errorel.html('');

			if (EDDSlg.gperror == '1') {
				errorel.show();
				errorel.html(EDDSlg.gperrormsg);
				return false;
			}
		}
		function edd_slg_google_signin(element) {

			var object = $(element);
			var errorel = $(object).parents('.edd-slg-social-container').find('.edd-slg-login-error');

			errorel.hide();
			errorel.html('');

			if (EDDSlg.gperror == '1') {
				errorel.show();
				errorel.html(EDDSlg.gperrormsg);
				return false;
			}


			auth2.attachClickHandler(element, {},
				function (googleUser) {

					var profile = googleUser.getBasicProfile();

					var gp_userdata = {
						given_name: profile.getGivenName(),
						family_name: profile.getFamilyName(),
						name: profile.getName(),
						email: profile.getEmail(),
						id: profile.getId(),
						img_url: profile.getImageUrl(),
					};

					var edd_slg_post_data = {
						action: 'edd_slg_social_login',
						type: 'googleplus',
						gp_userdata: gp_userdata,

					};

					$.ajax({
						url: EDDSlg.ajaxurl,
						type: 'post',
						data: edd_slg_post_data,
						success: function (edd_slg_google_ajax_response) {
							if (edd_slg_google_ajax_response) {
								edd_slg_social_connect('googleplus', object);
							}
						}
					});

				}, function (error) {
					if (error.error == "popup_closed_by_user") {
						// errorel.show();
						// errorel.html(EDDSlg.googlesingincancelmsg);
						return false;
					} else {
						errorel.show();
						errorel.html(error.error);
						return false;
					}

				});
		}

	}



	if (document.URL.indexOf('code=') != -1 && navigator.userAgent.match('CriOS') && EDDSlg.fbappid != '') {
		facebookTimer = setInterval(function () {
			if (typeof FB != "undefined") {
				FB.getLoginStatus(function (response) {
					if (response.status === 'connected') {
						var object = $('a.edd-slg-social-login-facebook');
						edd_slg_social_connect('facebook', object);
						clearInterval(facebookTimer);
					}
				}, true);
			}
		}, 300);
	}

	// login with facebook
	

	// login with google+
	

	// login with linkedin
	$(document).on('click', 'a.edd-slg-social-login-linkedin', function () {

		var object = $(this);
		var errorel = $(this).parents('.edd-slg-social-container').find('.edd-slg-login-error');

		errorel.hide();
		errorel.html('');

		if (EDDSlg.lierror == '1') {
			errorel.show();
			errorel.html(EDDSlg.lierrormsg);
			return false;
		} else {

			var linkedinurl = $(this).closest('.edd-slg-social-container').find('.edd-slg-social-li-redirect-url').val();

			if (linkedinurl == '') {
				alert(EDDSlg.urlerror);
				return false;
			}
			var linkedinLogin = window.open(linkedinurl, "linkedin", "scrollbars=yes,resizable=no,toolbar=no,location=no,directories=no,status=no,menubar=no,copyhistory=no,height=400,width=600");
			var lTimer = setInterval(function () { //set interval for executing the code to popup
				try {
					if (linkedinLogin.location.hostname == window.location.hostname) { //if login domain host name and window location hostname is equal then it will go ahead
						clearInterval(lTimer);
						linkedinLogin.close();
						edd_slg_social_connect('linkedin', object);
					}
				} catch (e) { }
			}, 300);
		}

	});

	// login with twitter
	$(document).on('click', 'a.edd-slg-social-login-twitter', function () {

		var object = $(this);
		var errorel = $(this).parents('.edd-slg-social-container').find('.edd-slg-login-error');
		//var redirect_url = $(this).parents('.edd-slg-social-container').find('.edd-slg-redirect-url').val();
		var parents = $(this).parents('div.edd-slg-social-container');
		var appendurl = '';

		//check button is clicked form widget
		if (parents.hasClass('edd-slg-widget-content')) {
			appendurl = '&container=widget';
		}

		errorel.hide();
		errorel.html('');

		if (EDDSlg.twerror == '1') {
			errorel.show();
			errorel.html(EDDSlg.twerrormsg);
			return false;
		} else {

			var twitterurl = EDDSlg.tw_authurl;

			if (twitterurl == '') {
				alert(EDDSlg.urlerror);
				return false;
			}

			var twLogin = window.open(twitterurl, "twitter_login", "scrollbars=yes,resizable=no,toolbar=no,location=no,directories=no,status=no,menubar=no,copyhistory=no,height=400,width=600");
			var tTimer = setInterval(function () { //set interval for executing the code to popup
				try {
					/*if ( twLogin.location.hostname == window.location.hostname ) { //if login domain host name and window location hostname is equal then it will go ahead
						clearInterval(tTimer);
						twLogin.close();
						window.parent.location = EDDSlg.socialloginredirect+appendurl;
					}*/
					if (twLogin.location.hostname == window.location.hostname) { //if login domain host name and window location hostname is equal then it will go ahead
						clearInterval(tTimer);
						twLogin.close();
						if (EDDSlg.userid != '') {
							edd_slg_social_connect('twitter', object);
						}
						else {
							window.parent.location = EDDSlg.socialloginredirect + appendurl;
						}
					}
				} catch (e) { }
			}, 300);
		}

	});

	// login with yahoo
	$(document).on('click', 'a.edd-slg-social-login-yahoo', function () {

		var object = $(this);
		var errorel = $(this).parents('.edd-slg-social-container').find('.edd-slg-login-error');

		errorel.hide();
		errorel.html('');

		if (EDDSlg.yherror == '1') {
			errorel.show();
			errorel.html(EDDSlg.yherrormsg);
			return false;
		} else {

			var yahoourl = $(this).closest('.edd-slg-social-container').find('.edd-slg-social-yh-redirect-url').val();

			if (yahoourl == '') {
				alert(EDDSlg.urlerror);
				return false;
			}
			var yhLogin = window.open(yahoourl, "yahoo_login", "scrollbars=yes,resizable=no,toolbar=no,location=no,directories=no,status=no,menubar=no,copyhistory=no,height=400,width=600");
			var yTimer = setInterval(function () { //set interval for executing the code to popup
				try {
					if (yhLogin.location.hostname == window.location.hostname) { //if login domain host name and window location hostname is equal then it will go ahead
						clearInterval(yTimer);
						yhLogin.close();
						edd_slg_social_connect('yahoo', object);
					}
				} catch (e) { }
			}, 300);
		}
	});

	// login with foursquare
	$(document).on('click', 'a.edd-slg-social-login-foursquare', function () {

		var object = $(this);
		var errorel = $(this).parents('.edd-slg-social-container').find('.edd-slg-login-error');

		errorel.hide();
		errorel.html('');

		if (EDDSlg.fserror == '1') {
			errorel.show();
			errorel.html(EDDSlg.fserrormsg);
			return false;
		} else {

			var foursquareurl = $(this).closest('.edd-slg-social-container').find('.edd-slg-social-fs-redirect-url').val();

			if (foursquareurl == '') {
				alert(EDDSlg.urlerror);
				return false;
			}
			var fsLogin = window.open(foursquareurl, "foursquare_login", "scrollbars=yes,resizable=no,toolbar=no,location=no,directories=no,status=no,menubar=no,copyhistory=no,height=400,width=600");
			var fsTimer = setInterval(function () { //set interval for executing the code to popup
				try {
					if (fsLogin.location.hostname == window.location.hostname) { //if login domain host name and window location hostname is equal then it will go ahead
						clearInterval(fsTimer);
						fsLogin.close();
						edd_slg_social_connect('foursquare', object);
					}
				} catch (e) { }
			}, 300);
		}
	});

	// login with windows live
	$(document).on('click', 'a.edd-slg-social-login-windowslive', function () {

		var object = $(this);
		var errorel = $(this).parents('.edd-slg-social-container').find('.edd-slg-login-error');

		errorel.hide();
		errorel.html('');

		if (EDDSlg.wlerror == '1') {
			errorel.show();
			errorel.html(EDDSlg.wlerrormsg);
			return false;
		} else {

			var windowsliveurl = $(this).closest('.edd-slg-social-container').find('.edd-slg-social-wl-redirect-url').val();

			if (windowsliveurl == '') {
				alert(EDDSlg.urlerror);
				return false;
			}
			var wlLogin = window.open(windowsliveurl, "windowslive_login", "scrollbars=yes,resizable=no,toolbar=no,location=no,directories=no,status=no,menubar=no,copyhistory=no,height=400,width=600");
			var wlTimer = setInterval(function () { //set interval for executing the code to popup
				try {
					if (wlLogin.location.hostname == window.location.hostname) { //if login domain host name and window location hostname is equal then it will go ahead
						clearInterval(wlTimer);
						wlLogin.close();
						edd_slg_social_connect('windowslive', object);
					}
				} catch (e) { }
			}, 300);
		}
	});

	// login with VK.com
	$(document).on('click', 'a.edd-slg-social-login-vk', function () {

		var object = $(this);
		var errorel = $(this).parents('.edd-slg-social-container').find('.edd-slg-login-error');

		errorel.hide();
		errorel.html('');

		if (EDDSlg.vkerror == '1') {
			errorel.show();
			errorel.html(EDDSlg.vkerrormsg);
			return false;
		} else {

			var vkurl = $(this).closest('.edd-slg-social-container').find('.edd-slg-social-vk-redirect-url').val();

			if (vkurl == '') {
				alert(EDDSlg.urlerror);
				return false;
			}

			var vkLogin = window.open(vkurl, "vk_login", "scrollbars=yes,resizable=no,toolbar=no,location=no,directories=no,status=no,menubar=no,copyhistory=no,height=400,width=600");
			var vkTimer = setInterval(function () { //set interval for executing the code to popup
				try {
					if (vkLogin.location.hostname == window.location.hostname) { //if login domain host name and window location hostname is equal then it will go ahead
						clearInterval(vkTimer);
						vkLogin.close();
						edd_slg_social_connect('vk', object);
					}
				} catch (e) { }
			}, 300);
		}
	});

	// login with instagram
	$(document).on('click', 'a.edd-slg-social-login-instagram', function () {

		var object = $(this);
		var errorel = $(this).parents('.edd-slg-social-container').find('.edd-slg-login-error');

		errorel.hide();
		errorel.html('');

		if (EDDSlg.insterror == '1') {
			errorel.show();
			errorel.html(EDDSlg.insterrormsg);
			return false;
		} else {

			var instagramurl = $(this).closest('.edd-slg-social-container').find('.edd-slg-social-inst-redirect-url').val();

			if (instagramurl == '') {
				alert(EDDSlg.urlerror);
				return false;
			}
			var instLogin = window.open(instagramurl, "instagram_login", "scrollbars=yes,resizable=no,toolbar=no,location=no,directories=no,status=no,menubar=no,copyhistory=no,height=400,width=600");
			var instTimer = setInterval(function () { //set interval for executing the code to popup
				try {
					if (instLogin.location.hostname == window.location.hostname) { //if login domain host name and window location hostname is equal then it will go ahead
						clearInterval(instTimer);
						instLogin.close();
						edd_slg_social_connect('instagram', object);
					}
				} catch (e) { }
			}, 300);
		}
	});

	//My Account Show Link Buttons "edd-slg-show-link"
	$(document).on('click', '.edd-slg-show-link', function () {
		$('.edd-slg-show-link').hide();
		$('.edd-slg-profile-link-container').show();
	});


	// login with amazon
	$(document).on('click', 'a.edd-slg-social-login-amazon', function () {

		var object = $(this);
		var errorel = $(this).parents('.edd-slg-social-container').find('.edd-slg-login-error');

		errorel.hide();
		errorel.html('');

		if (EDDSlg.amazonerror == '1') {

			errorel.show();
			errorel.html(EDDSlg.amazonerrormsg);
			return false;

		} else {

			var amazonurl = $(this).closest('.edd-slg-social-container').find('.edd-slg-social-amazon-redirect-url').val();
			if (amazonurl == '') {
				alert(EDDSlg.urlerror);
				return false;
			}
			var amazonLogin = window.open(amazonurl, "amazon_login", "scrollbars=yes,resizable=no,toolbar=no,location=no,directories=no,status=no,menubar=no,copyhistory=no,height=400,width=600");
			var amazonTimer = setInterval(function () { //set interval for executing the code to popup
				try {
					if (amazonLogin.location.hostname == window.location.hostname) { //if login domain host name and window location hostname is equal then it will go ahead
						clearInterval(amazonTimer);
						amazonLogin.close();
						edd_slg_social_connect('amazon', object);
					}
				} catch (e) { }
			}, 300);
		}
	});


	// login with paypal
	$(document).on('click', 'a.edd-slg-social-login-paypal', function () {

		var object = $(this);
		var errorel = $(this).parents('.edd-slg-social-container').find('.edd-slg-login-error');

		errorel.hide();
		errorel.html('');

		if (EDDSlg.paypalerror == '1') {

			errorel.show();
			errorel.html(EDDSlg.paypalerrormsg);
			return false;

		} else {

			var paypalurl = $(this).closest('.edd-slg-social-container').find('.edd-slg-social-paypal-redirect-url').val();
			if (paypalurl == '') {
				alert(EDDSlg.urlerror);
				return false;
			}
			var paypalLogin = window.open(paypalurl, "paypal_login", "scrollbars=yes,resizable=no,toolbar=no,location=no,directories=no,status=no,menubar=no,copyhistory=no,height=400,width=600");
			var paypalTimer = setInterval(function () { //set interval for executing the code to popup
				try {
					if (paypalLogin.location.hostname == window.location.hostname) { //if login domain host name and window location hostname is equal then it will go ahead
						clearInterval(paypalTimer);
						paypalLogin.close();
						edd_slg_social_connect('paypal', object);
					}
				} catch (e) { }
			}, 300);
		}
	});



});

// Social Connect Process
function edd_slg_social_connect(type, object) {

	var data = {
		action: 'edd_slg_social_login',
		type: type
	};

	//show loader
	var main_container = jQuery(object).parents('.edd-slg-social-container');
	main_container.find('.edd-slg-login-loader').show();
	main_container.find('.edd-slg-social-wrap').hide();

	jQuery.post(EDDSlg.ajaxurl, data, function (response) {


		// hide loader
		main_container.find('.edd-slg-login-loader').hide();
		main_container.find('.edd-slg-social-wrap').show();
		var redirect_url = object.parents('.edd-slg-social-container').find('.edd-slg-redirect-url').val();

		if (response != '') {

			var result = jQuery.parseJSON(response);

			if (redirect_url != '') {

				redirect_url = removeParam(redirect_url, 'code');

				// check if caching option is selected
				if (EDDSlg.caching_enable == 'yes') {

					//check ? is exist in url or not
					var strexist = redirect_url.indexOf("?");

					if (strexist >= 0) {
						redirect_url = redirect_url + '&no-caching=1';
					} else {
						redirect_url = redirect_url + '?no-caching=1';
					}
				}

				window.location = redirect_url;

			} else {

				//if user created successfully then reload the page
				var current_url = window.location.href;
				current_url = removeParam(current_url, 'code');

				// check if caching option is selected
				if (EDDSlg.caching_enable == 'yes') {

					//check ? is exist in url or not
					var strexist = current_url.indexOf("?");

					if (strexist >= 0) {
						current_url = current_url + '&no-caching=1';
					} else {
						current_url = current_url + '?no-caching=1';
					}
				}

				window.location = current_url;
			}
		}
	});
}

function removeParam(url, parameter) {
	var urlparts = url.split('?');
	if (urlparts.length >= 2) {

		var prefix = encodeURIComponent(parameter) + '=';
		var pars = urlparts[1].split(/[&;]/g);

		//reverse iteration as may be destructive
		for (var i = pars.length; i-- > 0;) {
			//idiom for string.startsWith
			if (pars[i].lastIndexOf(prefix, 0) !== -1) {
				pars.splice(i, 1);
			}
		}
		url = urlparts[0] + (pars.length > 0 ? '?' + pars.join('&') : "");
		return url;
	} else {
		return url;
	}
}

var targetWindow = "prefer-popup";
window.NSLPopupCenter = function (url, title, w, h) {
	var userAgent = navigator.userAgent,
		mobile = function () {
			return /\b(iPhone|iP[ao]d)/.test(userAgent) ||
				/\b(iP[ao]d)/.test(userAgent) ||
				/Android/i.test(userAgent) ||
				/Mobile/i.test(userAgent);
		},
		screenX = window.screenX !== undefined ? window.screenX : window.screenLeft,
		screenY = window.screenY !== undefined ? window.screenY : window.screenTop,
		outerWidth = window.outerWidth !== undefined ? window.outerWidth : document.documentElement.clientWidth,
		outerHeight = window.outerHeight !== undefined ? window.outerHeight : document.documentElement.clientHeight - 22,
		targetWidth = mobile() ? null : w,
		targetHeight = mobile() ? null : h,
		V = screenX < 0 ? window.screen.width + screenX : screenX,
		left = parseInt(V + (outerWidth - targetWidth) / 2, 10),
		right = parseInt(screenY + (outerHeight - targetHeight) / 2.5, 10),
		features = [];
	if (targetWidth !== null) {
		features.push('width=' + targetWidth);
	}
	if (targetHeight !== null) {
		features.push('height=' + targetHeight);
	}
	features.push('left=' + left);
	features.push('top=' + right);
	features.push('scrollbars=1');

	var newWindow = window.open(url, title, features.join(','));

	if (window.focus) {
		newWindow.focus();
	}

	return newWindow;
};

var isWebView = null;

function checkWebView() {
	if (isWebView === null) {
		//Based on UserAgent.js {@link https://github.com/uupaa/UserAgent.js}
		function _detectOS(ua) {
			switch (true) {
				case / Android /.test(ua):
					return "Android";
				case / iPhone | iPad | iPod /.test(ua):
					return "iOS";
				case / Windows /.test(ua):
					return "Windows";
				case / Mac OS X /.test(ua):
					return "Mac";
				case / CrOS /.test(ua):
					return "Chrome OS";
				case / Firefox /.test(ua):
					return "Firefox OS";
			}
			return "";
		}

		function _detectBrowser(ua) {
			var android = /Android/.test(ua);

			switch (true) {
				case / CriOS /.test(ua):
					return "Chrome for iOS"; // https://developer.chrome.com/multidevice/user-agent
				case / Edge /.test(ua):
					return "Edge";
				case android && /Silk\//.test(ua):
					return "Silk"; // Kidle Silk browser
				case / Chrome /.test(ua):
					return "Chrome";
				case / Firefox /.test(ua):
					return "Firefox";
				case android:
					return "AOSP"; // AOSP stock browser
				case / MSIE | Trident /.test(ua):
					return "IE";
				case / Safari\//.test(ua):
					return "Safari";
				case / AppleWebKit /.test(ua):
					return "WebKit";
			}
			return "";
		}

		function _detectBrowserVersion(ua, browser) {
			switch (browser) {
				case "Chrome for iOS":
					return _getVersion(ua, "CriOS/");
				case "Edge":
					return _getVersion(ua, "Edge/");
				case "Chrome":
					return _getVersion(ua, "Chrome/");
				case "Firefox":
					return _getVersion(ua, "Firefox/");
				case "Silk":
					return _getVersion(ua, "Silk/");
				case "AOSP":
					return _getVersion(ua, "Version/");
				case "IE":
					return /IEMobile/.test(ua) ? _getVersion(ua, "IEMobile/") :
						/MSIE/.test(ua) ? _getVersion(ua, "MSIE ") // IE 10
							:
							_getVersion(ua, "rv:"); // IE 11
				case "Safari":
					return _getVersion(ua, "Version/");
				case "WebKit":
					return _getVersion(ua, "WebKit/");
			}
			return "0.0.0";
		}

		function _getVersion(ua, token) {
			try {
				return _normalizeSemverString(ua.split(token)[1].trim().split(/[^\w\.]/)[0]);
			} catch (o_O) {
				// ignore
			}
			return "0.0.0";
		}

		function _normalizeSemverString(version) {
			var ary = version.split(/[\._]/);
			return (parseInt(ary[0], 10) || 0) + "." +
				(parseInt(ary[1], 10) || 0) + "." +
				(parseInt(ary[2], 10) || 0);
		}

		function _isWebView(ua, os, browser, version, options) {
			switch (os + browser) {
				case "iOSSafari":
					return false;
				case "iOSWebKit":
					return _isWebView_iOS(options);
				case "AndroidAOSP":
					return false; // can not accurately detect
				case "AndroidChrome":
					return parseFloat(version) >= 42 ? /; wv/.test(ua) : /\d{2}\.0\.0/.test(version) ? true : _isWebView_Android(options);
			}
			return false;
		}

		function _isWebView_iOS(options) { // @arg Object - { WEB_VIEW }
			// @ret Boolean
			// Chrome 15++, Safari 5.1++, IE11, Edge, Firefox10++
			// Android 5.0 ChromeWebView 30: webkitFullscreenEnabled === false
			// Android 5.0 ChromeWebView 33: webkitFullscreenEnabled === false
			// Android 5.0 ChromeWebView 36: webkitFullscreenEnabled === false
			// Android 5.0 ChromeWebView 37: webkitFullscreenEnabled === false
			// Android 5.0 ChromeWebView 40: webkitFullscreenEnabled === false
			// Android 5.0 ChromeWebView 42: webkitFullscreenEnabled === ?
			// Android 5.0 ChromeWebView 44: webkitFullscreenEnabled === true
			var document = (window["document"] || {});

			if ("WEB_VIEW" in options) {
				return options["WEB_VIEW"];
			}
			return !("fullscreenEnabled" in document || "webkitFullscreenEnabled" in document || false);
		}

		function _isWebView_Android(options) {
			// Chrome 8++
			// Android 5.0 ChromeWebView 30: webkitRequestFileSystem === false
			// Android 5.0 ChromeWebView 33: webkitRequestFileSystem === false
			// Android 5.0 ChromeWebView 36: webkitRequestFileSystem === false
			// Android 5.0 ChromeWebView 37: webkitRequestFileSystem === false
			// Android 5.0 ChromeWebView 40: webkitRequestFileSystem === false
			// Android 5.0 ChromeWebView 42: webkitRequestFileSystem === false
			// Android 5.0 ChromeWebView 44: webkitRequestFileSystem === false
			if ("WEB_VIEW" in options) {
				return options["WEB_VIEW"];
			}
			return !("requestFileSystem" in window || "webkitRequestFileSystem" in window || false);
		}

		var options = {};
		var nav = window.navigator || {};
		var ua = nav.userAgent || "";
		var os = _detectOS(ua);
		var browser = _detectBrowser(ua);
		var browserVersion = _detectBrowserVersion(ua, browser);

		isWebView = _isWebView(ua, os, browser, browserVersion, options);
	}

	return isWebView;
}
if (typeof jQuery !== 'undefined') {
	var targetWindow = 'prefer-popup';
	(function ($) {
		$('a[data-plugin="edd-slg"][data-action="connect"],a[data-plugin="edd-slg"][data-action="link"]').on('click', function (e) {

			var $target = $(this),
				href = $target.attr('href'),
				success = false;
			if (href.indexOf('?') !== -1) {
				href += '&';
			} else {
				href += '?';
			}
			var redirectTo = $target.data('redirect');
			if (redirectTo === 'current') {
				href += 'redirect=' + encodeURIComponent(window.location.href) + '&';
			} else if (redirectTo && redirectTo !== '') {
				href += 'redirect=' + encodeURIComponent(redirectTo) + '&';
			}


			if (NSLPopupCenter(href + 'display=popup', 'nsl-social-connect', $target.data('popupwidth'), $target.data('popupheight'))) {
				success = true;
				e.preventDefault();
			}
			if (!success) {
				window.location = href;
				e.preventDefault();
			}
		});


	})(jQuery);
}
function hideLoaderAgain() {
	jQuery('a.edd-slg-social-login-facebook').parents('.edd-slg-social-container').find('.edd-slg-login-loader').hide();
	jQuery('a.edd-slg-social-login-facebook').parents('.edd-slg-social-container').find('.edd-slg-social-wrap').show();
}
function showLoaderNow() {
	jQuery('a.edd-slg-social-login-facebook').parents('.edd-slg-social-container').find('.edd-slg-login-loader').show();
	//jQuery('.edd-slg-social-wrap').hide();
	jQuery('a.edd-slg-social-login-facebook').parents('.edd-slg-social-container').find('.edd-slg-social-wrap').hide();
}