<?php

use humhub\commands\IntegrityController;
use humhub\modules\user\models\User;
use humhub\widgets\TopMenu;
use humhub\widgets\NotificationArea;
use humhub\modules\user\widgets\ProfileHeaderControls;

return [
    'id' => 'mail',
    'class' => 'humhub\modules\mail\Module',
    'namespace' => 'humhub\modules\mail',
    'events' => [
        ['class' => User::class, 'event' => User::EVENT_BEFORE_DELETE, 'callback' => ['humhub\modules\mail\Events', 'onUserDelete']],
        ['class' => TopMenu::class, 'event' => TopMenu::EVENT_INIT, 'callback' => ['humhub\modules\mail\Events', 'onTopMenuInit']],
        ['class' => NotificationArea::class, 'event' => NotificationArea::EVENT_INIT, 'callback' => ['humhub\modules\mail\Events', 'onNotificationAddonInit']],
        ['class' => ProfileHeaderControls::class, 'event' => ProfileHeaderControls::EVENT_INIT, 'callback' => ['humhub\modules\mail\Events', 'onProfileHeaderControlsInit']],
        ['class' => IntegrityController::class, 'event' => IntegrityController::EVENT_ON_RUN, 'callback' => ['humhub\modules\mail\Events', 'onIntegrityCheck']],
        ['class' => 'humhub\modules\rest\Module', 'event' => 'restApiAddRules', 'callback' => ['humhub\modules\mail\Events', 'onRestApiAddRules']],
        ['class' => 'humhub\widgets\MetaSearchWidget', 'event' => 'init', 'callback' => ['humhub\modules\mail\Events', 'onMetaSearchWidgetInit']],
    ],
];
