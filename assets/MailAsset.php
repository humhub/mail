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
    public $defer = true;

    public $publishOptions = [
        'forceCopy' => true
    ];

    public $sourcePath = '@mail/resources';

    public $js = [
        'js/humhub.mail.ConversationView.js',
        'js/humhub.mail.ConversationViewEntry.js',
        'js/humhub.mail.inbox.js',
        'js/humhub.mail.conversation.js',
        'js/humhub.mail.notification.js'
    ];

    public $css = ['css/humhub.mail.css'];

    public static function register($view)
    {
        $view->registerJsConfig([
            'mail.notification' => [
                'url' => [
                    'count' => Url::to(['/mail/mail/get-new-message-count-json']),
                    'list' => Url::to(['/mail/mail/notification-list']),
                ]
            ],
            'mail.conversation' => [
                'url' => [
                    'seen' => Url::to(['/mail/mail/seen'])
                ]
            ]
        ]);

        return parent::register($view);
    }
}