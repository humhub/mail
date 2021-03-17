<?php

namespace humhub\modules\mail\controllers;

use humhub\components\access\ControllerAccess;
use humhub\components\Controller;
use humhub\modules\mail\models\forms\InboxFilterForm;
use humhub\modules\mail\widgets\ConversationInbox;
use humhub\modules\mail\widgets\InboxMessagePreview;
use Yii;

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
            [ControllerAccess::RULE_LOGGED_IN_ONLY]
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
            try {
                $result .= InboxMessagePreview::widget(['userMessage' => $userMessage]);
            } catch(\Throwable $e) {
                Yii::error($e);
            }
        }

        return $this->asJson([
            'result' => $result,
            'isLast' => $filter->wasLastPage()
        ]);

    }

    public function actionUpdateEntries()
    {
        $filter = new InboxFilterForm();
        $filter->apply();

        $result = [];
        foreach ($filter->query->all() as $userMessage) {
            try {
                $result[$userMessage->message_id] = InboxMessagePreview::widget(['userMessage' => $userMessage]);
            } catch (\Throwable $e) {
                Yii::error($e);
            }
        }

        return $this->asJson([
            'result' => $result,
        ]);
    }

}