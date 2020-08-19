<?php

use humhub\modules\mail\assets\MailAsset;
use humhub\modules\mail\permissions\StartConversation;
use humhub\modules\mail\widgets\NewMessageButton;
use humhub\widgets\Link;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this \humhub\modules\ui\view\components\View */

MailAsset::register($this);

$canStartConversation = Yii::$app->user->can(StartConversation::class);

?>
<div class="btn-group">
    <a href="#" id="icon-messages" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-envelope"></i></a>
    <span id="badge-messages" style="display:none;" class="label label-danger label-notification"></span>
    <ul id="dropdown-messages" class="dropdown-menu">
        <li class="dropdown-header">
            <div class="arrow"></div>
            <?= Yii::t('MailModule.base', 'Conversations') ?>
            <?= ($canStartConversation)
                ? NewMessageButton::widget([ 'id' => 'create-message-button', 'right' => true])
                : '' ?>
        </li>
        <ul class="media-list">
            <li id="loader_messages"></li>
        </ul>
        <li>
            <div class="dropdown-footer">
                <a class="btn btn-default col-md-12" href="<?= Url::to(['/mail/mail/index']); ?>">
                    <?= Yii::t('MailModule.widgets_views_mailNotification', 'Show all messages'); ?>
                </a>
            </div>
        </li>
    </ul>
</div>

