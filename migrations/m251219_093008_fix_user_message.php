<?php

use humhub\components\Migration;
use humhub\modules\mail\models\Message;
use humhub\modules\mail\models\UserMessage;
use humhub\modules\user\models\User;

class m251219_093008_fix_user_message extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        UserMessage::deleteAll([
            'OR',
            ['NOT IN', 'message_id', Message::find()->select('id')],
            ['NOT IN', 'user_id', User::find()->select('id')],
        ]);

        $this->safeAddForeignKey('fk_mail_user_message_user_id', 'user_message', 'user_id', 'user', 'id', 'CASCADE');
        $this->safeAddForeignKey('fk_mail_user_message_message_id', 'user_message', 'message_id', 'message', 'id', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m251219_093008_fix_user_message cannot be reverted.\n";

        return false;
    }
}
