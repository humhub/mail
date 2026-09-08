<?php

use humhub\modules\mail\models\forms\CreateMessage;
use humhub\modules\mail\models\forms\InviteParticipantForm;
use humhub\modules\mail\models\forms\ReplyForm;
use humhub\modules\mail\models\Message;
use humhub\modules\mail\models\UserMessage;
use humhub\modules\user\models\User;
use tests\codeception\_support\HumHubDbTestCase;

/**
 * Tests for UserMessage::getNewMessageCount() and its cache invalidation.
 *
 * The cache itself (see UserMessage::getNewMessageCount()) is a thin getOrSet() wrapper - the
 * correctness load is entirely carried by the invalidation calls spread across
 * UserMessage::afterSave()/afterDelete() and AbstractMessageEntry::afterSave(). So instead of
 * testing those invalidation calls in isolation, these tests warm the cache with one action and
 * assert that a *subsequent* action which should change the count is actually reflected - if any
 * invalidation call were missing, these would fail by returning a stale cached value.
 */
class UserMessageNewMessageCountTest extends HumHubDbTestCase
{
    /**
     * Creates a conversation as User1 (id=2) with the given recipients.
     *
     * @param User[] $recipients
     */
    private function createConversation(array $recipients, string $title = 'Test conversation'): Message
    {
        $this->becomeUser('User1');

        $form = new CreateMessage([
            'title' => $title,
            'message' => 'Hello',
            'recipient' => array_map(fn(User $user) => $user->guid, $recipients),
        ]);

        $this->assertTrue($form->save(), 'Message creation failed: ' . json_encode($form->getErrors()));

        return $form->messageInstance;
    }

    private function reply(Message $message, string $content = 'Reply'): void
    {
        $replyForm = new ReplyForm(['model' => $message, 'message' => $content]);
        $this->assertTrue($replyForm->save(), 'Reply failed: ' . json_encode($replyForm->getErrors()));
    }

    public function testNewConversationCountsAsUnreadForRecipientOnly(): void
    {
        $user2 = User::findOne(['username' => 'User2']);
        $this->createConversation([$user2]);

        // Originator (User1) already "saw" their own message (last_viewed is set on creation) -
        // own count stays 0.
        $this->becomeUser('User1');
        $this->assertEquals(0, UserMessage::getNewMessageCount());

        // Recipient (User2) has one unread conversation.
        $this->becomeUser('User2');
        $this->assertEquals(1, UserMessage::getNewMessageCount());
    }

    /**
     * Covers exactly the matrix requested in review: a reply increases the other participant's
     * count, and seen() resets it - proving the cache is actually invalidated rather than serving
     * a stale value between these two actions.
     */
    public function testReplyIncreasesCountAndSeenResetsIt(): void
    {
        $user2 = User::findOne(['username' => 'User2']);
        $message = $this->createConversation([$user2]);

        // User2 reads the conversation - warms their cache at 0.
        $this->becomeUser('User2');
        $message->seen($user2->id);
        $this->assertEquals(0, UserMessage::getNewMessageCount(), 'Count should be 0 right after seen()');

        // Timestamps have 1-second precision (see Message::seen()/AbstractMessageEntry - both use
        // date('Y-m-d G:i:s')) - without this, the reply below could land in the same second as
        // the seen() call above and the ">" comparison in the count query would flakily miss it.
        sleep(1);

        // User1 replies - this must invalidate User2's cached count, not just User1's own.
        $this->becomeUser('User1');
        $this->reply($message);

        // If User2's cache wasn't invalidated, this would incorrectly still return the cached 0.
        $this->assertEquals(1, UserMessage::getNewMessageCount($user2->id), "Reply should invalidate the recipient's cached count");

        // The replier's own count must stay 0 (ReplyForm bumps their own last_viewed on reply).
        $this->assertEquals(0, UserMessage::getNewMessageCount(), 'Replying to your own conversation must not mark it unread for yourself');

        // User2 marks it seen again - must invalidate their cache back down to 0.
        $this->becomeUser('User2');
        $message->seen($user2->id);
        $this->assertEquals(0, UserMessage::getNewMessageCount(), 'seen() should invalidate the cache and reset the count to 0');
    }

    public function testJoiningAndLeavingInvalidateTheParticipantsOwnCache(): void
    {
        $user2 = User::findOne(['username' => 'User2']);
        $user3 = User::findOne(['username' => 'User3']);
        $message = $this->createConversation([$user2]);

        // User3 isn't a participant yet - warms their cache at 0.
        $this->becomeUser('User3');
        $this->assertEquals(0, UserMessage::getNewMessageCount(), 'Non-participant should have 0 unread conversations');

        // User1 invites User3 - inserts a UserMessage row for User3, which must invalidate
        // User3's cache (keyed by User3's id, already populated above).
        $this->becomeUser('User1');
        $inviteForm = new InviteParticipantForm(['message' => $message, 'recipients' => [$user3->guid]]);
        $this->assertTrue($inviteForm->save(), 'Invite failed: ' . json_encode($inviteForm->getErrors()));

        $this->assertEquals(1, UserMessage::getNewMessageCount($user3->id), "Joining a conversation should invalidate the new participant's cached count");

        // User3 leaves - must invalidate their cache again, back down to 0.
        $this->becomeUser('User3');
        $message->leave();

        $this->assertEquals(0, UserMessage::getNewMessageCount($user3->id), 'Leaving should invalidate the cache - the conversation is no longer counted');
    }
}
