<?php

namespace humhub\modules\mail\widgets;

use humhub\modules\mail\helpers\Url;
use humhub\modules\mail\models\UserMessageTag;
use humhub\modules\mail\models\MessageTag;
use humhub\widgets\form\BasePicker;
use humhub\widgets\Icon;
use Yii;

class ConversationTagPicker extends BasePicker
{
    /**
     * Tabler icon name of a tag, the image of a picker item
     */
    public const ICON = 'star-filled';

    /**
     * @inheritDoc
     */
    public $defaultRoute = Url::ROUTE_SEARCH_TAG;

    /**
     * @inheritDoc
     */
    public $addOptions = true;

    /**
     * @inheritDoc
     */
    public $itemClass = MessageTag::class;

    public function init()
    {
        $this->defaultResults = MessageTag::findByUser(Yii::$app->user->id)->all();
    }

    /**
     * Used to retrieve the option text of a given $item.
     *
     * @param MessageTag $item selected item
     * @return string item option text
     */
    protected function getItemText($item)
    {
        return $item->name;
    }

    /**
     * Used to retrieve the option image url of a given $item.
     *
     * @param UserMessageTag $item selected item
     * @return string the Tabler icon name of a tag
     */
    protected function getItemImage($item)
    {
        return static::ICON;
    }

    public static function getIcon()
    {
        return Icon::get('star-filled')->asString();
    }
}
