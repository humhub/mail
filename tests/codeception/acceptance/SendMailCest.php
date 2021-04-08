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
        $this->openNotificationInbox($I);
        $this->submitInvalidMessageByModal($I);
        $this->submitMessageMessageByModal($I);

        $this->addParticipant($I);

        $this->createConversationByInbox($I);
        $this->switchClickOnInboxMessage($I);

        $this->addAndFilterTag($I);


        $I->logout();
        
        $I->amUser2();

        $this->seeNewMessagesAndEnterOverview($I);

        $this->leaveConversation($I);
    }

    private function openNotificationInbox(AcceptanceTester $I)
    {
        $I->wantTo('ensure sending mails to another user works');
        $I->amGoingTo('try opening the send mail modal');
        $I->expectTo('see the mail icon in');
        $I->seeElementInDOM('#icon-messages');
        $I->click('#icon-messages');
        $I->waitForText('Show all messages');
        $I->click('#create-message-button');
    }

    private function submitInvalidMessageByModal(AcceptanceTester $I)
    {
        $I->expectTo('see create new message form');
        $I->waitForText('New message', 10, '#globalModal');
        $this->sendMessage($I, 'Sara', null, 'Just a test message.');
        $I->waitForText('Subject cannot be blank.', null, '#globalModal');
    }

    private function submitMessageMessageByModal(AcceptanceTester $I)
    {
        $this->sendMessage($I, 'Sara', 'Hello there!', 'Just a test message.');
        $I->expectTo('see my message overview with the new conversation');
        $I->waitForText('Hello there!', null,'#mail-conversation-header');
    }

    private function sendMessage(AcceptanceTester $I, $recipient, $title, $message)
    {
        $I->selectUserFromPicker('#createmessage-recipient', $recipient);
        $I->wait(2);
        $I->fillField('#createmessage-title', $title);
        $I->fillField('#createmessage-message .humhub-ui-richtext', $message);
        $I->click('Send','#globalModal');
    }

    private function addParticipant(AcceptanceTester $I)
    {
        $I->wantTo('ensure I can add a participant');
        $I->click('#conversationSettingsButton');
        $I->wait(1);
        $I->click('Add user', '#mail-conversation-header');
        $I->waitForText('Add more participants to your conversation', 10, '#globalModal');
        $I->selectUserFromPicker('#inviteparticipantform-recipients', 'Admin');

        $I->click('Save', '#globalModal'); //Send
        $I->expectTo('see the new user within the conversation user list');
        $I->waitForElement('[data-original-title="Admin Tester"]', null, '#mail-conversation-header');
    }

    private function createConversationByInbox(AcceptanceTester $I)
    {
        $I->wantTo('create another conversation');
        $I->click('+ Message', '#mail-conversation-overview .panel-heading');
        $I->waitForText('New message', null, '#globalModal');
        $this->sendMessage($I, 'Admin', 'Hi Admin!', 'Admin test message');
        $I->waitForText('Admin test message', null,'#mail-conversation-root');
        $I->see('Hi Admin!', '#mail-conversation-root');
    }

    private function switchClickOnInboxMessage(AcceptanceTester $I)
    {
        $I->wantToTest('the switch between conversations');
        $I->click('[data-message-id="4"]', '#mail-conversation-overview');
        $I->waitForText('Hello there!', null, '#mail-conversation-root');
        $I->see('Just a test message.');
    }

    private function addAndFilterTag(AcceptanceTester $I)
    {
        $I->dontSee('#conversation-tags-root');
        $I->click('#conversationSettingsButton');
        $I->wait(1);
        $I->click('Tags', '#mail-conversation-header');
        $I->waitForText('Edit conversation tags', null, '#globalModal');
        $I->selectFromPicker('#conversationtagsform-tags', 'TestTag');
        $I->click('Save', '#globalModal');
        $I->waitForText('TESTTAG', null, '#conversation-tags-root');
        $I->dontSeeElement('#mail-filter-menu');
        $I->click('TestTag', '#conversation-tags-root');
        $I->waitForText('TestTag', null, '#mail-filter-menu');
        $I->see('Hello there!', '#inbox');
        $I->dontSee('Hi Admin!', '#inbox');
    }

    private function seeNewMessagesAndEnterOverview(AcceptanceTester $I)
    {
        $I->wantTo('get sure I received the new message');
        $I->waitForElement('#badge-messages');
        $I->click('#icon-messages');
        $I->waitForElementVisible('#create-message-button', 10);
        $I->click('Show all messages');
        $I->expectTo('see my message overview with the new conversation');
        $I->waitForText('Hello there!', null,'#mail-conversation-root');
        $I->see('Just a test message.');
    }

    private function leaveConversation(AcceptanceTester $I)
    {
        $I->click('#conversationSettingsButton');
        $I->wait(1);
        $I->click('Leave conversation', '#mail-conversation-header');
        $I->waitForText('Confirm leaving conversation', null,'#globalModalConfirm');
        $I->click('Leave', '#globalModalConfirm');

        $I->waitForText('Third message title', 10, '#mail-conversation-header');
        $I->click('#conversationSettingsButton');
        $I->wait(1);
        $I->click('Leave conversation', '#mail-conversation-header');
        $I->waitForText('Confirm leaving conversation', null,'#globalModalConfirm');
        $I->click('Leave', '#globalModalConfirm');

        $I->expectTo('see an empty conversation box');
        $I->waitForText('There are no messages yet.');
    }
}