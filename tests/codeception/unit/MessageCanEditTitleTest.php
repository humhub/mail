<?php

use humhub\modules\mail\models\forms\CreateMessage;
use humhub\modules\mail\models\Message;
use humhub\modules\user\models\User;
use tests\codeception\_support\HumHubDbTestCase;

/**
 * Tests for Message::canEditTitle()
 */
class MessageCanEditTitleTest extends HumHubDbTestCase
{
    /**
     * Creates a conversation as User1 (id=2) with User2 (id=3) as recipient.
     */
    private function createConversationWithTitle(string $title = 'Test Title'): Message
    {
        $this->becomeUser('User1');

        $form = new CreateMessage([
            'title' => $title,
            'message' => 'Hello',
            'recipient' => [User::findOne(['id' => 3])->guid],
        ]);

        $this->assertTrue($form->save(), 'Message creation failed: ' . json_encode($form->getErrors()));

        return $form->messageInstance;
    }

    public function testNoTitleReturnsFalse(): void
    {
        $this->becomeUser('User1');

        // Bypass the form (which requires a title) and insert a message directly
        $message = new Message(['title' => null]);
        $this->assertTrue($message->save());

        $this->assertFalse($message->canEditTitle());
    }

    public function testCreatorCanEditTitle(): void
    {
        $message = $this->createConversationWithTitle();

        // Still logged in as User1 (the creator)
        $this->assertTrue($message->canEditTitle());
    }

    public function testNonCreatorParticipantCannotEditTitle(): void
    {
        $message = $this->createConversationWithTitle();

        // Switch to User2 — a participant but not the creator
        $this->becomeUser('User2');
        $message->refresh();

        $this->assertFalse($message->canEditTitle());
    }

    public function testNonParticipantCannotEditTitle(): void
    {
        $message = $this->createConversationWithTitle();

        // Switch to User3 (id=4) — not part of this conversation at all
        $this->becomeUser('User3');
        $message->refresh();

        $this->assertFalse($message->canEditTitle());
    }

    public function testLastRemainingParticipantCanEditTitleEvenIfNotCreator(): void
    {
        $message = $this->createConversationWithTitle();

        // User1 (creator, id=2) leaves; User2 (id=3) is the only participant left
        $message->leave();
        $message->refresh();

        // Now logged in as User2, who is the sole remaining participant but not the creator
        $this->becomeUser('User2');
        $message->refresh();

        $this->assertTrue($message->canEditTitle());
    }

    public function testCreatorRemainsAllowedAfterOtherParticipantLeaves(): void
    {
        $message = $this->createConversationWithTitle();
        $messageId = $message->id;

        // User2 (id=3) leaves the conversation
        $this->becomeUser('User2');
        $userMessage = $message->getUserMessage();
        $userMessage->delete();

        // Reload as User1 (creator, still the only participant)
        $this->becomeUser('User1');
        $message = Message::findOne($messageId);

        $this->assertTrue($message->canEditTitle());
    }
}
