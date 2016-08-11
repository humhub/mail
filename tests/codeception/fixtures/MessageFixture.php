<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace tests\codeception\fixtures;

use yii\test\ActiveFixture;

class MessageFixture extends ActiveFixture
{

    public $modelClass = 'humhub\modules\mail\models\Message';
    public $depends = [
        'tests\codeception\fixtures\MessageEntryFixture',
        'tests\codeception\fixtures\UserMessageFixture',
    ];
}
