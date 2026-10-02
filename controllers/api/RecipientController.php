<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2026 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\mail\controllers\api;

use humhub\components\api\BaseController;
use humhub\modules\mail\services\RecipientStateService;
use Yii;
use yii\web\ForbiddenHttpException;

/**
 * Whether the caller may write to users — the "Send message" action on the cards of the core's
 * People directory (`vue/components/MailPeopleCardAction.vue` in the extension slot
 * `user.card-actions`).
 *
 * @since 3.5.0
 */
class RecipientController extends BaseController
{
    /**
     * @inheritdoc
     */
    protected bool $allowSessionAuth = true;

    /**
     * The state of each of the users `ids` (repeated or comma-separated, at most
     * {@see RecipientStateService::MAX_IDS}) the caller may see. Answers
     * `{results: {<userId>: {canMessage, url}}}` ({@see RecipientStateService::states()}).
     *
     * @throws ForbiddenHttpException when the caller may not start conversations
     */
    public function actionStates()
    {
        if (!RecipientStateService::canStartConversation()) {
            throw new ForbiddenHttpException('You are not allowed to start conversations.');
        }

        $ids = Yii::$app->request->get('ids', []);
        $ids = is_array($ids) ? $ids : explode(',', (string)$ids);

        $results = [];
        foreach ((new RecipientStateService())->states($ids) as $userId => $state) {
            $results[(string)$userId] = $state;
        }

        return ['results' => (object)$results];
    }
}
