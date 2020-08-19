<?php

namespace humhub\modules\mail\controllers;

use humhub\modules\content\widgets\richtext\RichText;
use humhub\modules\mail\models\forms\ConversationTagsForm;
use humhub\modules\mail\models\MessageTag;
use humhub\modules\mail\widgets\ConversationTagPicker;
use humhub\modules\mail\widgets\ConversationTags;
use humhub\modules\mail\widgets\wall\ConversationEntry;
use humhub\widgets\ModalClose;
use Yii;
use humhub\modules\mail\permissions\StartConversation;
use yii\data\Pagination;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;
use humhub\components\Controller;
use humhub\modules\file\models\File;
use humhub\modules\mail\models\Message;
use humhub\modules\mail\models\MessageEntry;
use humhub\modules\mail\models\UserMessage;
use humhub\modules\User\models\User;
use humhub\modules\mail\models\forms\InviteParticipantForm;
use humhub\modules\mail\models\forms\ReplyForm;
use humhub\modules\mail\models\forms\CreateMessage;
use humhub\modules\mail\permissions\SendMail;
use humhub\modules\user\models\UserPicker;
use yii\web\NotFoundHttpException;

/**
 * MailController provides messaging actions.
 *
 * @package humhub.modules.mail.controllers
 * @since 0.5
 */
class TagController extends Controller
{
    /**
     * @inheritDoc
     */
    public function getAccessRules()
    {
        return [
            ['login']
        ];
    }

    public function actionSearch($keyword)
    {
        $results = MessageTag::search(Yii::$app->user->id, $keyword);

        return $this->asJson(array_map(function(MessageTag $tag) {
            return ['id' => $tag->id, 'text' => $tag->name, 'image' => ConversationTagPicker::getIcon()];
        }, $results));
    }

    public function actionEditConversation($messageId)
    {
        $message = Message::findOne(['id' => $messageId]);

        if(!$message) {
            throw new NotFoundHttpException();
        }

        if(!$message->isParticipant(Yii::$app->user->getIdentity())) {
            throw new ForbiddenHttpException();
        }

        $model = new ConversationTagsForm(['message' => $message]);

        if($model->load(Yii::$app->request->post()) && $model->save()) {
            return ModalClose::widget([
                'script' => '$("#'.ConversationTags::ID.'").replaceWith(\''.ConversationTags::widget(['message' => $message]).'\');'
            ]);
        }

        return $this->renderAjax('editConversationTagsModal', ['model' => new ConversationTagsForm(['message' => $message])]);
    }
}