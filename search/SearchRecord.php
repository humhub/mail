<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\mail\search;

use humhub\interfaces\MetaSearchResultInterface;
use humhub\modules\mail\helpers\Url;
use humhub\modules\mail\models\UserMessage;
use humhub\modules\mail\widgets\InboxMessagePreview;
use humhub\modules\ui\icon\widgets\Icon;
use humhub\modules\user\models\User;
use humhub\modules\user\widgets\Image;

/**
 * Search Record for Message Entry
 */
class SearchRecord implements MetaSearchResultInterface
{
    public ?InboxMessagePreview $preview = null;

    public function __construct(UserMessage $userMessage)
    {
        $this->preview = new InboxMessagePreview(['userMessage' => $userMessage]);
    }

    /**
     * @inheritdoc
     */
    public function getImage(): string
    {
        $lastParticipant = $this->preview->lastParticipant();

        if ($lastParticipant instanceof User) {
            return Image::widget([
                'user' => $lastParticipant,
                'width' => 36,
                'link' => false,
                'hideOnlineStatus' => true,
            ]);
        }

        return Icon::get('envelope');
    }

    /**
     * @inheritdoc
     */
    public function getTitle(): string
    {
        return $this->preview->getMessage()->title;
    }

    /**
     * @inheritdoc
     */
    public function getDescription(): string
    {
        return $this->preview->getMessagePreview();
    }

    /**
     * @inheritdoc
     */
    public function getUrl(): string
    {
        return Url::toMessenger($this->preview->getMessage());
    }
}
