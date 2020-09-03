<?php

use yii\db\Schema;
use humhub\components\Migration;
use humhub\modules\mail\models\MessageEntry;

class m150709_050453_conversation_tags extends Migration
{

    public function safeUp()
    {

        $this->createTable('user_message_tag', [
            'message_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'tag_id' => $this->integer()->notNull(),
        ], '');

        $this->addPrimaryKey('pk-user-message-tag', 'user_message_tag', ['user_id', 'message_id', 'tag_id']);

        try {
            $this->addForeignKey('fk-user-message-id', 'user_message_tag', ['message_id', 'user_id'], 'user_message', ['message_id', 'user_id'], 'cascade');
        } catch(\Exception $e) {
            Yii::error($e, 'mail');
        }

        try {
            $this->addForeignKey('fk-conversation-tag-tag-id', 'user_message_tag', 'tag_id', 'message_tag', 'id', 'cascade');
        } catch(\Exception $e) {
            Yii::error($e, 'mail');
        }
    }

    public function safeDown()
    {
        echo "m150709_050453_conversation_tags cannot be reverted.\n";

        return false;
    }
}
