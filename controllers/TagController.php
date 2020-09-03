<?php

namespace humhub\modules\mail\controllers;

use humhub\components\access\ControllerAccess;
use humhub\modules\mail\helpers\Url;
use humhub\modules\mail\models\forms\AddTag;
use humhub\modules\mail\models\forms\ConversationTagsForm;
use humhub\modules\mail\models\MessageTag;
use humhub\modules\mail\widgets\ConversationTagPicker;
use humhub\modules\mail\widgets\ConversationTags;
use humhub\widgets\ModalClose;
use Yii;
use yii\web\ForbiddenHttpException;
use humhub\components\Controller;
use humhub\modules\mail\models\Message;
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
            [ControllerAccess::RULE_LOGGED_IN_ONLY]
        ];
    }

    public function actionManage()
    {
        return $this->render('manage', ['model' => new AddTag()]);
    }

    public function actionAdd()
    {
        $model = new AddTag();
        $model->load(Yii::$app->request->post());
        if($model->save()) {
            $model = new AddTag();
        }
        return $this->render('manage', ['model' => $model]);
    }

    public function actionEdit($id)
    {
        $tag = $this->findTag($id);

        if($tag->load(Yii::$app->request->post()) && $tag->save()) {
            return ModalClose::widget(['reload' => true]);
        }

        return $this->renderAjax('editModal', ['model' => $tag]);

    }

    /**
     * @param $id
     * @return MessageTag
     * @throws NotFoundHttpException
     */
    private function findTag($id)
    {
        $tag = MessageTag::findByUser(Yii::$app->user->id)->andWhere(['id' => $id])->one();

        if (!$tag) {
            throw new NotFoundHttpException();
        }

        return $tag;
    }

    /**
     * @param $id
     * @return TagController|\yii\console\Response|\yii\web\Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     * @throws \yii\web\HttpException
     */
    public function actionDelete($id)
    {
        $this->forcePostRequest();
        $this->findTag($id)->delete();
        return $this->redirect(Url::toManageTags());
    }

    public function actionSearch($keyword)
    {
        $results = MessageTag::search(Yii::$app->user->id, $keyword);

        return $this->asJson(array_map(function (MessageTag $tag) {
            return ['id' => $tag->id, 'text' => $tag->name, 'image' => ConversationTagPicker::getIcon()];
        }, $results));
    }

    public function actionEditConversation($messageId)
    {
        $message = Message::findOne(['id' => $messageId]);

        if (!$message) {
            throw new NotFoundHttpException();
        }

        if (!$message->isParticipant(Yii::$app->user->getIdentity())) {
            throw new ForbiddenHttpException();
        }

        $model = new ConversationTagsForm(['message' => $message]);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return ModalClose::widget([
                'script' => '$("#' . ConversationTags::ID . '").replaceWith(\'' . ConversationTags::widget(['message' => $message]) . '\');'
            ]);
        }

        return $this->renderAjax('editConversationTagsModal', ['model' => new ConversationTagsForm(['message' => $message])]);
    }
}