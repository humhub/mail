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
        $I->seeUserDefinitions(['Admin', 'User1', 'User2']);
    }

    public function testAddRecipient(ApiTester $I)
    {
        if (!$this->isRestModuleEnabled()) {
            return;
        }

        $I->wantTo('add recipient to the conversation');
        $I->amUser2();
        $I->sendPost('mail/3/user/4');
        $I->seeUserDefinitions(['Admin', 'User1', 'User2', 'User3']);

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
        $I->seeUserDefinitions(['Admin', 'User2']);

        $I->sendDelete('mail/3/user/2');
        $I->seeBadResponseContainsJson(['message' => 'User is not a participant of the conversation.']);
    }
}
