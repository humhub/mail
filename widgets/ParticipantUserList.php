<?php


namespace humhub\modules\mail\widgets;


use humhub\components\Widget;
use humhub\libs\Html;
use humhub\modules\mail\helpers\Url;
use humhub\modules\mail\models\Message;
use humhub\modules\user\models\User;
use Yii;

class ParticipantUserList extends Widget
{
    /**
     * @var Message
     */
    public $message;

    /**
     * @var User first user
     */
    public $user;

    /**
     * @var array
     */
    public $options = [];

    /**
     * @var array
     */
    public $linkOptions = [];

    public function run()
    {
        if(empty($this->message->users)) {
            return '';
        }

         $result = Html::beginTag('span', $this->options);
         $result .= Yii::t('MailModule.base','with').'&nbsp;';
         $result .= Html::beginTag('a', array_merge($this->getDefaultLinkOptions(), $this->linkOptions));
         $result .= $this->renderUserList();
         $result .= Html::endTag('a');
         $result .= Html::endTag('span');

         return $result;
    }

    private function renderUserList()
    {
        $userCount = count($this->message->users);
        $result = '';

        if($userCount === 2) {
            $result .= Html::encode($this->message->users[0]->displayName);
            $result .= ', '. Html::encode($this->message->users[1]->displayName);
        } else {
            $result .= Html::encode($this->getFirstUser()->displayName);
            $result .= ($userCount > 1)
                ? ', +'.Yii::t('MailModule.base', '{n,plural,=1{# other} other{# others}}', ['n' => $userCount - 1])
                : '';
        }
        return $result;
    }

    private function getDefaultLinkOptions()
    {
        return  [
            'href'=> '#',
            'data-action-click' => 'ui.modal.load',
            'data-action-url' => Url::toConversationUserList($this->message),
            'style' => ['color' =>  $this->view->theme->variable('info')]
        ];
    }

    private function getFirstUser()
    {
        if($this->user) {
            return $this->user;
        }

        foreach ($this->message->users as $participant) {
            if(!$participant->is(Yii::$app->user->getIdentity())) {
                return $participant;
            }
        }

        return $this->message->users[0];
    }
}