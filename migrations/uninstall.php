<?php

use yii\db\Migration;

class uninstall extends Migration
{

    public function up()
    {

        $this->dropTable('user_message');
        $this->dropTable('message');
        $this->dropTable('message_entry');
    }

    public function down()
    {
        echo "uninstall does not support migration down.\n";
        return false;
    }

}
