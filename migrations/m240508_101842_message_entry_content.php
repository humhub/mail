<?php

use humhub\modules\mail\models\MessageEntry;
use yii\db\Migration;

/**
 * Class m240508_101842_message_entry_content
 */
class m240508_101842_message_entry_content extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameColumn('message_entry', 'content', 'entry_content');

        /** @var MessageEntry $messageEntry */
        foreach (MessageEntry::find()->all() as $messageEntry) {
            $messageEntry->content->object_model = MessageEntry::getObjectModel();
            $messageEntry->content->object_id = $messageEntry->getPrimaryKey();
            $messageEntry->content->save();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240508_101842_message_entry_content cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240508_101842_message_entry_content cannot be reverted.\n";

        return false;
    }
    */
}
