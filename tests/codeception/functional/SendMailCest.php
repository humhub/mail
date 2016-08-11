<?php
namespace mail\functional;


use mail\FunctionalTester;

class SendMailCest
{
    public function testSendMail(FunctionalTester $I)
    {
        $I->amUser();
        $I->wantToTest('if sending messages works');
        
        $I->amGoingTo('send a message to another user');
        $I->sendMessage('01e50e0d-82cd-41fc-8b0c-552392f5839e', 'TestTitle', 'TestMessage');
        $I->expect('the new message in the database');
        $I->seeRecord('humhub\modules\mail\models\Message', ['title' => 'TestTitle']);
        $message = $I->grabRecord('humhub\modules\mail\models\Message', ['title' => 'TestTitle']);
        $I->seeRecord('humhub\modules\mail\models\MessageEntry', ['message_id' => $message->id, 'content' => 'TestMessage']);
        $I->seeRecord('humhub\modules\mail\models\UserMessage', ['message_id' => $message->id, 'user_id' => 2, 'is_originator' => 1]);
        $I->seeRecord('humhub\modules\mail\models\UserMessage', ['message_id' => $message->id, 'user_id' => 3]);
        
        $I->amGoingTo('check my conversation overview');
        $I->amOnPage(['/mail/mail/index']);
        
        $I->expect('to see the new message');
        $I->see('Conversation');
        $I->dontSee('There are no messages yet.');
        $I->see('TestTitle');
        $I->see('TestMessage');
    }
    
    // send mail to multiple recipients
    // permissions
    // friendship
    // Add user
    // Delete Message
    // Notification
}