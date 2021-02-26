<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2020 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\mail\helpers;

use humhub\modules\mail\models\Message;
use humhub\modules\mail\models\MessageEntry;
use humhub\modules\mail\models\MessageTag;
use humhub\modules\rest\definitions\UserDefinitions;


/**
 * Class RestDefinitions
 */
class RestDefinitions
{

    public static function getMessage(Message $message)
    {
        return [
            'id' => $message->id,
            'title' => $message->title,
            'created_at' => $message->created_at,
            'created_by' => $message->created_by,
            'updated_at' => $message->updated_at,
            'updated_by' => $message->updated_by,
        ];
    }

    public static function getMessageEntry(MessageEntry $entry)
    {
        return [
            'id' => $entry->id,
            'user_id' => $entry->user_id,
            'file_id' => $entry->file_id,
            'content' => $entry->content,
            'created_at' => $entry->created_at,
            'created_by' => $entry->created_by,
            'updated_at' => $entry->updated_at,
            'updated_by' => $entry->updated_by,
        ];
    }

    public static function getMessageUsers(Message $message)
    {
        $messageUsers = [];
        foreach ($message->getUsers()->all() as $messageUser) {
            $messageUsers[] = UserDefinitions::getUser($messageUser);
        }
        return $messageUsers;
    }

    public static function getMessageTag(MessageTag $tag)
    {
        return [
            'id' => $tag->id,
            'name' => $tag->name,
            'sort_order' => $tag->sort_order,
            'color' => $tag->color,
        ];
    }
}

