<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2026 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\mail\services;

use humhub\libs\BasePermission;
use humhub\modules\content\components\ContentContainerPermissionManager;
use humhub\modules\content\models\ContentContainerPermission;
use humhub\modules\friendship\models\Friendship;
use humhub\modules\mail\helpers\Url;
use humhub\modules\mail\permissions\SendMail;
use humhub\modules\mail\permissions\StartConversation;
use humhub\modules\user\components\UserList;
use humhub\modules\user\models\User;
use Yii;

/**
 * Whether the current user may write to other users — the "Send message" action on the cards of
 * the People directory (`vue/components/MailPeopleCardAction.vue`), answered by
 * `GET /api/v2/mail/recipient-states` ({@see \humhub\modules\mail\controllers\api\RecipientController}).
 *
 * The rules are the ones of the profile's "Send message" entry ({@see \humhub\modules\mail\Events::onProfileHeaderControlsMenuInit()})
 * and of `mail/mail/create`: the caller may start conversations, and the recipient may receive
 * them from the caller ({@see SendMail}, a profile permission) unless the caller is an
 * administrator. A page of users is answered with a fixed number of queries, not a number per
 * user.
 *
 * @since 3.5.0
 */
class RecipientStateService
{
    /**
     * @var int the most users one request may name — a card directory asks for the page it
     * displays (as the core's `user/states`)
     */
    public const MAX_IDS = 100;

    /**
     * Whether the current user may start conversations at all: logged in, holding
     * {@see StartConversation}, and not impersonated while impersonation hides private content
     * (the messenger denies access then).
     */
    public static function canStartConversation(): bool
    {
        $user = Yii::$app->user;

        return !$user->isGuest
            && $user->impersonation->canAccessPrivateContent()
            && $user->can(StartConversation::class);
    }

    /**
     * The state of each of the users `$ids` the current user may see, as the People directory
     * lists them (available, not hidden): `canMessage` — whether a conversation with them may be
     * started (never with oneself) — and `url`, the "new conversation" form preselecting them
     * (`null` where `canMessage` is `false`).
     *
     * @param int[] $ids
     * @return array<int, array{canMessage: bool, url: ?string}> keyed by user id
     */
    public function states(array $ids): array
    {
        $ids = array_slice(array_values(array_unique(array_filter(array_map('intval', $ids), fn(int $id) => $id > 0))), 0, self::MAX_IDS);
        $viewer = Yii::$app->user->getIdentity();

        if ($ids === [] || $viewer === null) {
            return [];
        }

        /** @var User[] $users */
        $users = User::find()
            ->available()
            ->andWhere(UserList::notHidden($viewer))
            ->andWhere(['user.id' => $ids])
            ->orderBy(['user.id' => SORT_ASC])
            ->all();

        $canStart = static::canStartConversation();
        $receivers = $canStart ? $this->receivers($viewer, $users) : [];

        $results = [];
        foreach ($users as $user) {
            $canMessage = isset($receivers[$user->id]);
            $results[$user->id] = [
                'canMessage' => $canMessage,
                'url' => $canMessage ? Url::toCreateConversation($user->guid) : null,
            ];
        }

        return $results;
    }

    /**
     * The users of `$users` who accept messages from `$viewer`, as `[userId => true]` — what
     * `$user->can(SendMail::class)` answers per user, with the stored permission states and the
     * friendships read once for all of them.
     *
     * @param User[] $users
     */
    protected function receivers(User $viewer, array $users): array
    {
        $others = array_values(array_filter($users, fn(User $user) => (int)$user->id !== (int)$viewer->id));
        if ($others === []) {
            return [];
        }

        if (Yii::$app->user->isAdmin()) {
            return array_fill_keys(array_map(fn(User $user) => (int)$user->id, $others), true);
        }

        $friends = [];
        if (Yii::$app->getModule('friendship')->isFriendshipEnabled()) {
            $friends = array_flip(array_map('intval', Friendship::getFriendsQuery($viewer)
                ->select('user.id')
                ->andWhere(['user.id' => array_map(fn(User $user) => $user->id, $others)])
                ->column()));
        }

        $permission = new SendMail();
        $stored = [];
        foreach (ContentContainerPermission::find()
            ->select(['contentcontainer_id', 'group_id', 'state'])
            ->where([
                'contentcontainer_id' => array_map(fn(User $user) => $user->contentcontainer_id, $others),
                'module_id' => $permission->getModuleId(),
                'permission_id' => $permission->getId(),
            ])
            ->asArray()
            ->all() as $row) {
            $stored[$row['contentcontainer_id']][$row['group_id']] = $row['state'];
        }

        // The default state of a profile group is the same for every user (the class default,
        // or what the administrator stored for all users).
        $defaultStates = [];
        $defaultManager = new ContentContainerPermissionManager(['contentContainer' => $others[0]]);

        $receivers = [];
        foreach ($others as $user) {
            $group = isset($friends[$user->id]) ? User::USERGROUP_FRIEND : User::USERGROUP_USER;
            $state = $stored[$user->contentcontainer_id][$group]
                ?? ($defaultStates[$group] ??= $defaultManager->getSingleGroupDefaultState($group, $permission));

            if ($state == BasePermission::STATE_ALLOW) {
                $receivers[$user->id] = true;
            }
        }

        return $receivers;
    }
}
