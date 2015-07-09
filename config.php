<?php

use humhub\modules\user\models\User;
use humhub\widgets\TopMenu;
use humhub\widgets\NotificationArea;
use humhub\modules\user\widgets\ProfileHeaderControls;

return [
    'id' => 'mail',
    'class' => 'module\mail\Module',
    'events' => [
        ['class' => User::className(9), 'event' => User::EVENT_BEFORE_DELETE, 'callback' => ['module\mail\Events', 'onUserDelete']],
        ['class' => TopMenu::className(), 'event' => TopMenu::EVENT_INIT, 'callback' => ['module\mail\Events', 'onTopMenuInit']],
        ['class' => NotificationArea::className(), 'event' => NotificationArea::EVENT_INIT, 'callback' => ['module\mail\Events', 'onNotificationAddonInit']],
        ['class' => ProfileHeaderControls::className(), 'event' => ProfileHeaderControls::EVENT_INIT, 'callback' => ['module\mail\Events', 'onProfileHeaderControlsInit']],
    ],
];
?>