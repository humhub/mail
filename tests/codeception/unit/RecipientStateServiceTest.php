<?php

namespace tests\codeception\unit;

use humhub\libs\BasePermission;
use humhub\modules\mail\helpers\Url;
use humhub\modules\mail\permissions\SendMail;
use humhub\modules\mail\services\RecipientStateService;
use humhub\modules\user\models\User;
use tests\codeception\_support\HumHubDbTestCase;
use Yii;

/**
 * Whether the current user may write to the users of a People directory page — the states behind
 * the "Send message" card action (`GET /api/v2/mail/recipient-states`).
 *
 * Fixture ground truth: the enabled users are 1 (`Admin`), 2 (`User1`), 3 (`User2`), 4 (`User3`)
 * and 8 (`AdminNotMember`); 5 is disabled. The friendship system is off.
 *
 * @since 3.5.0
 */
class RecipientStateServiceTest extends HumHubDbTestCase
{
    protected $fixtureConfig = ['default'];

    public function _after()
    {
        Yii::$app->getModule('friendship')->settings->set('enable', 0);
        Yii::$app->user->impersonation->allowPrivateContentAccess = false;

        parent::_after();
    }

    public function testUsersMayBeMessagedByDefault()
    {
        $this->becomeUser('User1');

        $states = (new RecipientStateService())->states([1, 2, 3, 4, 5]);

        $this->assertSame([1, 2, 3, 4], array_keys($states), 'a disabled user is left out');
        $this->assertSame(['canMessage' => false, 'url' => null], $states[2], 'no conversation with oneself');
        $this->assertSame(['canMessage' => true, 'url' => Url::toCreateConversation(User::findOne(3)->guid)], $states[3]);
        $this->assertTrue($states[1]['canMessage']);
        $this->assertTrue($states[4]['canMessage']);
    }

    public function testIdsAreSanitized()
    {
        $this->becomeUser('User1');

        $this->assertSame([3, 4], array_keys((new RecipientStateService())->states(['4', 'x', '-1', 3, '3', 0])));
        $this->assertSame([], (new RecipientStateService())->states([]));
    }

    public function testHiddenUsersAreLeftOut()
    {
        $this->setVisibility(4, User::VISIBILITY_HIDDEN);
        $this->becomeUser('User1');

        $this->assertSame([3], array_keys((new RecipientStateService())->states([3, 4])));
    }

    public function testRecipientDenyingMessagesCanBeMessagedByAdministratorsOnly()
    {
        $this->denySendMail(3, User::USERGROUP_USER);

        $this->becomeUser('User1');
        $states = (new RecipientStateService())->states([3, 4]);
        $this->assertSame(['canMessage' => false, 'url' => null], $states[3]);
        $this->assertTrue($states[4]['canMessage']);
        $this->assertSameAsPermissionManager([3, 4]);

        $this->becomeUser('Admin');
        $this->assertTrue((new RecipientStateService())->states([3])[3]['canMessage'], 'an administrator may write to anyone');
    }

    public function testFriendsMayWriteWhereOtherUsersMayNot()
    {
        Yii::$app->getModule('friendship')->settings->set('enable', 1);
        $this->denySendMail(3, User::USERGROUP_USER);
        $this->denySendMail(4, User::USERGROUP_USER);
        $this->makeFriends(2, 3);

        $this->becomeUser('User1');
        $states = (new RecipientStateService())->states([3, 4]);

        $this->assertTrue($states[3]['canMessage'], 'user 3 accepts messages from friends');
        $this->assertFalse($states[4]['canMessage'], 'user 4 is no friend of user 1');
        $this->assertSameAsPermissionManager([3, 4]);
    }

    public function testRecipientDenyingMessagesToFriendsIsRespected()
    {
        Yii::$app->getModule('friendship')->settings->set('enable', 1);
        $this->denySendMail(3, User::USERGROUP_FRIEND);
        $this->makeFriends(2, 3);
        $this->makeFriends(2, 4);

        $this->becomeUser('User1');
        $states = (new RecipientStateService())->states([3, 4]);

        $this->assertFalse($states[3]['canMessage']);
        $this->assertTrue($states[4]['canMessage']);
        $this->assertSameAsPermissionManager([3, 4]);
    }

    public function testNobodyCanBeMessagedWhileImpersonating()
    {
        $this->becomeUser('Admin');
        $this->assertTrue(Yii::$app->user->impersonation->start(User::findOne(['username' => 'User1'])));

        try {
            $this->assertFalse(RecipientStateService::canStartConversation());
            $states = (new RecipientStateService())->states([3, 4]);
            $this->assertFalse($states[3]['canMessage']);
            $this->assertFalse($states[4]['canMessage']);
        } finally {
            Yii::$app->user->impersonation->stop();
        }

        $this->assertTrue(RecipientStateService::canStartConversation());
    }

    public function testGuestsCannotStartConversations()
    {
        $this->logout();

        $this->assertFalse(RecipientStateService::canStartConversation());
        $this->assertSame([], (new RecipientStateService())->states([2, 3]));
    }

    /**
     * The batched answer is what the profile permission answers per user.
     */
    private function assertSameAsPermissionManager(array $ids): void
    {
        $states = (new RecipientStateService())->states($ids);
        foreach ($ids as $id) {
            $user = User::findOne($id);
            $user->getPermissionManager()->clear();
            $this->assertSame(
                $user->getPermissionManager()->can(SendMail::class, [], false),
                $states[$id]['canMessage'],
                'user ' . $id,
            );
        }
    }

    private function denySendMail(int $userId, string $group): void
    {
        User::findOne($userId)->getPermissionManager()->setGroupState($group, new SendMail(), BasePermission::STATE_DENY);
    }

    private function makeFriends(int $userId, int $friendId): void
    {
        foreach ([[$userId, $friendId], [$friendId, $userId]] as [$from, $to]) {
            Yii::$app->db->createCommand()->insert('user_friendship', [
                'user_id' => $from,
                'friend_user_id' => $to,
                'created_at' => date('Y-m-d H:i:s'),
            ])->execute();
        }
    }

    private function setVisibility(int $userId, int $visibility): void
    {
        User::updateAll(['visibility' => $visibility], ['id' => $userId]);
    }
}
