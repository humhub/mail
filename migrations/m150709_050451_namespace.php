<?php

use yii\db\Schema;
use humhub\components\Migration;
use humhub\modules\mail\models\MessageEntry;

class m150709_050451_namespace extends Migration
{

    public function up()
    {
        $this->renameClass('MessageEntry', MessageEntry::className());
    }

    public function down()
    {
        echo "m150709_050451_namespace cannot be reverted.\n";

        return false;
    }

    /*
      // Use safeUp/safeDown to run migration code within a transaction
      public function safeUp()
      {
      }

      public function safeDown()
      {
      }
     */
}
