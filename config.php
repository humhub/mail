<?php

use humhub\commands\IntegrityController;
use humhub\modules\user\models\User;
use humhub\modules\user\widgets\HeaderControlsMenu;
use humhub\widgets\TopMenu;
use humhub\widgets\NotificationArea;

return [
    'id' => 'mail',
    'class' => 'humhub\modules\mail\Module',
    'namespace' => 'humhub\modules\mail',
    'events' => [
        ['class' => User::class, 'event' => User::EVENT_BEFORE_DELETE, 'callback' => ['humhub\modules\mail\Events', 'onUserDelete']],
        ['class' => TopMenu::class, 'event' => TopMenu::EVENT_INIT, 'callback' => ['humhub\modules\mail\Events', 'onTopMenuInit']],
        ['class' => NotificationArea::class, 'event' => NotificationArea::EVENT_INIT, 'callback' => ['humhub\modules\mail\Events', 'onNotificationAddonInit']],
        ['class' => HeaderControlsMenu::class, 'event' => HeaderControlsMenu::EVENT_INIT, 'callback' => ['humhub\modules\mail\Events', 'onProfileHeaderControlsMenuInit']],
        ['class' => IntegrityController::class, 'event' => IntegrityController::EVENT_ON_RUN, 'callback' => ['humhub\modules\mail\Events', 'onIntegrityCheck']],
        ['class' => 'humhub\modules\rest\Module', 'event' => 'restApiAddRules', 'callback' => ['humhub\modules\mail\Events', 'onRestApiAddRules']],
        ['class' => 'humhub\widgets\MetaSearchWidget', 'event' => 'init', 'callback' => ['humhub\modules\mail\Events', 'onMetaSearchWidgetInit']],
    ],
];
