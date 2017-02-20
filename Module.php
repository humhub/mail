<?php

namespace humhub\modules\mail;

use humhub\modules\user\models\User;
use humhub\modules\space\models\Space;

/**
 * MailModule provides messaging functions inside the application.
 *
 * @package humhub.modules.mail
 * @since 0.5
 */
class Module extends \humhub\components\Module
{

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return  ('Mail');
    }

    /**
     * @inheritdoc
     */
    public function getPermissions($contentContainer = null)
    {
        if ($contentContainer instanceof Space) {
            return [
                new permissions\SendMail()
            ];
        } elseif ($contentContainer instanceof User) {
            return [
                new permissions\RecieveMail(),
            ];
        }

        return [
            new permissions\SendMail(),
            new permissions\RecieveMail(),
        ];
    }

}
