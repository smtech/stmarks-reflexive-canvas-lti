<?php

require_once 'common.inc.php';

use Battis\BootstrapSmarty\NotificationMessage;

if (!empty($_REQUEST['error'])) {
    $smarty->addMessage(
        $_REQUEST['error']['title'],
        $_REQUEST['error']['message'],
        NotificationMessage::ERROR
    );
}

$smarty->display('subpage.tpl');
