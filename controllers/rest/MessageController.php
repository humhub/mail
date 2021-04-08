<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2020 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\mail\controllers\rest;

use humhub\modules\mail\models\forms\CreateMessage;
use humhub\modules\mail\models\Message;
use humhub\modules\mail\permissions\StartConversation;
use humhub\modules\rest\components\BaseController;
use humhub\modules\mail\helpers\RestDefinitions;
use Yii;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;


/**
 * Class MessageController
 */
class MessageController extends BaseController
{

    /**
     * Get list of mail conversations
     *
     * @return array
     */
    public function actionIndex()
    {
        $results = [];
        $messagesQuery = Message::find()
            ->innerJoin('user_message', 'message_id = id')
            ->where(['user_id' => Yii::$app->user->id]);

        $pagination = $this->handlePagination($messagesQuery);
        foreach ($messagesQuery->all() as $message) {
            $results[] = RestDefinitions::getMessage($message);
        }
        return $this->returnPagination($messagesQuery, $pagination, $results);
    }

    /**
     * Get a mail conversation by id
     *
     * @param $id
     * @return array
     * @throws HttpException
     */
    public function actionView($id)
    {
        $message = static::getMessage($id, true);
        return RestDefinitions::getMessage($message);
    }

    /**
     * Create a mail conversation
     *
     * @return array
     * @throws \Throwable
     */
    public function actionCreate()
    {
        if (!Yii::$app->user->isAdmin() && !Yii::$app->user->getPermissionManager()->can(StartConversation::class)) {
            return $this->returnError(403, 'You cannot create conversations!');
        }

        $message = new CreateMessage();
        $message->load(['CreateMessage' => Yii::$app->request->post()]);

        if ($message->save()) {
            return $this->actionView($message->messageInstance->id);
        }

        if ($message->hasErrors()) {
            return $this->returnError(400, 'Validation failed', $message->getErrors());
        }

        Yii::error('Could not create validated conversation.', 'api');
        return $this->returnError(500, 'Internal error while save conversation!');
    }

    /**
     * Get conversation by id
     *
     * @param $id
     * @return Message
     * @throws HttpException
     */
    public static function getMessage($id)
    {
        $message = Message::findOne(['id' => $id]);
        if ($message === null) {
            throw new HttpException(404, 'Message not found!');
        }

        if (!$message->isParticipant(Yii::$app->user)) {
            throw new ForbiddenHttpException('You must be a participant of the conversation.');
        }

        return $message;
    }
}