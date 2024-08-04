<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use yii\bootstrap\Html;

/* @var array $options */
?>
<div class="form-group">
    <?= Html::textInput(null, $options['value'] ?? null, $options) ?>
</div>
