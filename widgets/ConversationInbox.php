<?php


namespace humhub\modules\mail\widgets;


use humhub\modules\mail\helpers\Url;
use humhub\modules\mail\models\forms\InboxFilterForm;
use humhub\modules\mail\models\UserMessage;
use humhub\modules\mail\Module;
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
    public $pageSize = 7;

    /**
     * @var UserMessage[]
     */
    private $result;

    public function init()
    {
        parent::init();
        $this->result = $this->filter->getPage();
    }

    /**
     * @inheritDoc
     */
    public function run()
    {
        return $this->render('inbox', [
            'options' => $this->getOptions(),
            'userMessages' =>  $this->result
        ]);
    }

    public function getData()
    {
        return [
            'widget-reload-url' => Url::toUpdateInbox(),
            'load-more-url' => Url::toInboxLoadMore(),
            'update-entries-url' => Url::toInboxUpdateEntries(),
            'is-last' => $this->filter->wasLastPage()
        ];
    }

    public function getAttributes()
    {
        return [
            'class' => 'media-list'
        ];
    }

}