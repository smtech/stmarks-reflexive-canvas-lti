<?php

require_once 'common.inc.php';

use smtech\OAuth2\Client\Provider\CanvasLMS;
use Battis\DataUtilities;

/* have we been asked to return to a particular URL? */
if (!empty($_REQUEST['oauth-return'])) {
    $_SESSION['oauth']['return'] = $_REQUEST['oauth-return'];
}

/* have we been given a specific error URL? */
if (!empty($_REQUEST['oauth-error'])) {
    $_SESSION['oauth']['error'] = $_REQUEST['oauth-error'];
}

/* do we have a Canvas instance URL yet? */
if (empty($_SESSION['oauth']['instance']) && empty ($_REQUEST['url'])) {
    $smarty->assign([
        'formAction' => $_SERVER['PHP_SELF'],
        'reason' => (empty($_REQUEST['reason']) ? false : $_REQUEST['reason'])
    ]);
    $smarty->display('oauth.tpl');
    exit;
} elseif (empty($_SESSION['oauth']['instance']) && !empty($_REQUEST['url'])) {
    $_SESSION['oauth']['instance'] = $_REQUEST['url'];
}

$provider = new CanvasLMS([
    'clientId' => $_SESSION['oauth']['key'],
    'clientSecret' => $_SESSION['oauth']['secret'],
    'purpose' => $_SESSION['oauth']['purpose'],
    'redirectUri' => DataUtilities::URLfromPath(__FILE__),
    'canvasInstanceUrl' => $_SESSION['oauth']['instance']
]);

/* if we don't already have an authorization code, let's get one! */
if (!isset($_GET['code'])) {
    $authorizationUrl = $provider->getAuthorizationUrl();
    $_SESSION['oauth']['state'] = $provider->getState();
    header("Location: $authorizationUrl");
    exit;

/* check that the passed state matches the stored state to mitigate cross-site request forgery attacks */
} elseif (empty($_GET['state']) || $_GET['state'] !== $_SESSION['oauth']['state']) {
    unset($_SESSION['oauth']);
    header(
        "Location: {$_SESSION['oauth']['error']}?error[title]=Invalid State&" .
        'error[message]=Mismatch between stored and received OAuth states, ' .
        'may indicate CSRF attack.'
    );
    exit;

} else {
    /*
     * acquire and save our token (using our existing code), pass back the
     * newly-acquired token in session data
     */
    $_SESSION['TOOL_CANVAS_API'] = [
        'key' => $_SESSION['oauth']['key'],
        'secret' => $_SESSION['oauth']['secret'],
        'url' => $_SESSION['oauth']['instance'],
        'token' => $provider->getAccessToken('authorization_code', ['code' => $_GET['code']])->getToken()
    ];

    /* return to what we were doing before we had to authenticate */
    header("Location: {$_SESSION['oauth']['return']}");
    unset($_SESSION['oauth']);
    exit;
}
