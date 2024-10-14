<?php

use humhub\components\Migration;
use humhub\modules\mail\models\AbstractMessageEntry;
use humhub\modules\mail\models\MessageEntry;

/**
 * Class m230213_094842_add_state
 */
class m230213_094842_add_entry_type extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->safeAddColumn(
            AbstractMessageEntry::tableName(),
            'type',
            $this->tinyInteger()
            ->defaultValue(MessageEntry::type())
            ->notNull()
            ->unsigned()
            ->after('content'),
        );
        $this->alterColumn(AbstractMessageEntry::tableName(), 'content', $this->text()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->safeDropColumn(AbstractMessageEntry::tableName(), 'type');
        $this->alterColumn(AbstractMessageEntry::tableName(), 'content', $this->text()->notNull());
    }
}
