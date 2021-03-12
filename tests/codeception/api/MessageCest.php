<?php

namespace mail\api;

use Codeception\Util\HttpCode;
use mail\ApiTester;

class MessageCest
{
    public function testList(ApiTester $I)
    {
        if (!$I->isRestModuleEnabled()) {
            return;
        }

        $I->amAdmin();

        $I->sendGet('mail');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'total' => 0,
            'page' => 1,
            'pages' => 0,
            'links' => ['self' => '/api/v1/mail?page=1&per-page=100'],
            'results' => [],
        ]);
    }
}
