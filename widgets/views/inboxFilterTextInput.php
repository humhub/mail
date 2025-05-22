<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use yii\bootstrap\Html;

/* @var array $options */
?>
<div class="mb-3">
    <?= Html::textInput(null, $options['value'] ?? null, $options) ?>
</div>
