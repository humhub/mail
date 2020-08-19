<?php

namespace humhub\modules\mail\controllers;

use humhub\components\Controller;
use humhub\modules\mail\models\forms\InboxFilterForm;
use humhub\modules\mail\widgets\ConversationInbox;

/**
 * MailController provides messaging actions.
 *
 * @package humhub.modules.mail.controllers
 * @since 0.5
 */
class InboxController extends Controller
{

    public $pageSize = 30;

    public function getAccessRules()
    {
        return [
            ['login']
        ];
    }

    /**
     * Overview of all messages
     * @param null $id
     * @return string
     * @throws \Exception
     */
    public function actionIndex()
    {
        return ConversationInbox::widget([
            'filter' => new InboxFilterForm()
        ]);
    }

}