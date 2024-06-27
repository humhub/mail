<?php

use humhub\components\Migration;
use humhub\modules\mail\models\MessageEntry;

/**
 * Class m230214_062338_drop_file_id
 */
class m230214_062338_drop_file_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->safeDropColumn(MessageEntry::tableName(), 'file_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230214_062338_drop_file_id cannot be reverted.\n";

        return false;
    }
}
