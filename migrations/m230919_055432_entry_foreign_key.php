<?php

use humhub\components\Migration;

/**
 * Class m230919_055432_entry_foreign_key
 */
class m230919_055432_entry_foreign_key extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DELETE message_entry FROM message_entry
            LEFT JOIN user ON user.id = message_entry.user_id
            LEFT JOIN message ON message.id = message_entry.message_id
            WHERE user.id IS NULL OR message.id IS NULL');
        $this->safeAddForeignKey('fk-message-entry-user-id', 'message_entry', 'user_id', 'user', 'id', 'CASCADE');
        $this->safeAddForeignKey('fk-message-entry-message-id', 'message_entry', 'message_id', 'message', 'id', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->safeDropForeignKey('fk-message-entry-user-id', 'message_entry');
        $this->safeDropForeignKey('fk-message-entry-message-id', 'message_entry');
    }
}
