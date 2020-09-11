<?php

use yii\db\Schema;
use humhub\components\Migration;
use humhub\modules\mail\models\MessageEntry;

class m150709_050452_message_tags extends Migration
{

    public function safeUp()
    {
        $this->createTable('message_tag', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'name' => $this->string(100)->notNull(),
            'sort_order' => $this->integer(11)->defaultValue(0),
            'color' => $this->string(7)->null()
        ]);

        try {
            $this->addForeignKey('fk-message-tag-user-id', 'message_tag', 'user_id', 'user', 'id', 'cascade');
        } catch(\Exception $e) {
            Yii::error($e);
        }
    }

    public function safeDown()
    {
        echo "m150709_050452_message_tags cannot be reverted.\n";

        return false;
    }
}
