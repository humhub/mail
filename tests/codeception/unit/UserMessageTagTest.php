<?php

use humhub\modules\mail\models\UserMessageTag;
use humhub\modules\mail\models\forms\ConversationTagsForm;
use humhub\modules\mail\models\forms\CreateMessage;
use humhub\modules\mail\models\MessageTag;
use tests\codeception\_support\HumHubDbTestCase;
use humhub\modules\user\models\User;

class UserMessageTagTest extends HumHubDbTestCase
{
    private function createMessage($tags = [])
    {
        $this->becomeUser('User1');
        $user2 = User::findOne(['id' => 3]);

        $message = new CreateMessage([
            'message' => 'Hey!',
            'title' => 'Test Conversation',
            'recipient' => [$user2->guid],
            'tags' => $tags
        ]);

        $this->assertTrue($message->save());

        return $message;
    }

   public function testSingleTagIsCreatedOnMessageCreation()
   {
       $message = $this->createMessage( ['_add:TestTag']);

       $this->assertCount(7, MessageTag::find()->all());
       $this->assertCount(7, UserMessageTag::find()->all());

       /** @var MessageTag[] $tag */
       $tags = MessageTag::findByMessage(Yii::$app->user->id, $message->messageInstance)->all();
       $this->assertNotNull($tags);
       $this->assertCount(1, $tags);
       $this->assertEquals(Yii::$app->user->id, $tags[0]->user_id);
       $this->assertEquals('TestTag',  $tags[0]->name);
   }

    public function testMultipleTagIsCreatedOnMessageCreation()
    {
        $message = $this->createMessage(['_add:TestTag', '_add:TestTag2']);

        $this->assertCount(8, MessageTag::find()->all());
        $this->assertCount(8, UserMessageTag::find()->all());

        /** @var MessageTag[] $tag */
        $tags = MessageTag::findByMessage(Yii::$app->user->id, $message->messageInstance)->all();
        $this->assertNotNull($tags);
        $this->assertCount(2, $tags);
        $this->assertEquals(Yii::$app->user->id, $tags[0]->user_id);
        $this->assertEquals(Yii::$app->user->id, $tags[1]->user_id);
        $this->assertEquals('TestTag',  $tags[0]->name);
        $this->assertEquals('TestTag2',  $tags[1]->name);
    }

    public function testDuplicateTagIsAttachedOnlyOnce()
    {
        $message = $this->createMessage(['_add:TestTag', '_add:TestTag']);

        $this->assertCount(7, MessageTag::find()->all());
        $this->assertCount(7, UserMessageTag::find()->all());

        /** @var MessageTag[] $tag */
        $tags = MessageTag::findByMessage(Yii::$app->user->id, $message->messageInstance)->all();
        $this->assertNotNull($tags);
        $this->assertCount(1, $tags);
        $this->assertEquals(Yii::$app->user->id, $tags[0]->user_id);
        $this->assertEquals('TestTag',  $tags[0]->name);
    }

    public function testMissingTagsAreDeletedOnAttach()
    {
        $message = $this->createMessage(['_add:TestTag', '_add:TestTag2']);

        $tags = MessageTag::findByMessage(Yii::$app->user->id, $message->messageInstance)->all();
        $editForm = new ConversationTagsForm(['message' => $message->messageInstance, 'tags' => [$tags[0]]]);
        $editForm->save();

        $updatedTags = MessageTag::findByMessage(Yii::$app->user->id, $message->messageInstance)->all();
        $this->assertCount(1, $updatedTags);
        $this->assertEquals('TestTag', $updatedTags[0]->name);
    }
}