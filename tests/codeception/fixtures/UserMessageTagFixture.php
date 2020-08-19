<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace tests\codeception\fixtures;

use humhub\modules\mail\models\MessageTag;
use yii\test\ActiveFixture;

class UserMessageTagFixture extends ActiveFixture
{

    public $modelClass = MessageTag::class;

    public $dataFile = '@mail/tests/codeception/fixtures/data/message_tag.php';

}
