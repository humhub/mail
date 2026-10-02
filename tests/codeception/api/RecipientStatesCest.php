<?php

namespace mail\api;

use humhub\libs\BasePermission;
use humhub\modules\mail\helpers\Url;
use humhub\modules\mail\permissions\SendMail;
use humhub\modules\user\models\User;
use mail\ApiTester;
use PHPUnit\Framework\Assert;

/**
 * `GET /api/v2/mail/recipient-states` — whether the caller may write to the users of a People
 * directory page (the "Send message" card action). A core `/api/v2` endpoint with browser-session
 * authentication; it does not need the rest module. The suite's REST base is `/api/v1`, hence
 * the absolute URLs.
 *
 * @since 3.5.0
 */
class RecipientStatesCest
{
    private const URL = 'http://localhost:8080/api/v2/mail/recipient-states';

    public function testRequiresAuthentication(ApiTester $I)
    {
        $I->wantTo('be rejected without a session');
        $I->sendGet(self::URL, ['ids' => '2,3']);
        $I->seeResponseCodeIs(401);
    }

    public function testStatesOfThePage(ApiTester $I)
    {
        $I->wantTo('learn whom of the users I am shown I may write to');
        User::findOne(4)->getPermissionManager()->setGroupState(User::USERGROUP_USER, new SendMail(), BasePermission::STATE_DENY);

        $I->amLoggedInAs(2);
        $I->sendGet(self::URL, ['ids' => '2,3,4,5']);

        $I->seeResponseCodeIs(200);
        $results = json_decode($I->grabResponse(), true)['results'];

        Assert::assertSame([2, 3, 4], array_keys($results), 'the disabled user 5 is left out');
        Assert::assertSame(['canMessage' => false, 'url' => null], $results[2], 'no conversation with oneself');
        Assert::assertSame(['canMessage' => true, 'url' => Url::toCreateConversation(User::findOne(3)->guid)], $results[3]);
        Assert::assertSame(['canMessage' => false, 'url' => null], $results[4], 'user 4 does not accept messages from users');
    }

    public function testEmptyRequestAnswersAnEmptyObject(ApiTester $I)
    {
        $I->amLoggedInAs(2);
        $I->sendGet(self::URL);

        $I->seeResponseCodeIs(200);
        Assert::assertSame('{"results":{}}', $I->grabResponse());
    }
}
