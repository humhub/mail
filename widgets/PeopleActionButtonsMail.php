<?php

namespace humhub\modules\mail\widgets;

use humhub\modules\mail\Module;
use humhub\modules\ui\icon\widgets\Icon;
use humhub\modules\user\widgets\PeopleActionButtons;
use humhub\widgets\BootstrapComponent;
use Yii;

class PeopleActionButtonsMail extends PeopleActionButtons
{
    public function run()
    {
        $html = $this->addFollowButton();
        $html .= $this->addFriendshipButton();

        /** @var Module $module */
        $module = Yii::$app->getModule('mail');

        if ($module->showSendMessageButtonInPeopleCards) {
            $html .= NewMessageButton::widget([
                'label' => Icon::get('send') . ' ' . Yii::t('MailModule.base', 'Message'),
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
