<?php

namespace humhub\modules\mail\assets;

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
        'forceCopy' => false,
    ];

    public $js = [
        'humhub.mail.messenger.bundle.min.js',
    ];

    public $depends = [
        MailNotificationAsset::class
    ];
}
