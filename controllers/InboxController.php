<?php

namespace humhub\modules\mail\controllers;

use humhub\components\Controller;
use humhub\modules\mail\models\forms\InboxFilterForm;
use humhub\modules\mail\widgets\ConversationInbox;
use humhub\modules\mail\widgets\InboxMessagePreview;

/**
 * MailController provides messaging actions.
 *
 * @package humhub.modules.mail.controllers
 * @since 0.5
 */
class InboxController extends Controller
{

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

    public function actionLoadMore()
    {
        $filter = new InboxFilterForm();
        $userMessages = $filter->getPage();

        $result = '';
        foreach ($userMessages as $userMessage) {
            $result .= InboxMessagePreview::widget(['userMessage' => $userMessage]);
        }

       return $this->asJson([
           'result' => $result,
           'isLast' => $filter->wasLastPage()
       ]);

    }

}