<?php
namespace humhub\modules\mail\assets;

use yii\helpers\Url;
use yii\web\AssetBundle;

/**
 * Created by PhpStorm.
 * User: kingb
 * Date: 29.07.2018
 * Time: 08:19
 */

class MailAsset extends AssetBundle
{
    public $publishOptions = [
        'forceCopy' => false
    ];

    public $sourcePath = '@mail/resources';

    public $js = [
        'js/humhub.mail.wall.js',
        'js/humhub.mail.js'
    ];

    public $css = ['css/humhub.mail.css'];

    public static function register($view)
    {
        $view->registerJsConfig([
            'mail' => [
                'url' => [
                    'count' => Url::to(['/mail/mail/get-new-message-count-json']),
                    'list' => Url::to(['/mail/mail/notification-list']),
                ]
            ],
            'mail.wall' => [
                'url' => [
                    'seen' => Url::to(['/mail/mail/seen'])
                ]
            ]
        ]);

        return parent::register($view);
    }


}