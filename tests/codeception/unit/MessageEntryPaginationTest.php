<?php

use humhub\modules\mail\models\forms\CreateMessage;
use humhub\modules\mail\models\Message;
use humhub\modules\mail\models\MessageEntry;
use humhub\modules\user\models\User;
use tests\codeception\_support\HumHubDbTestCase;

/**
 * Regression tests for the pagination cursor handling in Message::getEntryUpdates()
 * and Message::getEntryPage().
 *
 * Background: both methods used `if ($from)` to decide whether a pagination cursor
 * was given. Because PHP casts the string "0" to false, a request with `from=0`
 * was silently treated the same as "no cursor at all". In getEntryUpdates() this
 * additionally meant no limit() was ever applied, so the entire conversation
 * history was loaded in a single query - a self-inflicted DoS on large
 * conversations. See cipher-lab.org advisory, 2026-08-08.
 */
class MessageEntryPaginationTest extends HumHubDbTestCase
{
    /**
     * Creates a fresh conversation between User1 (id=2) and User2 (id=3), logged in
     * as User1. The conversation already contains exactly one entry afterwards,
     * with a deterministic created_at (offset 0).
     */
    private function createConversation(): Message
    {
        $this->becomeUser('User1');

        $form = new CreateMessage([
            'title' => 'Pagination Test',
            'message' => 'Entry 0',
            'recipient' => [User::findOne(['id' => 3])->guid],
        ]);

        $this->assertTrue($form->save(), 'Message creation failed: ' . json_encode($form->getErrors()));

        $message = $form->messageInstance;
        $this->setEntryCreatedAt($message->getLastEntry(), 0);

        return $message;
    }

    /**
     * Appends $count additional entries to $message, as the currently logged in
     * user, each one second apart (starting one second after the initial entry)
     * so their relative order is always unambiguous.
     */
    private function addEntries(Message $message, int $count): void
    {
        for ($i = 0; $i < $count; $i++) {
            $entry = MessageEntry::createForMessage($message, Yii::$app->user->getIdentity(), 'Extra entry ' . $i);
            $this->assertTrue($entry->save(), 'Entry creation failed: ' . json_encode($entry->getErrors()));
            $this->setEntryCreatedAt($entry, $i + 1);
        }
    }

    /**
     * Forces a deterministic created_at on an already-saved entry, $secondsOffset
     * seconds after a fixed base time.
     */
    private function setEntryCreatedAt(MessageEntry $entry, int $secondsOffset): void
    {
        $entry->created_at = (new DateTime('2026-01-01 00:00:00'))
            ->modify("+{$secondsOffset} seconds")
            ->format('Y-m-d H:i:s');

        $this->assertTrue($entry->save(false), 'Failed to set deterministic created_at on entry ' . $entry->id);
    }

    public function testGetEntryUpdatesIsAlwaysBoundedByPageSize(): void
    {
        $module = Yii::$app->getModule('mail');
        $module->conversationUpdatePageSize = 3;

        $message = $this->createConversation();
        $this->addEntries($message, 10); // 11 entries total, well above the page size of 3

        // Every one of these must be treated as "no valid cursor" and still be
        // capped at conversationUpdatePageSize - none of them must load everything.
        foreach ([null, '0', 0, '', false] as $from) {
            $entries = $message->getEntryUpdates($from)->all();
            $this->assertLessThanOrEqual(
                3,
                count($entries),
                'getEntryUpdates(' . var_export($from, true) . ') must never exceed conversationUpdatePageSize'
            );
        }
    }

    public function testGetEntryUpdatesFromZeroBehavesLikeNoCursor(): void
    {
        $module = Yii::$app->getModule('mail');
        $module->conversationUpdatePageSize = 50; // large enough to not truncate any of these results

        $message = $this->createConversation();
        $this->addEntries($message, 4); // 5 entries total

        $withNull = array_map(fn($e) => $e->id, $message->getEntryUpdates(null)->all());
        $withStringZero = array_map(fn($e) => $e->id, $message->getEntryUpdates('0')->all());
        $withIntZero = array_map(fn($e) => $e->id, $message->getEntryUpdates(0)->all());

        $this->assertSame($withNull, $withStringZero, 'from="0" must be treated the same as no cursor at all');
        $this->assertSame($withNull, $withIntZero, 'from=0 (int) must be treated the same as no cursor at all');
    }

    public function testGetEntryUpdatesRespectsARealPositiveCursor(): void
    {
        $module = Yii::$app->getModule('mail');
        $module->conversationUpdatePageSize = 50;

        $message = $this->createConversation();
        $this->addEntries($message, 4); // 5 entries total, ascending ids

        $allEntries = $message->getEntryUpdates(null)->all();
        $this->assertCount(5, $allEntries);

        $cursor = $allEntries[1]->id; // second oldest entry
        $expectedRemainingIds = array_map(fn($e) => $e->id, array_slice($allEntries, 2));

        $filtered = array_map(fn($e) => $e->id, $message->getEntryUpdates($cursor)->all());

        $this->assertSame($expectedRemainingIds, $filtered);
    }

    public function testGetEntryPageFromZeroBehavesLikeNoCursor(): void
    {
        $module = Yii::$app->getModule('mail');
        $module->conversationInitPageSize = 3;
        $module->conversationUpdatePageSize = 3;

        $message = $this->createConversation();
        $this->addEntries($message, 10); // 11 entries total

        $withNull = array_map(fn($e) => $e->id, $message->getEntryPage(null));
        $withStringZero = array_map(fn($e) => $e->id, $message->getEntryPage('0'));

        $this->assertSame($withNull, $withStringZero, 'getEntryPage("0") must behave like getEntryPage(null)');
        $this->assertCount(3, $withStringZero);
    }

    public function testGetEntryPageRespectsARealPositiveCursor(): void
    {
        $module = Yii::$app->getModule('mail');
        $module->conversationInitPageSize = 50;
        $module->conversationUpdatePageSize = 50;

        $message = $this->createConversation();
        $this->addEntries($message, 4); // 5 entries total, ascending ids

        $allEntries = $message->getEntryUpdates(null)->all();
        $this->assertCount(5, $allEntries);

        $cursor = $allEntries[3]->id; // fourth oldest entry (index 3)
        $expectedOlderIds = array_map(fn($e) => $e->id, array_slice($allEntries, 0, 3));

        $page = array_map(fn($e) => $e->id, $message->getEntryPage($cursor));

        $this->assertSame($expectedOlderIds, $page);
    }
}
