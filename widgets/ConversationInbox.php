<?php


namespace humhub\modules\mail\widgets;


use humhub\modules\mail\helpers\Url;
use humhub\modules\mail\models\forms\InboxFilterForm;
use humhub\widgets\JsWidget;

class ConversationInbox extends JsWidget
{
    /**
     * @inheritDoc
     */
    public $id = 'inbox';

    /**
     * @inheritDoc
     */
    public $jsWidget = 'mail.inbox.ConversationList';

    /**
     * @var InboxFilterForm
     */
    public $filter;

    /**
     * @inheritDoc
     */
    public $init = true;

    /**
     * @inheritDoc
     */
    public $pageSize = 30;

    /**
     * @inheritDoc
     */
    public function run()
    {
        $this->filter->apply();

        return $this->render('inbox', [
            'options' => $this->getOptions(),
            'userMessages' =>  $this->filter->query->limit($this->pageSize)->all(),
        ]);
    }

    public function getData()
    {
        return [
            'widget-reload-url' => Url::toUpdateInbox()
        ];
    }

    public function getAttributes()
    {
        return [
            'class' => 'media-list'
        ];
    }

}