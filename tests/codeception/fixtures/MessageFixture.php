<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace tests\codeception\fixtures;

use humhub\modules\mail\models\Message;
use yii\test\ActiveFixture;

class MessageFixture extends ActiveFixture
{

    public $modelClass = Message::class;
    public $depends = [
        MessageEntryFixture::class,
        UserMessageFixture::class,
        ConversationTagFixture::class,
        UserMessageTagFixture::class,
    ];
}
