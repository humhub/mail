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
                <h6><?= Yii::t('MailModule.base', 'Conversations') ?></h6>
                <?= $canStartConversation
                    ? NewMessageButton::widget(['id' => 'create-message-button', 'icon' => 'plus', 'label' => ''])
                    : '' ?>
            </div>
        </li>
        <li>
            <div class="dropdown-item">
                <div id="loader_messages"></div>
            </div>
        </li>
        <li>
            <a class="dropdown-item btn btn-light col-lg-12" href="<?= Url::toMessenger() ?>">
                <?= Yii::t('MailModule.base', 'Show all messages') ?>
            </a>
        </li>
    </ul>
</div>
