<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace module\mail;

use yii\web\AssetBundle;

class Assets extends AssetBundle
{

    public $sourcePath = '@module/mail/assets';
    public $css = [
        'mail.css',
    ];
    public $js = [
        'mail.js'
    ];

}
