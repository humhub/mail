<?php

namespace tests\codeception\unit;

use humhub\components\access\ControllerAccess;
use humhub\components\access\StrictAccess;
use humhub\modules\mail\controllers\InboxController;
use humhub\modules\mail\controllers\MailController;
use humhub\modules\mail\controllers\TagController;
use humhub\modules\user\components\Impersonation;
use humhub\modules\user\models\User;
use tests\codeception\_support\HumHubDbTestCase;
use Yii;

/**
 * Conversations are private content, so the Messenger is not accessible while an admin impersonates a user,
 * unless the core `Impersonation::$allowPrivateContentAccess` option restores the pre-1.19 behavior.
 *
 * @since 3.4.5
 */
class ImpersonationTest extends HumHubDbTestCase
{
    protected $fixtureConfig = ['default'];

    public function _after()
    {
        $this->configureImpersonation(false);

        parent::_after();
    }

    /**
     * Both impersonation configurations with their expected effect: [allowPrivateContentAccess, access denied]
     */
    private static function impersonationModes(): array
    {
        return [
            'deny private content access (default)' => [false, true],
            'allow private content access' => [true, false],
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
     * Cross product of every impersonation configuration with every Messenger controller
     */
    public static function messengerAccessProvider(): array
    {
        $data = [];

        foreach (static::impersonationModes() as $modeName => [$allowPrivateContentAccess, $expectDenied]) {
            foreach (static::messengerControllers() as $controllerName => [$controllerClass, $action]) {
                $data[$modeName . ' / ' . $controllerName] = [
                    $allowPrivateContentAccess,
                    $expectDenied,
                    $controllerClass,
                    $action,
                ];
            }
        }

        return $data;
    }

    public function testControllersDenyImpersonatedUsers()
    {
        foreach (static::messengerControllers() as [$controllerClass, $action]) {
            $this->assertContains(
                [ControllerAccess::RULE_DENY_IMPERSONATED],
                $this->getAccessRules($controllerClass),
                $controllerClass . ' must be denied while impersonating',
            );
        }
    }

    /**
     * @dataProvider messengerAccessProvider
     */
    public function testMessengerAccess(
        bool $allowPrivateContentAccess,
        bool $expectDenied,
        string $controllerClass,
        string $action,
    ) {
        $this->configureImpersonation($allowPrivateContentAccess);
        $this->becomeUser('Admin');

        $this->assertTrue(
            $this->hasAccess($controllerClass, $action),
            'A user which does not impersonate always has access to the Messenger',
        );

        $this->startImpersonation('User1');

        $this->assertSame(
            !$expectDenied,
            $this->hasAccess($controllerClass, $action),
            'Messenger access while impersonating',
        );

        $this->assertTrue(Yii::$app->user->impersonation->stop());

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

    private function configureImpersonation(bool $allowPrivateContentAccess): Impersonation
    {
        $impersonation = Yii::$app->user->impersonation;
        $impersonation->allowPrivateContentAccess = $allowPrivateContentAccess;

        return $impersonation;
    }

    private function startImpersonation(string $userName): void
    {
        $this->assertFalse(Yii::$app->user->impersonation->isActive());

        $this->assertTrue(Yii::$app->user->impersonation->start(User::findOne(['username' => $userName])));

        $this->assertTrue(Yii::$app->user->impersonation->isActive());
    }
}
