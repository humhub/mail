<?php
namespace mail\acceptance;

use AcceptanceTester;

class SendMailCest
{
    /**
     * @dependssss login
     * @throws \Exception
     */
    public function testSendMail(AcceptanceTester $I)
    {
        $I->amUser();
        $I->wantTo('ensure sending mails to another user works');
        $I->amGoingTo('try opening the send mail modal');
        $I->expectTo('see the mail icon in');
        $I->seeElementInDOM('#icon-messages');
        $I->click('#icon-messages');
        $I->waitForText('There are no messages yet.');
        $I->click('#create-message-button');

        $I->expectTo('see create new message form');
        $I->waitForText('New message', 10, '#globalModal');
        $this->sendMessage($I, 'Sara', null, 'Just a test message.');
        $I->waitForText('Subject cannot be blank.', null, '#globalModal');

        $this->sendMessage($I, 'Sara', 'Hello there!', 'Just a test message.');
        $I->expectTo('see my message overview with the new conversation');
        $I->waitForText('Hello there!', null,'#mail-conversation-header');
        
        $I->wantTo('ensure I can add a participant');
        $I->click('Add user');
        $I->waitForText('Add more participants to your conversation', 10, '#globalModal');
        $I->selectUserFromPicker('#inviteparticipantform-recipients', 'Admin');

        $I->click('Save', '#globalModal'); //Send
        $I->expectTo('see the new user within the conversation user list');
        $I->waitForElement('[data-original-title="Admin Tester"]', null, '#mail-conversation-header');

        $I->wantTo('create another conversation');
        $I->click('New message', '#mail-conversation-overview');
        $I->waitForText('New message', null, '#globalModal');
        $this->sendMessage($I, 'Admin', 'Hi Admin!', 'Admin test message');
        $I->waitForText('Admin test message', null,'#mail-conversation-root');
        $I->see('Hi Admin!', '#mail-conversation-root');

        $I->wantToTest('the switch between conversations');
        $I->click('[data-message-id="1"]', '#mail-conversation-overview');
        $I->waitForText('Hello there!', null, '#mail-conversation-root');
        $I->see('Just a test message.');

        $I->logout();
        
        $I->amUser2();
        $I->wantTo('get sure I received the new message');
        $I->waitForElement('#badge-messages');
        $I->click('#icon-messages');
        $I->waitForElementVisible('#create-message-button', 10);
        $I->click('Show all messages');
        $I->expectTo('see my message overview with the new conversation');
        $I->waitForText('Hello there!', null,'#mail-conversation-root');
        $I->see('Just a test message.');
        
        $I->wantTo('leave the new conversation');
        $I->click('[data-original-title="Leave conversation"]');
        $I->waitForText('Confirm leaving conversation', null,'#globalModalConfirm');
        $I->click('Leave', '#globalModalConfirm');

        $I->expectTo('see an empty conversation box');
        $I->waitForText('There are no messages yet.');
    }

    private function sendMessage(AcceptanceTester $I, $recipient, $title, $message)
    {
        $I->selectUserFromPicker('#createmessage-recipient', $recipient);
        $I->wait(2);
        $I->fillField('#createmessage-title', $title);
        $I->fillField('#createmessage-message .humhub-ui-richtext', $message);
        $I->click('Send','#globalModal');
    }
}