<?php

namespace humhub\modules\mail\assets;

use humhub\components\assets\AssetBundle;

/**
 * Created by PhpStorm.
 * User: kingb
 * Date: 29.07.2018
 * Time: 08:19
 */
class MailStyleAsset extends AssetBundle
{
    public $sourcePath = '@mail/resources/css';

    public $publishOptions = [
        'forceCopy' => false,
    ];

    public $css = [
        'humhub.mail.min.css',
    ];
}
