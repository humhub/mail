<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2020 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\mail\controllers\rest;

use humhub\modules\rest\components\BaseController;
use humhub\modules\mail\helpers\RestDefinitions;
use humhub\modules\user\models\User;
use Yii;
use yii\web\HttpException;


/**
 * Class UserController
 */
class UserController extends BaseController
{

    /**
     * Get all participants of the conversation
     *
     * @param $messageId
     * @return array
     * @throws HttpException
     */
    public function actionIndex($messageId)
    {
        $message = MessageController::getMessage($messageId);
        return RestDefinitions::getMessageUsers($message);
    }

    /**
     * Add a participant into conversation
     *
     * @param $messageId
     * @param $userId
     * @return array
     * @throws HttpException
     */
    public function actionAdd($messageId, $userId)
    {
        $message = MessageController::getMessage($messageId);
        $user = $this->getUser($userId);

        if ($message->isParticipant($user)) {
            return $this->returnError(400, 'User is already a participant of the conversation.');
        }

        if ($message->addRecepient($user)) {
            return $this->actionIndex($messageId);
        }

        Yii::error('Could not add a participant into conversation.', 'api');
        return $this->returnError(500, 'Internal error while add a participant into conversation!');
    }

    /**
     * Leave a participant from conversation
     *
     * @param $messageId
     * @param $userId
     * @return array
     * @throws HttpException
     */
    public function actionLeave($messageId, $userId)
    {
        $message = MessageController::getMessage($messageId);
        $user = $this->getUser($userId);

        if (!$message->isParticipant($user)) {
            return $this->returnError(400, 'User is not a participant of the conversation.');
        }

        $message->leave($userId);

        return $this->actionIndex($messageId);
    }

    /**
     * Get user by id
     *
     * @param $id
     * @return User
     * @throws HttpException
     */
    protected function getUser($id)
    {
        $user = User::findOne(['id' => $id]);
        if ($user === null) {
            throw new HttpException(404, 'User not found!');
        }
        return $user;
    }
}