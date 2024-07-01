<?php

namespace humhub\modules\mail\assets;

use humhub\components\assets\AssetBundle;
use humhub\modules\mail\helpers\Url;

/**
 * Created by PhpStorm.
 * User: kingb
 * Date: 29.07.2018
 * Time: 08:19
 */
class MailNotificationAsset extends AssetBundle
{
    public $sourcePath = '@mail/resources/js';

    public $publishOptions = [
        'forceCopy' => false,
    ];

    public $js = [
        'humhub.mail.notification.min.js',
    ];

    public $depends = [
        MailStyleAsset::class,
    ];

    public static function register($view)
    {
        $view->registerJsConfig([
            'mail.notification' => [
                'url' => [
                    'count' => Url::toMessageCountUpdate(),
                    'list' => Url::toNotificationList(),
                ],
            ],
        ]);

        return parent::register($view);
    }
}
