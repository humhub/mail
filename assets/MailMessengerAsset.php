<?php

namespace humhub\modules\mail\assets;

use humhub\components\assets\AssetBundle;

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
        MailNotificationAsset::class,
    ];
}
