<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2023 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\modules\mail\models\MessageEntry;
use humhub\modules\mail\widgets\ConversationDateBadge;
use humhub\modules\mail\widgets\ConversationStateBadge;

/* @var $entry MessageEntry */
/* @var $showDateBadge bool */
?>
<?php if ($showDateBadge) : ?>
    <?= ConversationDateBadge::widget(['entry' => $entry]) ?>
<?php endif; ?>

<?= ConversationStateBadge::widget(['entry' => $entry]) ?>