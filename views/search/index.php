<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\assets\CardsAsset;
use humhub\modules\mail\widgets\SearchFilters;
use yii\helpers\Url;

CardsAsset::register($this);
?>
<div class="container" data-action-component="stream.SimpleStream" data-ui-init>
    <div class="panel panel-default">
        <div class="panel-heading">
            <?= Yii::t('MailModule.search', '<strong>Search</strong> messages') ?>
        </div>

        <div class="panel-body">
            <?= SearchFilters::widget(['data' => ['action-url' => Url::to(['/mail/search/results'])]]) ?>
        </div>
    </div>

    <div data-stream-content></div>
</div>
