<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace tests\codeception\fixtures;

use humhub\modules\mail\models\UserMessageTag;
use yii\test\ActiveFixture;

class ConversationTagFixture extends ActiveFixture
{

    public $modelClass = UserMessageTag::class;

    public $dataFile = '@mail/tests/codeception/fixtures/data/user_message_tag.php';

}
