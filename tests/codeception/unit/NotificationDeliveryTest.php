<?php

use humhub\modules\mail\models\forms\CreateMessage;
use humhub\modules\mail\models\forms\ReplyForm;
use humhub\modules\notification\models\NotificationDelivery;
use humhub\modules\notification\tests\codeception\unit\DeliveryTestTrait;
use tests\codeception\_support\HumHubDbTestCase;
use humhub\modules\user\models\User;

/**
 * The messenger notifications in the delivery layer of the core, with the default timings of the
 * mail target and a frozen clock (see {@see DeliveryTestTrait}).
 */
class NotificationDeliveryTest extends HumHubDbTestCase
{
    use DeliveryTestTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpDelivery();
    }

    public function testNewConversationGoesOutAfterAMinuteAndRepliesAreCollected()
    {
        $message = $this->createConversation();

        // high priority: mailed after a minute, unless seen by then
        $this->assertCount(0, $this->mails());
        $this->now += 60;
        $this->runDeliverJob('email', 3);
        $this->assertCount(1, $this->mails());
        $this->assertSame(NotificationDelivery::STATE_SENT, (int)$this->deliveries('email', 3)[0]->state);

        $this->now += 10;
        $this->reply($message, 'Reply 1');
        $this->now += 10;
        $this->reply($message, 'Reply 2');

        // waiting for the delay of the second message within the hour
        $this->assertCount(1, $this->mails());

        $this->now += 300;
        $this->runDeliverJob('email', 3);

        $mails = $this->mails();
        $this->assertCount(2, $mails);
        $this->assertSame('Peter Tester sent you 2 messages in “First”', $mails[1]->getSubject());
        // the collected group is one notification: its mail quotes the newest message
        $text = $mails[1]->getSymfonyEmail()->getTextBody();
        $this->assertStringContainsString('> Reply 2', $text);
        $this->assertStringNotContainsString('Reply 1', $text);
        $this->assertStringContainsString('Reply now: ', $text);
    }

    public function testReadingTheConversationSkipsThePendingDeliveries()
    {
        $message = $this->createConversation();
        $this->now += 60;
        $this->runDeliverJob('email', 3);
        $this->now += 10;
        $this->reply($message, 'Reply 1');
        $this->now += 10;
        $this->reply($message, 'Reply 2');

        $pending = array_slice($this->deliveries('email', 3), 1);
        $this->assertCount(2, $pending);
        foreach ($pending as $delivery) {
            $this->assertSame(NotificationDelivery::STATE_PENDING, (int)$delivery->state);
        }

        $message->seen(3);

        $this->now += 300;
        $this->runDeliverJob('email', 3);

        $this->assertCount(1, $this->mails());
        foreach (array_slice($this->deliveries('email', 3), 1) as $delivery) {
            $this->assertSame(NotificationDelivery::STATE_SKIPPED, (int)$delivery->state);
        }
    }

    private function createConversation(): \humhub\modules\mail\models\Message
    {
        $this->becomeUser('User1');

        $form = new CreateMessage([
            'title' => 'First',
            'message' => 'First message',
            'recipient' => [User::findOne(['id' => 3])->guid],
        ]);
        $this->assertTrue($form->save());

        return $form->messageInstance;
    }

    private function reply(\humhub\modules\mail\models\Message $message, string $text): void
    {
        $this->assertTrue((new ReplyForm(['model' => $message, 'message' => $text]))->save());
    }
}
