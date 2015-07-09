<?php

use yii\db\Migration;

class m150429_190600_indexes extends Migration
{

    public function up()
    {
        $this->createIndex('index_user_id', 'message_entry', 'user_id', false);
        $this->createIndex('index_message_id', 'message_entry', 'message_id', false);
        $this->createIndex('index_updated', 'message', 'updated_at', false);
        $this->createIndex('index_last_viewed', 'user_message', 'last_viewed', false);
        $this->createIndex('index_updated_by', 'message', 'updated_by', false);
    }

    public function down()
    {
        echo "m150429_190600_indexes does not support migration down.\n";
        return false;
    }

    /*
      // Use safeUp/safeDown to do migration with transaction
      public function safeUp()
      {
      }

      public function safeDown()
      {
      }
     */
}
