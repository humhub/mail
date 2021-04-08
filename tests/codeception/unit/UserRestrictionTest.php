<?php

namespace tests\codeception\unit;

use humhub\modules\mail\models\Config;
use humhub\modules\mail\models\forms\CreateMessage;
use humhub\modules\user\models\fieldtype\DateTime;
use humhub\modules\user\models\User;
use tests\codeception\_support\HumHubDbTestCase;
use Yii;

class UserRestrictionTest extends HumHubDbTestCase
{

    /**
     * Test registration with whitelist and valid email (with approval process) -> user should be enabled.
     * TODO: Its not possible to completely deny conversations
     */
    /*
    public function testNonConversationsAllowed()
    {

        $this->becomeUser('User1');
        $config = new Config();
        $config->reset(User::findOne(['id' => 2]));
        $config->userConversationRestriction = 0;
        $this->assertTrue($config->save());

        $message = new CreateMessage([
            'recipient' => User::findOne(['id' => 1]),
            'title' => 'Test title',
            'message' => 'Test message'
        ]);

        $this->assertFalse($message->save());
    }
    */

    public function testFewConversationsAllowed()
    {
        $config = new Config();
        $config->reset(User::findOne(['id' => 2]));
        $config->userConversationRestriction = 3;
        $this->assertTrue($config->save());

        $this->becomeUser('User1');

        $message = new CreateMessage([
            'recipient' => User::findOne(['id' => 1]),
            'title' => 'Test title',
            'message' => 'Test message'
        ]);


        $this->assertTrue($message->save());

        $message = new CreateMessage([
            'recipient' => User::findOne(['id' => 1]),
            'title' => 'Test title',
            'message' => 'Test message'
        ]);


        $this->assertTrue($message->save());

        $message = new CreateMessage([
            'recipient' => User::findOne(['id' => 1]),
            'title' => 'Test title',
            'message' => 'Test message'
        ]);


        $this->assertTrue($message->save());

        $message = new CreateMessage([
            'recipient' => User::findOne(['id' => 1]),
            'title' => 'Test title',
            'message' => 'Test message'
        ]);

        $this->assertFalse($message->save());
    }

    public function testNewUserRestriction()
    {
        $user = User::findOne(['id' => 2]);
        $user->created_at = date('Y-m-d G:i:s');
        $user->save();
        $user->refresh();

        $config = new Config();
        $config->reset($user);
        $config->newUserSinceDays = 1;
        $config->newUserRestrictionEnabled = 1;
        $config->newUserConversationRestriction = 1;
        $this->assertTrue($config->save());

        $this->becomeUser('User1');


        $this->assertTrue($config->isNewUser($user));

        $message = new CreateMessage([
            'recipient' => User::findOne(['id' => 1]),
            'title' => 'Test title',
            'message' => 'Test message'
        ]);


        $this->assertTrue($message->save());

        $message = new CreateMessage([
            'recipient' => User::findOne(['id' => 1]),
            'title' => 'Test title',
            'message' => 'Test message'
        ]);

        $this->assertFalse($message->save());
    }

    public function testNewUserRestrictionTwo()
    {
        $user = User::findOne(['id' => 2]);
        $user->created_at = (new \DateTime())->sub(new \DateInterval('P1D'))->format('Y-m-d H:i:s');
        $user->save();
        $user->refresh();

        $config = new Config();
        $config->reset($user);
        $config->newUserSinceDays = 1;
        $config->newUserRestrictionEnabled = 1;
        $config->newUserConversationRestriction = 1;
        $config->userConversationRestriction = 2;
        $this->assertTrue($config->save());

        $this->becomeUser('User1');

        $this->assertFalse($config->isNewUser($user));

        $message = new CreateMessage([
            'recipient' => User::findOne(['id' => 1]),
            'title' => 'Test title',
            'message' => 'Test message'
        ]);


        $this->assertTrue($message->save());

        $message = new CreateMessage([
            'recipient' => User::findOne(['id' => 1]),
            'title' => 'Test title',
            'message' => 'Test message'
        ]);

        $this->assertTrue($message->save());

        $message = new CreateMessage([
            'recipient' => User::findOne(['id' => 1]),
            'title' => 'Test title',
            'message' => 'Test message'
        ]);

        $this->assertFalse($message->save());
    }

    public function testResetConversationCount()
    {
        $config = new Config();
        $config->reset(User::findOne(['id' => 2]));
        $config->userConversationRestriction = 1;
        $this->assertTrue($config->save());

        $this->becomeUser('User1');


        $message = new CreateMessage([
            'recipient' => User::findOne(['id' => 1]),
            'title' => 'Test title',
            'message' => 'Test message'
        ]);


        $this->assertTrue($message->save());

        $message = new CreateMessage([
            'recipient' => User::findOne(['id' => 1]),
            'title' => 'Test title',
            'message' => 'Test message'
        ]);

        $this->assertFalse($message->save());


        Config::getModule()->settings->user()->set('conversationCountTime', (new \DateTime())->sub(new \DateInterval('P1D'))->getTimestamp());

        $message = new CreateMessage([
            'recipient' => User::findOne(['id' => 1]),
            'title' => 'Test title',
            'message' => 'Test message'
        ]);

        $this->assertTrue($message->save());

        $message = new CreateMessage([
            'recipient' => User::findOne(['id' => 1]),
            'title' => 'Test title',
            'message' => 'Test message'
        ]);

        $this->assertFalse($message->save());
    }

}
