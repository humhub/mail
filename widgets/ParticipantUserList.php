<?php

namespace humhub\modules\mail\widgets;

use humhub\components\Widget;
use humhub\libs\Html;
use humhub\modules\mail\helpers\Url;
use humhub\modules\mail\models\Message;
use humhub\widgets\Link;
use Yii;

class ParticipantUserList extends Widget
{
    /**
     * @var Message
     */
    public $message;

    public int $limit = 2;

    public function run()
    {
        $userList = $this->renderUserList();
        if ($userList === '') {
            return '';
        }

        return Link::asLink($userList)->action('ui.modal.load', Url::toConversationUserList($this->message));
    }

    private function renderUserList(): string
    {
        $users = $this->message->getUsers()->limit($this->limit)->all();
        $userCount = count($users);
        $userTotalCount = $this->message->getUsers()->count();

        $result = '';
        foreach ($users as $u => $user) {
            $result .= Html::encode($user->displayName);
            $result .= $u < $userCount - 1 ? ', ' : '';
        }

        if ($userTotalCount > $userCount) {
            $result .= ', +' . Yii::t('MailModule.base', '{n,plural,=1{# other} other{# others}}', ['n' => $userTotalCount - $userCount]);
        }

        return $result;
    }
}