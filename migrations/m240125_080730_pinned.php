<?php

use humhub\components\Migration;
use humhub\modules\mail\models\UserMessage;

/**
 * Class m240125_080730_pinned
 */
class m240125_080730_pinned extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->safeAddColumn(UserMessage::tableName(), 'pinned', $this->boolean()->defaultValue(false)->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->safeDropColumn(UserMessage::tableName(), 'pinned');
    }
}
