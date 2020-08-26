<?php


namespace humhub\modules\mail\widgets;


use humhub\components\Widget;
use humhub\modules\mail\models\Message;
use humhub\modules\mail\models\MessageEntry;
use humhub\modules\mail\widgets\ConversationEntry;

class Messages extends Widget
{
    /**
     * @var Message
     */
    public $message;

    /**
     * @var
     */
    public $entries;

    /**
     * @var int
     */
    public $from;

    /**
     * @inheritDoc
     */
    public function run()
    {
        $prevEntry = null;
        $result = '';

        $entries = $this->getEntries();
        foreach ($entries as $index => $entry) {
            $nextEntry = $entries[$index + 1] ?? null;
            $result .= ConversationEntry::widget(['entry' => $entry, 'prevEntry' => $prevEntry, 'nextEntry' => $nextEntry]);
            $prevEntry = $entry;
        }

        return $result;
    }

    /**
     * @return MessageEntry[]
     */
    private function getEntries()
    {
        if($this->entries) {
            return $this->entries;
        }

        return $this->message->getEntryPage($this->from);
    }

}