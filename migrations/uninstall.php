<?php

class uninstall extends ZDbMigration {

    public function up() {

        $this->dropTable('user_message');
        $this->dropTable('message');
        $this->dropTable('message_entry');
    }

    public function down() {
        echo "uninstall does not support migration down.\n";
        return false;
    }

}