<?php

namespace humhub\modules\mail\helpers;

use humhub\modules\mail\models\Message;
use humhub\modules\mail\models\MessageEntry;

class Url extends \yii\helpers\Url
{
    const ROUTE_SEARCH_TAG = '/mail/tag/search';

    public static function toCreateConversation($userGuid = null)
    {
        $route = $userGuid ? ['/mail/mail/create', 'userGuid' => $userGuid] : ['/mail/mail/create'];
        return static::to($route);
    }

    public static function toDeleteMessageEntry(MessageEntry $entry)
    {
        return static::to(['/mail/mail/delete-entry', 'id' => $entry->id]);
    }

    public static function toLoadMessage()
    {
        return static::to(['/mail/mail/show']);
    }

    public static function toUpdateMessage()
    {
        return static::to(['/mail/mail/update']);
    }

    public static function toEditMessageEntry(MessageEntry $entry)
    {
        return static::to( ['/mail/mail/edit-entry', 'id' => $entry->id]);
    }

    public static function toEditConversationTags(Message $message)
    {
        return static::to(['/mail/tag/edit-conversation', 'messageId' => $message->id]);
    }

    public static function toManageTags()
    {
        return static::to(['/mail/tag/manage']);
    }

    public static function toAddTag()
    {
        return static::to(['/mail/tag/add']);
    }

    public static function toEditTag($id)
    {
        return static::to(['/mail/tag/edit', 'id' => $id]);
    }

    public static function toDeleteTag($id)
    {
        return static::to(['/mail/tag/delete', 'id' => $id]);
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

    public static function toMessenger(Message $message = null, $scheme = false)
    {
        $route = $message ? ['/mail/mail/index', 'id' => $message->id] : ['/mail/mail/index'];
        return static::to($route, $scheme);
    }

    public static function toConfig()
    {
        return static::to(['/mail/config']);
    }

    public static function toMessageCountUpdate()
    {
        return static::to(['/mail/mail/get-new-message-count-json']);
    }

    public static function toNotificationList()
    {
        return static::to(['/mail/mail/notification-list']);
    }

    public static function toNotificationSeen()
    {
        return static::to(['/mail/mail/seen']);
    }

    public static function toSearchNewParticipants(Message $message = null)
    {
        $route = $message ? ['/mail/mail/search-user', 'id' => $message->id] : ['/mail/mail/search-user'];
        return static::to($route);
    }

    public static function toAddParticipant(Message $message)
    {
        return static::to(['/mail/mail/add-user', 'id' => $message->id]);
    }

    public static function toReply(Message $message)
    {
        return static::to(['/mail/mail/reply', 'id' => $message->id]);
    }

    public static function toInboxLoadMore()
    {
        return static::to(['/mail/inbox/load-more']);
    }

    public static function toInboxUpdateEntries()
    {
        return static::to(['/mail/inbox/update-entries']);
    }

    public static function toLoadMoreMessages()
    {
        return static::to(['/mail/mail/load-more']);
    }
}