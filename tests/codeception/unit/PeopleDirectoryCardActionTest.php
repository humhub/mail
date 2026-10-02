<?php

namespace tests\codeception\unit;

use humhub\libs\BasePermission;
use humhub\modules\mail\assets\MailVueAsset;
use humhub\modules\mail\permissions\StartConversation;
use humhub\modules\user\widgets\PeopleDirectory;
use tests\codeception\_support\HumHubDbTestCase;
use Yii;

/**
 * The "Send message" action on the cards of the People directory is registered with the
 * directory, for a viewer who may start conversations only.
 *
 * @since 3.5.0
 */
class PeopleDirectoryCardActionTest extends HumHubDbTestCase
{
    protected $fixtureConfig = ['default'];

    public function testActionIsRegisteredWithTheDirectory()
    {
        $this->becomeUser('User1');

        $this->renderDirectory();

        $this->assertArrayHasKey(MailVueAsset::class, Yii::$app->view->assetBundles);
    }

    public function testActionIsNotRegisteredWithoutPermissionToStartConversations()
    {
        $user = $this->becomeUser('User1');
        foreach ($user->groups as $group) {
            Yii::$app->user->permissionManager->setGroupState($group->id, new StartConversation(), BasePermission::STATE_DENY);
        }
        Yii::$app->user->permissionManager->clear();

        $this->renderDirectory();

        $this->assertArrayNotHasKey(MailVueAsset::class, Yii::$app->view->assetBundles);
    }

    private function renderDirectory(): void
    {
        Yii::$app->view->assetBundles = [];
        PeopleDirectory::widget();
    }
}
