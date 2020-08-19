<?php

namespace humhub\modules\mail\helpers;

use humhub\modules\mail\models\Message;

class Url extends \yii\helpers\Url
{
    public static function toEditConversationTags(Message $message)
    {
        return static::to(['/mail/tag/edit-conversation', 'messageId' => $message->id]);
    }

    public static function toUpdateInbox()
    {
        return static::to(['/mail/inbox/index']);
    }

    public static function toConversationUserList(Message $message)
    {
        return static::to(['/mail/mail/user-list', 'id' => $message->id]);
    }

    public static function toLeaveConversation(Message $message)
    {
        return static::to(["/mail/mail/leave", 'id' => $message->id]);
    }
}