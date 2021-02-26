<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2020 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\mail\controllers\rest;

use humhub\modules\mail\models\forms\ConversationTagsForm;
use humhub\modules\mail\models\MessageTag;
use humhub\modules\mail\models\UserMessageTag;
use humhub\modules\rest\components\BaseController;
use humhub\modules\mail\helpers\RestDefinitions;
use Yii;
use yii\web\HttpException;


/**
 * Class TagController
 */
class TagController extends BaseController
{

    /**
     * Get all tags of the conversation
     *
     * @param $messageId
     * @return array
     * @throws HttpException
     */
    public function actionIndex($messageId)
    {
        // Check the requested Conversation exists and allowed to view by current User
        MessageController::getMessage($messageId);

        $results = [];
        $tagsQuery = MessageTag::find()
            ->innerJoin(UserMessageTag::tableName(), 'tag_id = id')
            ->where(['message_id' => $messageId])
            ->andWhere([MessageTag::tableName() . '.user_id' => Yii::$app->user->id]);

        $pagination = $this->handlePagination($tagsQuery);
        foreach ($tagsQuery->all() as $tag) {
            $results[] = RestDefinitions::getMessageTag($tag);
        }
        return $this->returnPagination($tagsQuery, $pagination, $results);
    }

    /**
     * Update tags for the conversation
     *
     * @param $messageId
     * @return array
     * @throws HttpException
     */
    public function actionUpdate($messageId)
    {
        $message = MessageController::getMessage($messageId);

        $conversationTagsForm = new ConversationTagsForm(['message' => $message]);

        $passedTags = Yii::$app->request->getBodyParam('tags', []);
        $updatedTags = [];
        foreach ($conversationTagsForm->tags as $conversationTag) {
            $tagIndex = array_search($conversationTag->name, $passedTags);
            if ($tagIndex !== false) {
                $updatedTags[] = $conversationTag->id;
                unset($passedTags[$tagIndex]);
            }
        }
        foreach ($passedTags as $passedTag) {
            $updatedTags[] = '_add:' . $passedTag;
        }

        $conversationTagsForm->load(['ConversationTagsForm' => ['tags' => $updatedTags]]);

        if ($conversationTagsForm->save()) {
            return $this->actionIndex($messageId);
        }

        if ($conversationTagsForm->hasErrors()) {
            return $this->returnError(400, 'Validation failed', $conversationTagsForm->getErrors());
        }

        Yii::error('Could not create validated entry for the conversation.', 'api');
        return $this->returnError(500, 'Internal error while update tags of the conversation!');
    }
}