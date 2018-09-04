<?php
namespace humhub\modules\mail\assets;

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
        'forceCopy' => true
    ];

    public $sourcePath = '@mail/resources';

    public $js = [
        'js/humhub.mail.wall.js',
        'js/humhub.mail.js'
    ];

    public $css = ['css/humhub.mail.css'];


}