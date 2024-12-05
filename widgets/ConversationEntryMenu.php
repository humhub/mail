<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2023 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\mail\widgets;

use humhub\components\Widget;
use humhub\modules\mail\helpers\Url;
use humhub\modules\mail\models\MessageEntry;
use humhub\widgets\ModalButton;
use Yii;

/**
 * @property-read string $username
 */
class ConversationEntryMenu extends Widget
{
    public MessageEntry $entry;

    private array $menus = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->initMenus();
    }

    public function initMenus()
    {
        if ($this->entry->canEdit()) {
            $this->menus[] = ModalButton::none()->link()
                ->icon('pencil')
                ->tooltip(Yii::t('MailModule.base', 'Edit'))
                ->load(Url::toEditMessageEntry($this->entry))
                ->cssClass('conversation-edit-button time badge');
        }
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        if (empty($this->menus)) {
            return '';
        }

        return $this->render('conversationEntryMenu', ['menus' => $this->menus]);
    }
}
