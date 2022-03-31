<?php

namespace humhub\modules\mail\widgets;

use humhub\modules\friendship\widgets\FriendshipButton;
use humhub\modules\mail\Module;
use humhub\modules\user\widgets\PeopleActionButtons;
use humhub\modules\user\widgets\UserFollowButton;
use humhub\widgets\BootstrapComponent;
use Yii;

class PeopleActionButtonsMail extends PeopleActionButtons
{
    public function run()
    {
        $html = UserFollowButton::widget([
            'user' => $this->user,
            'followOptions' => ['class' => 'btn btn-primary btn-sm'],
            'unfollowOptions' => ['class' => 'btn btn-primary btn-sm active'],
        ]);

        $html .= FriendshipButton::widget([
            'user' => $this->user,
            'options' => [
                'friends' => ['attrs' => ['class' => 'btn btn-info btn-sm active']],
                'addFriend' => ['attrs' => ['class' => 'btn btn-info btn-sm']],
                'acceptFriendRequest' => ['attrs' => ['class' => 'btn btn-info btn-sm active'], 'togglerClass' => 'btn btn-info btn-sm active'],
                'cancelFriendRequest' => ['attrs' => ['class' => 'btn btn-info btn-sm active']],
            ],
        ]);

        /** @var Module $module */
        $module = Yii::$app->getModule('mail');

        if ($module->showSendMessageButtonInPeopleCards) {
            $html .= NewMessageButton::widget([
                'guid' => $this->user->guid,
                'type' => BootstrapComponent::TYPE_DEFAULT,
            ]);
        }

        if (trim($html) === '') {
            return '';
        }

        return str_replace('{buttons}', $html, $this->template);
    }

}
