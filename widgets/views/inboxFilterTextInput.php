<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */


/* @var array $options */

use humhub\helpers\Html;

?>
<div class="mb-3">
    <?= Html::textInput(null, $options['value'] ?? null, $options) ?>
</div>
