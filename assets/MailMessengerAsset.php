<?php

namespace humhub\modules\mail\assets;

use humhub\components\assets\AssetBundle;

/**
 * Created by PhpStorm.
 * User: kingb
 * Date: 29.07.2018
 * Time: 08:19
 */
class MailMessengerAsset extends AssetBundle
{
    public $sourcePath = '@mail/resources/js';

    public $publishOptions = [
        'forceCopy' => false,
    ];

    public $js = [
        'humhub.mail.messenger.bundle.js',
    ];

    public $depends = [
        MailNotificationAsset::class
    ];
}