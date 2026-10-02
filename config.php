<?php

use humhub\commands\IntegrityController;
use humhub\modules\user\models\User;
use humhub\modules\user\widgets\HeaderControlsMenu;
use humhub\modules\user\widgets\PeopleDirectory;
use humhub\widgets\TopMenu;
use humhub\widgets\NotificationArea;

return [
    'id' => 'mail',
    'class' => 'humhub\modules\mail\Module',
    'namespace' => 'humhub\modules\mail',
    // HTTP API (see docs/develop/concept-api.md of the core).
    'urlManagerRules' => [
        ['pattern' => 'api/v2/mail/recipient-states', 'route' => 'mail/api/recipient/states', 'verb' => ['GET', 'HEAD']],
    ],
    'events' => [
        ['class' => User::class, 'event' => User::EVENT_BEFORE_DELETE, 'callback' => ['humhub\modules\mail\Events', 'onUserDelete']],
        ['class' => TopMenu::class, 'event' => TopMenu::EVENT_INIT, 'callback' => ['humhub\modules\mail\Events', 'onTopMenuInit']],
        ['class' => NotificationArea::class, 'event' => NotificationArea::EVENT_INIT, 'callback' => ['humhub\modules\mail\Events', 'onNotificationAddonInit']],
        ['class' => HeaderControlsMenu::class, 'event' => HeaderControlsMenu::EVENT_INIT, 'callback' => ['humhub\modules\mail\Events', 'onProfileHeaderControlsMenuInit']],
        ['class' => PeopleDirectory::class, 'event' => PeopleDirectory::EVENT_INIT, 'callback' => ['humhub\modules\mail\Events', 'onPeopleDirectoryInit']],
        ['class' => IntegrityController::class, 'event' => IntegrityController::EVENT_ON_RUN, 'callback' => ['humhub\modules\mail\Events', 'onIntegrityCheck']],
        ['class' => 'humhub\modules\rest\Module', 'event' => 'restApiAddRules', 'callback' => ['humhub\modules\mail\Events', 'onRestApiAddRules']],
        ['class' => 'humhub\widgets\MetaSearchWidget', 'event' => 'init', 'callback' => ['humhub\modules\mail\Events', 'onMetaSearchWidgetInit']],
        ['class' => 'humhub\modules\fcmPush\services\MessagingService', 'event' => 'pushNotificationCount', 'callback' => ['humhub\modules\mail\Events', 'onPushNotificationCount']], // humhub\modules\fcmPush\services\MessagingService::EVENT_NOTIFICATION_COUNT
    ],
];
