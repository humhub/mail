<?php


namespace humhub\modules\mail\widgets;


use humhub\modules\mail\helpers\Url;
use humhub\widgets\Link;
use Yii;

class ManageTagsLink extends Link
{
    public function init()
    {
        parent::init();
        $this->setType(static::TYPE_NONE)
            ->setText(Yii::t('MailModule.base', 'Manage Tags'))
            ->link(Url::toManageTags())
            ->icon('gear')->right()->cssClass('manage-tags-link');
    }

}