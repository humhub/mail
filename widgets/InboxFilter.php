<?php


namespace humhub\modules\mail\widgets;


use humhub\modules\mail\models\forms\InboxFilterForm;
use humhub\widgets\JsWidget;

class InboxFilter extends JsWidget
{
    public $jsWidget = 'mail.inbox.Filter';

    public $id = 'mail-filter-root';


    /**
     * @var InboxFilterForm
     */
    public $model;

    public function run()
    {
        return $this->render('inboxFilter', ['options' => $this->getOptions(), 'model' => $this->model]);
    }
}