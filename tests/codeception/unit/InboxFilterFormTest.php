<?php


use humhub\modules\mail\models\forms\CreateMessage;
use humhub\modules\mail\models\forms\InboxFilterForm;
use humhub\modules\mail\models\MessageTag;
use humhub\modules\user\models\User;
use tests\codeception\_support\HumHubDbTestCase;

class InboxFilterFormTest extends HumHubDbTestCase
{
    /**
     * @param $title
     * @param $message
     * @param null $users
     * @param array $tags
     * @return CreateMessage
     * @throws Throwable
     */
    private function createMessage($title, $message, $users = null, $tags = [])
    {
        $this->becomeUser('User1');

        if(!$users) {
            $user2 = User::findOne(['id' => 3]);
            $users = [$user2->guid];
        }

        $message = new CreateMessage([
            'title' => $title,
            'message' => $message,
            'recipient' => $users,
            'tags' => $tags
        ]);

        $this->assertTrue($message->save());

        return $message;
    }

    public function testTermFilterMatchesTitle()
    {
        $message1 = $this->createMessage('This is a test which contains my search term SEARCHME', 'Nothing...');
        $this->createMessage('This is a test which does not contain the search term', 'Nothing...');

        $filter = new InboxFilterForm(['term' => 'SEARCHME']);
        $filter->apply();
        $result = $filter->query->all();
        $this->assertCount(1, $result);
        $this->assertEquals($message1->messageInstance->id, $result[0]->message_id);
    }

    public function testTermFilterMatchesMessage()
    {
        $message1 = $this->createMessage('This is a test which contains my search term', 'SEARCHME');
        $this->createMessage('This is a test which does not contain the search term', 'Nothing...');

        $filter = new InboxFilterForm(['term' => 'SEARCHME']);
        $filter->apply();
        $result = $filter->query->all();
        $this->assertCount(1, $result);
        $this->assertEquals($message1->messageInstance->id, $result[0]->message_id);
    }

    public function testParticipationFilterMatchesSingleUser()
    {
        $user2 = User::findOne(['id' => 3]);
        $user3 = User::findOne(['id' => 4]);

        $message1 = $this->createMessage('First', 'First', [$user2->guid]);
        $message2 = $this->createMessage('Second', 'First',  [$user3->guid]);

        $filter = new InboxFilterForm(['participants' => [$user3->guid]]);
        $filter->apply();
        $result = $filter->query->all();
        $this->assertCount(1, $result);
        $this->assertEquals($message2->messageInstance->id, $result[0]->message_id);
    }

    public function testParticipationFilterOnlyIncludesMessagesWithAllUsers()
    {
        $user2 = User::findOne(['id' => 3]);
        $user3 = User::findOne(['id' => 4]);

        $message1 = $this->createMessage('First', 'First', [$user2->guid]);
        $message2 = $this->createMessage('Second', 'First',  [$user3->guid]);
        $message3 = $this->createMessage('Third', 'Third',  [$user3->guid, $user2->guid]);

        $filter = new InboxFilterForm(['participants' => [$user3->guid, $user2->guid]]);
        $filter->apply();
        $result = $filter->query->all();
        $this->assertCount(1, $result);
        $this->assertEquals($message3->messageInstance->id, $result[0]->message_id);
    }

    public function testSingleTagFilter()
    {
        $message1 = $this->createMessage('First', 'First', null, ['_add:TestTag']);
        $message2 = $this->createMessage('Second', 'First', null, ['_add:TestTag2']);
        $message3 = $this->createMessage('Third', 'Third');

        $userTag = MessageTag::findOne(['name' => 'TestTag']);

        $filter = new InboxFilterForm(['tags' => [$userTag->id]]);
        $filter->apply();
        $result = $filter->query->all();
        $this->assertCount(1, $result);
        $this->assertEquals($message1->messageInstance->id, $result[0]->message_id);
    }

    public function testMultipleTagFilter()
    {
        $message1 = $this->createMessage('First', 'First', null, ['_add:TestTag', '_add:TestTag2']);

        $userTag = MessageTag::findOne(['name' => 'TestTag']);
        $userTag2 = MessageTag::findOne(['name' => 'TestTag2']);

        $message2 = $this->createMessage('Second', 'First', null, [$userTag->id]);
        $message3 = $this->createMessage('Third', 'Third',  null, [$userTag2->id]);

        $userTag = MessageTag::findOne(['name' => 'TestTag']);

        $filter = new InboxFilterForm(['tags' => [$userTag->id, $userTag2->id]]);
        $filter->apply();
        $result = $filter->query->all();
        $this->assertCount(1, $result);
        $this->assertEquals($message1->messageInstance->id, $result[0]->message_id);
    }

}