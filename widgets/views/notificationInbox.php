<?php

use humhub\modules\mail\assets\MailNotificationAsset;
use humhub\modules\mail\helpers\Url;
use humhub\modules\mail\permissions\StartConversation;
use humhub\modules\mail\widgets\NewMessageButton;
use humhub\widgets\bootstrap\Badge;

/* @var $this \humhub\modules\ui\view\components\View */

MailNotificationAsset::register($this);

$canStartConversation = Yii::$app->user->can(StartConversation::class);

?>
<div class="btn-group">
    <a href="#" id="icon-messages" data-bs-toggle="dropdown"><i class="fa fa-envelope"></i></a>
    <?= Badge::danger()->id('badge-messages')->cssClass('d-none') ?>
    <ul id="dropdown-messages" class="dropdown-menu mail-inbox-messages">
        <li>
            <div class="dropdown-header">
                <div class="arrow"></div>
                <?= Yii::t('MailModule.base', 'Conversations') ?>
                <div class="dropdown-header-actions">
                    <?= $canStartConversation
                        ? NewMessageButton::widget(['id' => 'create-message-button', 'icon' => 'plus', 'label' => ''])
                        : '' ?>
                </div>
            </div>
        </li>
        <li>
            <div class="dropdown-item hh-list">
                <div id="loader_messages"></div>
            </div>
        </li>
        <li>
            <div class="dropdown-footer">
                <a class="btn btn-light col-lg-12" href="<?= Url::toMessenger() ?>">
                    <?= Yii::t('MailModule.base', 'Show all messages') ?>
                </a>
            </div>
        </li>
    </ul>
</div>
