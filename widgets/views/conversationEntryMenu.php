<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2023 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

/* @var array $menus */
?>
<div class="conversation-menu">
    <?php foreach ($menus as $menu) : ?>
        <div class="conversation-menu-item">
            <?= $menu ?>
        </div>
    <?php endforeach; ?>
</div>