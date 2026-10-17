<?php

namespace humhub\modules\mail\widgets;

use humhub\modules\mail\helpers\Url;
use humhub\modules\mail\models\Message;
use humhub\modules\mail\models\MessageTag;
use humhub\widgets\Icon;
use humhub\widgets\bootstrap\Badge;
use humhub\widgets\bootstrap\Link;

class ConversationTagBadge extends Badge
{
    public static function get(MessageTag $tag)
    {
        return static::light($tag->name)->icon('star-filled')
            ->withLink(
                Link::withAction(null, 'mail.inbox.setTagFilter')
                    ->options([
                        'data-tag-id' => $tag->id,
                        'data-tag-name' => $tag->name,
                        'data-tag-image' => Icon::get('star-filled'),
                    ])
                    ->encodeLabel(false),
            );
    }

    public static function getEditConversationTagBadge(Message $message, $icon = 'pencil')
    {
        return static::light()->icon($icon)
            ->withLink(
                Link::withAction(null, 'ui.modal.load', Url::toEditConversationTags($message))
                ->encodeLabel(false),
            );
    }
}
