<?php
/*
 * This is a PHP library that handles calling reCAPTCHA.
 *    - Documentation and latest version
 *          http://recaptcha.net/plugins/php/
 *    - Get a reCAPTCHA API Key
 *          https://www.google.com/recaptcha/admin/create
 *    - Discussion group
 *          http://groups.google.com/group/recaptcha
 *
 * Copyright (c) 2007 reCAPTCHA -- http://recaptcha.net
 * AUTHORS:
 *   Mike Crawford
 *   Ben Maurer
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

/**
 * The reCAPTCHA server URL's
 */
if ( ! defined( 'RECAPTCHA_API_SERVER' ) ) :
    define("RECAPTCHA_API_SERVER", "http://www.google.com/recaptcha/api");
endif;

if ( ! defined( 'RECAPTCHA_API_SECURE_SERVER' ) ) :
    define("RECAPTCHA_API_SECURE_SERVER", "https://www.google.com/recaptcha/api");
endif;

if ( ! defined( 'RECAPTCHA_VERIFY_SERVER' ) ) :
    define("RECAPTCHA_VERIFY_SERVER", "www.google.com");
endif;

/**
 * Encodes the given data into a query string format
 * @param $data - array of string elements to be encoded
 * @return string - encoded request
 */
if ( ! function_exists('_recaptcha_qsencode' ) ) :
function _recaptcha_qsencode ($data) {
    $req = "";
    foreach ( $data as $key => $value )
            $req .= $key . '=' . urlencode( stripslashes($value) ) . '&';

    // Cut the last '&'
    $req=substr($req,0,strlen($req)-1);
    return $req;
}
endif;



/**
 * Submits an HTTP POST to a reCAPTCHA server
 * @param string $host
 * @param string $path
 * @param array $data
 * @param int port
 * @return array response
 */
if ( ! function_exists( '_recaptcha_http_post' ) ) :
function _recaptcha_http_post($host, $path, $data, $port = 80)
{

    $req = _recaptcha_qsencode($data);

    $url = 'https://www.google.com/recaptcha/api/siteverify';


    $options = array(
        'http' => array(
            'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data)
        )
    );

    $context = stream_context_create($options);
    $verify = file_get_contents($url, false, $context);
    $captcha_success = json_decode($verify);

    if ($captcha_success->success == false) {

        return false;
    } else if ($captcha_success->success == true) {
        return true;
    }
}
endif;



/**
 * Gets the challenge HTML (javascript and non-javascript version).
 * This is called from the browser, and the resulting reCAPTCHA HTML widget
 * is embedded within the HTML form it was called from.
 * @param string $pubkey A public key for reCAPTCHA
 * @param string $error The error given by reCAPTCHA (optional, default is null)
 * @param boolean $use_ssl Should the request be made over ssl? (optional, default is false)

 * @return string - The HTML to be embedded in the user's form.
 */
if ( ! function_exists( 'recaptcha_get_html' ) ) :
function recaptcha_get_html ($pubkey, $error = null, $use_ssl = false)
{
    if ($pubkey == null || $pubkey == '') {
        die ("To use reCAPTCHA you must get an API key from <a href='https://www.google.com/recaptcha/admin/create'>https://www.google.com/recaptcha/admin/create</a>");
    }

    if ($use_ssl) {
                $server = RECAPTCHA_API_SECURE_SERVER;
        } else {
                $server = RECAPTCHA_API_SERVER;
        }

        $errorpart = "";
        if ($error) {
           $errorpart = "&amp;error=" . $error;
        }
    return '<script src="https://www.google.com/recaptcha/api.js"></script><div class="g-recaptcha" data-sitekey="'. $pubkey.'"></div>

    <noscript>
        <iframe src="'. $server . '/noscript?k=' . $pubkey . $errorpart . '" height="300" width="300" frameborder="0"></iframe><br/>
        <textarea name="recaptcha_challenge_field" rows="3" cols="40"></textarea>
        <input type="hidden" name="recaptcha_response_field" value="manual_challenge"/>
    </noscript>';
}
endif;

/**
 * A ReCaptchaResponse is returned from recaptcha_check_answer()
 */
if ( ! class_exists( 'ReCaptchaResponse' ) ) :
class ReCaptchaResponse {
        var $is_valid;
        var $error;
}
endif;

/**
  * Calls an HTTP POST function to verify if the user's guess was correct
  * @param string $privkey
  * @param string $remoteip
  * @param string $challenge
  * @param string $response
  * @param array $extra_params an array of extra variables to post to the server
  * @return ReCaptchaResponse
  */
if ( ! function_exists( 'recaptcha_check_answer' ) ) :
function recaptcha_check_answer ($privkey, $remoteip, $response, $extra_params = array())
{
    if ($privkey == null || $privkey == '') {
        die ("To use reCAPTCHA you must get an API key from <a href='https://www.google.com/recaptcha/admin/create'>https://www.google.com/recaptcha/admin/create</a>");
    }

    if ($remoteip == null || $remoteip == '') {
        die ("For security reasons, you must pass the remote ip to reCAPTCHA");
    }

    //discard spam submissions
    if ($response == null || strlen($response) == 0) {

    $recaptcha_response = new ReCaptchaResponse();
    $recaptcha_response->is_valid = false;
    $recaptcha_response->error = apply_filters('recaptch_error_incorrect-captcha-sol_label', 'incorrect-captcha-sol');
    return $recaptcha_response;
  }


    $response = _recaptcha_http_post('https://www.google.com', "/recaptcha/api/siteverify", array(
        'secret' => $privkey,
        'response' => $response,
        'remoteip' => $remoteip
         )
        );


    $recaptcha_response = new ReCaptchaResponse();



  if ($response == 'true') {
    $recaptcha_response->is_valid = true;
  }   else {
      $recaptcha_response->is_valid = false;
  }

   return $recaptcha_response;
}
endif;

/**
 * gets a URL where the user can sign up for reCAPTCHA. If your application
 * has a configuration page where you enter a key, you should provide a link
 * using this function.
 * @param string $domain The domain where the page is hosted
 * @param string $appname The name of your application
 */
if ( ! function_exists( 'recaptcha_get_signup_url' ) ) :
function recaptcha_get_signup_url ($domain = null, $appname = null) {
    return "https://www.google.com/recaptcha/admin/create?" .  _recaptcha_qsencode (array ('domains' => $domain, 'app' => $appname));
}
endif;

if ( ! function_exists( '_recaptcha_aes_pad' ) ) :
function _recaptcha_aes_pad($val) {
    $block_size = 16;
    $numpad = $block_size - (strlen ($val) % $block_size);
    return str_pad($val, strlen ($val) + $numpad, chr($numpad));
}
endif;

/* Mailhide related code */
if ( ! function_exists( '_recaptcha_aes_encrypt' ) ) :
function _recaptcha_aes_encrypt($val,$ky) {
    if (! function_exists ("mcrypt_encrypt")) {
        die ("To use reCAPTCHA Mailhide, you need to have the mcrypt php module installed.");
    }
    $mode=MCRYPT_MODE_CBC;
    $enc=MCRYPT_RIJNDAEL_128;
    $val=_recaptcha_aes_pad($val);
    return mcrypt_encrypt($enc, $ky, $val, $mode, "\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0");
}
endif;

if ( ! function_exists( '_recaptcha_mailhide_urlbase64' ) ) :
function _recaptcha_mailhide_urlbase64 ($x) {
    return strtr(base64_encode ($x), '+/', '-_');
}
endif;

/* gets the reCAPTCHA Mailhide url for a given email, public key and private key */
if ( ! function_exists( 'recaptcha_mailhide_url' ) ) :
function recaptcha_mailhide_url($pubkey, $privkey, $email) {
    if ($pubkey == '' || $pubkey == null || $privkey == "" || $privkey == null) {
        die ("To use reCAPTCHA Mailhide, you have to sign up for a public and private key, " .
             "you can do so at <a href='http://www.google.com/recaptcha/mailhide/apikey'>http://www.google.com/recaptcha/mailhide/apikey</a>");
    }


    $ky = pack('H*', $privkey);
    $cryptmail = _recaptcha_aes_encrypt ($email, $ky);

    return "http://www.google.com/recaptcha/mailhide/d?k=" . $pubkey . "&c=" . _recaptcha_mailhide_urlbase64 ($cryptmail);
}
endif;

/**
 * gets the parts of the email to expose to the user.
 * eg, given johndoe@example,com return ["john", "example.com"].
 * the email is then displayed as john...@example.com
 */
if ( ! function_exists( '_recaptcha_mailhide_email_parts' ) ) :
function _recaptcha_mailhide_email_parts ($email) {
    $arr = preg_split("/@/", $email );

    if (strlen ($arr[0]) <= 4) {
        $arr[0] = substr ($arr[0], 0, 1);
    } else if (strlen ($arr[0]) <= 6) {
        $arr[0] = substr ($arr[0], 0, 3);
    } else {
        $arr[0] = substr ($arr[0], 0, 4);
    }
    return $arr;
}
endif;

/**
 * Gets html to display an email address given a public an private key.
 * to get a key, go to:
 *
 * http://www.google.com/recaptcha/mailhide/apikey
 */
if ( ! function_exists( 'recaptcha_mailhide_html' ) ) :
function recaptcha_mailhide_html($pubkey, $privkey, $email) {
    $emailparts = _recaptcha_mailhide_email_parts ($email);
    $url = recaptcha_mailhide_url ($pubkey, $privkey, $email);

    return htmlentities($emailparts[0]) . "<a href='" . htmlentities ($url) .
        "' onclick=\"window.open('" . htmlentities ($url) . "', '', 'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0,width=500,height=300'); return false;\" title=\"Reveal this e-mail address\">...</a>@" . htmlentities ($emailparts [1]);

}
endif;
?>