<?php

use humhub\components\Migration;

class uninstall extends Migration
{

    public function up()
    {
        $this->safeDropTable('user_message_tag');
        $this->safeDropTable('message_tag');
        $this->safeDropTable('user_message');
        $this->safeDropTable('message');
        $this->safeDropTable('message_entry');
    }

    public function down()
    {
        echo "uninstall does not support migration down.\n";
        return false;
    }

}
