<?php

namespace humhub\modules\mail\widgets;

use humhub\components\Widget;
use humhub\libs\Html;
use humhub\modules\mail\helpers\Url;
use humhub\modules\mail\models\Message;
use humhub\widgets\Link;

class ParticipantUserList extends Widget
{
    /**
     * @var Message
     */
    public $message;

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
        $users = $this->message->users;
        $userCount = count($users);

        $result = '';
        foreach ($users as $u => $user) {
            $result .= Html::encode($user->displayName);
            $result .= $u < $userCount - 1 ? ', ' : '';
        }

        return $result;
    }
}