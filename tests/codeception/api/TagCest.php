<?php

namespace mail\api;

use mail\ApiTester;
use tests\codeception\_support\HumHubApiTestCest;

class TagCest extends HumHubApiTestCest
{
    public function testListByConversationId(ApiTester $I)
    {
        if (!$this->isRestModuleEnabled()) {
            return;
        }

        $I->wantTo('see tags of the conversation by id');
        $I->amAdmin();
        $I->sendGet('mail/1/tags');
        $I->seeSuccessResponseContainsJson([
            'total' => 3,
            'page' => 1,
            'pages' => 1,
            'links' => ['self' => '/api/v1/mail/1/tags?page=1&per-page=100'],
            'results' => [
                ['id' => 1, 'name' => 'Tag admin 1', 'sort_order' => 10],
                ['id' => 2, 'name' => 'Tag admin 2', 'sort_order' => 20],
                ['id' => 3, 'name' => 'Tag admin 3', 'sort_order' => 30],
            ],
        ]);
    }

    public function testUpdateTags(ApiTester $I)
    {
        if (!$this->isRestModuleEnabled()) {
            return;
        }

        $I->wantTo('see tags of the conversation by id');
        $I->amUser1();
        $I->sendPut('mail/2/tags', ['tags' => ['User1 tag 1', 'User1 tag 2', 'User1 tag 3']]);
        $I->seeSuccessResponseContainsJson([
            'total' => '3',
            'page' => 1,
            'pages' => 1,
            'links' => ['self' => '/api/v1/mail/2/tags?page=1&per-page=100'],
            'results' => [
                ['id' => 7, 'name' => 'User1 tag 1'],
                ['id' => 8, 'name' => 'User1 tag 2'],
                ['id' => 9, 'name' => 'User1 tag 3'],
            ],
        ]);
    }

    public function testCannotUpdateTags(ApiTester $I)
    {
        if (!$this->isRestModuleEnabled()) {
            return;
        }

        $I->wantTo('see tags of the conversation by id');
        $I->amUser3();
        $I->sendPut('mail/2/tags', ['tags' => ['User1 tag 1', 'User1 tag 2', 'User1 tag 3']]);
        $I->seeForbiddenResponseContainsJson([
            'message' => 'You must be a participant of the conversation.',
        ]);
    }
}
