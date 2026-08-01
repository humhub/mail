<?php

namespace tests\codeception\unit;

use humhub\components\access\ControllerAccess;
use humhub\components\access\StrictAccess;
use humhub\modules\admin\Module as AdminModule;
use humhub\modules\mail\controllers\InboxController;
use humhub\modules\mail\controllers\MailController;
use humhub\modules\mail\controllers\TagController;
use humhub\modules\user\models\User;
use tests\codeception\_support\HumHubDbTestCase;
use Yii;

/**
 * Conversations are private content, so the Messenger is not accessible while an admin impersonates a user,
 * unless the core `AdminModule::$impersonateMode` allows the access to private content.
 *
 * @since 3.4.5
 */
class ImpersonateModeTest extends HumHubDbTestCase
{
    protected $fixtureConfig = ['default'];

    public function _after()
    {
        $this->setImpersonateMode(AdminModule::IMPERSONATE_MODE_DENY_PRIVATE_CONTENT_LOGGED);

        parent::_after();
    }

    /**
     * All impersonation modes with their expected effect: [mode, private content is denied]
     */
    private static function impersonateModes(): array
    {
        return [
            'full access' => [AdminModule::IMPERSONATE_MODE_FULL_ACCESS, false],
            'full access, logged' => [AdminModule::IMPERSONATE_MODE_FULL_ACCESS_LOGGED, false],
            'deny private content' => [AdminModule::IMPERSONATE_MODE_DENY_PRIVATE_CONTENT, true],
            'deny private content, logged' => [AdminModule::IMPERSONATE_MODE_DENY_PRIVATE_CONTENT_LOGGED, true],
        ];
    }

    /**
     * All Messenger controllers which give access to conversations: [controller class, action id]
     */
    private static function messengerControllers(): array
    {
        return [
            'conversation' => [MailController::class, 'index'],
            'inbox' => [InboxController::class, 'index'],
            'tag' => [TagController::class, 'manage'],
        ];
    }

    /**
     * Cross product of every impersonation mode with every Messenger controller
     */
    public static function messengerAccessProvider(): array
    {
        $data = [];

        foreach (static::impersonateModes() as $modeName => [$mode, $expectDenied]) {
            foreach (static::messengerControllers() as $controllerName => [$controllerClass, $action]) {
                $data[$modeName . ' / ' . $controllerName] = [$mode, $expectDenied, $controllerClass, $action];
            }
        }

        return $data;
    }

    public function testControllersDeclareThePrivateContentAccessRule()
    {
        foreach (static::messengerControllers() as [$controllerClass, $action]) {
            $this->assertContains(
                [ControllerAccess::RULE_PRIVATE_CONTENT_ACCESS],
                $this->getAccessRules($controllerClass),
                $controllerClass . ' must be marked as giving access to private content',
            );
        }
    }

    /**
     * @dataProvider messengerAccessProvider
     */
    public function testMessengerAccess(string $mode, bool $expectDenied, string $controllerClass, string $action)
    {
        $this->setImpersonateMode($mode);
        $this->becomeUser('Admin');

        $this->assertTrue(
            $this->hasAccess($controllerClass, $action),
            'A user which does not impersonate always has access to the Messenger',
        );

        $this->impersonate('User1');

        $this->assertSame(
            !$expectDenied,
            $this->hasAccess($controllerClass, $action),
            'Messenger access while impersonating',
        );

        $this->assertTrue(Yii::$app->user->restoreImpersonator());

        $this->assertTrue(
            $this->hasAccess($controllerClass, $action),
            'Access is restored once the impersonation has been stopped',
        );
    }

    private function hasAccess(string $controllerClass, string $action): bool
    {
        $access = new StrictAccess(['action' => $action]);
        $access->setRules($this->getAccessRules($controllerClass));

        return $access->run();
    }

    private function getAccessRules(string $controllerClass): array
    {
        $controller = new $controllerClass('mail', Yii::$app->moduleManager->getModule('mail'));

        return $controller->behaviors()['acl']['rules'];
    }

    private function setImpersonateMode(string $mode): AdminModule
    {
        /* @var AdminModule $module */
        $module = Yii::$app->getModule('admin');
        $module->impersonateMode = $mode;

        return $module;
    }

    private function impersonate(string $userName): void
    {
        $this->assertFalse(Yii::$app->user->isImpersonated);

        $this->assertTrue(Yii::$app->user->impersonate(User::findOne(['username' => $userName])));

        $this->assertTrue(Yii::$app->user->isImpersonated);
    }
}
