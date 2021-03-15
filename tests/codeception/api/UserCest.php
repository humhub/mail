<?php

namespace mail\api;

use mail\ApiTester;
use tests\codeception\_support\HumHubApiTestCest;

class UserCest extends HumHubApiTestCest
{
    public function testListByConversationId(ApiTester $I)
    {
        if (!$this->isRestModuleEnabled()) {
            return;
        }

        $I->wantTo('see recipients of the conversation by id');
        $I->amAdmin();
        $I->sendGet('mail/3/users');
        $I->seeSuccessResponseContainsJson($this->getRecipientsOfConversation3());
    }

    public function testAddRecipient(ApiTester $I)
    {
        if (!$this->isRestModuleEnabled()) {
            return;
        }

        $I->wantTo('add recipient to the conversation');
        $I->amUser2();
        $I->sendPost('mail/3/user/4');
        $I->seeSuccessResponseContainsJson(array_merge($this->getRecipientsOfConversation3(), [
            [
                'id' => 4,
                'guid' => '01e50e0d-82cd-41fc-8b0c-552392f5839f',
                'display_name' => 'Andreas Tester',
                'account' => ['username' => 'User3', 'email' => 'user3@example.com'],
                'profile' => ['firstname' => 'Andreas', 'lastname' => 'Tester'],
            ],
        ]));

        $I->sendPost('mail/3/user/4');
        $I->seeBadResponseContainsJson(['message' => 'User is already a participant of the conversation.']);
    }

    public function testRemoveRecipient(ApiTester $I)
    {
        if (!$this->isRestModuleEnabled()) {
            return;
        }

        $I->wantTo('remove recipient from the conversation');
        $I->amUser2();
        $I->sendDelete('mail/3/user/2');
        $recipientsOfConversation3 = $this->getRecipientsOfConversation3();
        $I->seeSuccessResponseContainsJson([$recipientsOfConversation3[0], $recipientsOfConversation3[2]]);

        $I->sendDelete('mail/3/user/2');
        $I->seeBadResponseContainsJson(['message' => 'User is not a participant of the conversation.']);
    }

    private function getRecipientsOfConversation3()
    {
        return [
            [
                'id' => 1,
                'guid' => '01e50e0d-82cd-41fc-8b0c-552392f5839c',
                'display_name' => 'Admin Tester',
                'account' => ['username' => 'Admin', 'email' => 'admin@example.com'],
                'profile' => ['firstname' => 'Admin', 'lastname' => 'Tester'],
            ],
            [
                'id' => 2,
                'guid' => '01e50e0d-82cd-41fc-8b0c-552392f5839d',
                'display_name' => 'Peter Tester',
                'account' => ['username' => 'User1', 'email' => 'user1@example.com'],
                'profile' => ['firstname' => 'Peter', 'lastname' => 'Tester'],
            ],
            [
                'id' => 3,
                'guid' => '01e50e0d-82cd-41fc-8b0c-552392f5839e',
                'display_name' => 'Sara Tester',
                'account' => ['username' => 'User2', 'email' => 'user2@example.com'],
                'profile' => ['firstname' => 'Sara', 'lastname' => 'Tester'],
            ],
        ];
    }
}
