<?php

namespace mail;

use Codeception\Module;

/**
 * This helper is used to populate the database with needed fixtures before any tests are run.
 * In this example, the database is populated with the demo login user, which is used in acceptance
 * and functional tests.  All fixtures will be loaded before the suite is started and unloaded after it
 * completes.
 */
class FunctionalMailHelper extends Module
{
    public function sendMessage($recipient, $title, $message)
    {
        $result = $this->getModule('Yii2')->_loadPage('POST', 'index-test.php?r=mail/mail/create', 
                ['CreateMessage[recipient][]' => $recipient,
                 'CreateMessage[title]' => $title,
                 'CreateMessage[message]' => $message]);
    }
}
