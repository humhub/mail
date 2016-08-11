<?php
namespace mail\acceptance;

use AcceptanceTester;

class SendMailCest
{
    
    private function sendMessage(AcceptanceTester $I, $recipient, $title, $message)
    {
        $I->fillField('#recipient_tag_input_field', $recipient);
        $I->wait(3);
        $I->click($recipient);
        $I->fillField('CreateMessage[title]', $title);
        $I->fillField('CreateMessage[message]', $message);
        $I->click('#globalModal .modal-footer button:first-child');
        $I->wait(10);
    }
    
    /**
     * @dependssss login
     */
    public function testSendMail(AcceptanceTester $I)
    {
        $I->amUser();
        $I->wantTo('ensure sending mails to another user works');
        $I->amGoingTo('try opening the send mail modal');
        $I->expectTo('see the mail icon in');
        $I->seeElementInDOM('#icon-messages');
        $I->click('#icon-messages');
        $I->waitForElementVisible('#create-message-button', 10);
        $I->click('#create-message-button');
        $I->expectTo('see create new message form');
        $I->waitForElementVisible('#create-message-form', 10);
        $this->sendMessage($I, 'User2', 'Hello there!', 'Just a test message.');
        $I->expectTo('see my message overview with the new conversation');
        $I->see('Conversations');
        $I->see('Hello there!');
        $I->see('Just a test message.');
        
        $I->wantTo('ensure I can add a participant');
        $I->click('Add user');
        $I->waitForElementVisible('.addUserFrom_mail_user_picker_container', 10);
        $I->fillField('#addUserFrom_mail_tag_input_field', 'Admin');
        $I->wait(5);
        $I->click('Admin');
        $I->click('/html/body/div[8]/div/div/form/div[3]/button[1]'); //Send
        $I->wait(5);
        $I->logout();
        
        $I->amAdmin();
        $I->wantTo('get sure I received the new message');
        $I->seeElement('#badge-messages');
        $I->click('#icon-messages');
        $I->waitForElementVisible('#create-message-button', 10);
        $I->click('Show all messages');
        $I->wait(10);
        $I->expectTo('see my message overview with the new conversation');
        $I->see('Conversations');
        $I->see('Hello there!');
        $I->see('Just a test message.');
        
        $I->wantTo('leave the new conversation');
        $I->click('Leave discussion');
        $I->wait(10);
        $I->expectTo('see an empty conversation box');
        $I->see('There are no messages yet.');
    }
}