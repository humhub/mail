<?php

namespace mail\api;

use mail\ApiTester;
use tests\codeception\_support\HumHubApiTestCest;

class MessageCest extends HumHubApiTestCest
{
    public function testListByAdmin(ApiTester $I)
    {
        if (!$this->isRestModuleEnabled()) {
            return;
        }

        $I->wantTo('see conversations of the Admin');
        $I->amAdmin();
        $I->seePaginationGetResponse('mail', [
            ['id' => 1, 'title' => 'First message title'],
            ['id' => 2, 'title' => 'Second message title'],
            ['id' => 3, 'title' => 'Third message title'],
        ]);
    }

    public function testListByUser1(ApiTester $I)
    {
        if (!$this->isRestModuleEnabled()) {
            return;
        }

        $I->wantTo('see conversations of the User 1');
        $I->amUser1();
        $I->seePaginationGetResponse('mail', [
            ['id' => 2, 'title' => 'Second message title'],
            ['id' => 3, 'title' => 'Third message title'],
        ]);
    }

    public function testGetConversationById(ApiTester $I)
    {
        if (!$this->isRestModuleEnabled()) {
            return;
        }

        $I->wantTo('see conversation by id');
        $I->amUser1();
        $I->sendGet('mail/2');
        $I->seeSuccessResponseContainsJson(['id' => 2, 'title' => 'Second message title']);
    }

    public function testCreateConversation(ApiTester $I)
    {
        if (!$this->isRestModuleEnabled()) {
            return;
        }

        $I->wantTo('create conversation');
        $I->amUser1();
        $I->sendPost('mail', [
            'title' => ($title = 'New created conversation by User1'),
            'message' => 'Sample text for the created conversation.',
            'recipient' => ['01e50e0d-82cd-41fc-8b0c-552392f5839c', '01e50e0d-82cd-41fc-8b0c-552392f5839e'],
        ]);
        $I->seeSuccessResponseContainsJson(['id' => 4, 'title' => $title]);
    }
}
