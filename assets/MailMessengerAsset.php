<?php
namespace humhub\modules\mail\assets;

use humhub\modules\mail\helpers\Url;
use yii\web\AssetBundle;

/**
 * Created by PhpStorm.
 * User: kingb
 * Date: 29.07.2018
 * Time: 08:19
 */

class MailMessengerAsset extends AssetBundle
{
    public $defer = true;

    public $sourcePath = '@mail/resources/js';

    public $publishOptions = [
        'forceCopy' => true,
        'only' => [
            'humhub.mail.messenger.bundle.js'
        ]
    ];

    public $js = [
        'humhub.mail.messenger.bundle.js',
    ];

    public $depends = [
        MailNotificationAsset::class
    ];

    public static function register($view)
    {
        $view->registerJsConfig([
            'mail.conversation' => [
                'url' => [
                    'seen' => Url::toNotificationSeen()
                ]
            ]
        ]);

        return parent::register($view);
    }
}