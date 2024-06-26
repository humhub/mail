<?php

namespace humhub\modules\mail\widgets;

use humhub\components\Widget;
use humhub\libs\Html;
use humhub\modules\mail\helpers\Url;
use humhub\modules\mail\models\Message;
use humhub\modules\user\models\User;
use humhub\widgets\Link;
use Yii;

class ParticipantUserList extends Widget
{
    public Message $message;

    /**
     * @var array $limits Limit users depending on screen size:
     *      - key - Number of visible users
     *      - value 1 - Style class applied for number of other users
     *      - value 2 - Style class applied for usernames
     */
    public array $limits = [
        2 => ['visible-xs-inline', 'hidden-xs'],
        6 => ['visible-md-inline visible-sm-inline', 'hidden-md hidden-sm'],
        8 => ['visible-lg-inline', ''],
    ];

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
        $maxLimit = max(array_keys($this->limits));
        $users = $this->message->getUsers()->limit($maxLimit)->all();
        $userTotalCount = $this->message->getUsers()->count();

        $usernames = '';
        foreach ($users as $u => $user) {
            $usernames .= $this->renderUserName($user, $u, $userTotalCount);
        }

        return $usernames . $this->renderOtherCount($userTotalCount);
    }

    private function getUserNameClass(int $itemIndex): string
    {
        $classes = [];
        foreach ($this->limits as $limit => $class) {
            if ($itemIndex >= $limit) {
                $classes[] = $class[1];
            }
        }
        return implode(' ', $classes);
    }

    private function renderUserName(User $user, int $itemIndex, int $count): string
    {
        $text = Html::encode($user->displayName) . ($itemIndex < $count - 1 ? ', ' : '');
        $class = $this->getUserNameClass($itemIndex);

        return $class === '' ? $text : Html::tag('span', $text, ['class' => $class]);
    }

    private function renderOtherCount(int $count): string
    {
        $result = '';
        foreach ($this->limits as $limit => $class) {
            $otherCount = $count - $limit;
            if ($otherCount > 0) {
                $otherCountText = '+' . Yii::t('MailModule.base', '{n,plural,=1{# other} other{# others}}', ['n' => $otherCount]);
                $result .= Html::tag('span', $otherCountText, ['class' => $class[0]]);
            }
        }

        return $result;
    }
}
