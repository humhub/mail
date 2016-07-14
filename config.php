<?php

use humhub\modules\user\models\User;
use humhub\widgets\TopMenu;
use humhub\widgets\NotificationArea;
use humhub\modules\user\widgets\ProfileHeaderControls;

return [
    'id' => 'mail',
    'class' => 'humhub\modules\mail\Module',
    'namespace' => 'humhub\modules\mail',
    'events' => [
        ['class' => User::className(), 'event' => User::EVENT_BEFORE_DELETE, 'callback' => ['humhub\modules\mail\Events', 'onUserDelete']],
        ['class' => TopMenu::className(), 'event' => TopMenu::EVENT_INIT, 'callback' => ['humhub\modules\mail\Events', 'onTopMenuInit']],
        ['class' => NotificationArea::className(), 'event' => NotificationArea::EVENT_INIT, 'callback' => ['humhub\modules\mail\Events', 'onNotificationAddonInit']],
        ['class' => ProfileHeaderControls::className(), 'event' => ProfileHeaderControls::EVENT_INIT, 'callback' => ['humhub\modules\mail\Events', 'onProfileHeaderControlsInit']],
    ],
];
?>