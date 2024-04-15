<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\mail\search;

use humhub\interfaces\MetaSearchResultInterface;
use humhub\modules\content\widgets\richtext\RichText;
use humhub\modules\mail\helpers\Url;
use humhub\modules\mail\models\MessageEntry;
use humhub\modules\ui\icon\widgets\Icon;
use humhub\modules\user\models\User;
use humhub\modules\user\widgets\Image;
use Yii;

/**
 * Search Record for Message Entry
 */
class SearchRecord implements MetaSearchResultInterface
{
    public ?MessageEntry $entry = null;

    public function __construct(MessageEntry $entry)
    {
        $this->entry = $entry;
    }

    /**
     * @inheritdoc
     */
    public function getImage(): string
    {
        $author = $this->entry->createdBy;
        if ($author instanceof User) {
            return Image::widget([
                'user' => $author,
                'width' => 36,
                'link' => false,
                'hideOnlineStatus' => true,
            ]);
        } else {
            return Icon::get('envelope');
        }
    }

    /**
     * @inheritdoc
     */
    public function getTitle(): string
    {
        return RichText::output($this->entry->content, ['record' => $this->entry]);
    }

    /**
     * @inheritdoc
     */
    public function getDescription(): string
    {
        $description = [];

        $author = $this->entry->createdBy;
        if ($author instanceof User) {
            $description[] = $author->getDisplayName();
        }

        if ($this->entry->created_at !== null) {
            $description[] = Yii::$app->formatter->asDate($this->entry->created_at, 'short');
        }

        $description[] = $this->entry->message->title;

        return implode(' &middot; ', $description);
    }

    /**
     * @inheritdoc
     */
    public function getUrl(): string
    {
        return Url::toMessenger($this->entry->message);
    }
}
