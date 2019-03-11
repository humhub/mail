<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\mail\controllers;

use Yii;
use humhub\modules\mail\models\Config;
use humhub\models\Setting;

/**
 * ConfigController handles the configuration requests.
 *
 * @package humhub.modules.mail.controllers
 * @author Gaik Akopian
 */
class ConfigController extends \humhub\modules\admin\components\Controller
{

    /**
     * Configuration action for super admins.
     */
    public function actionIndex()
    {
        $form = new Config();

        if ($form->load(Yii::$app->request->post()) && $form->save()) {
            $this->view->saved();
        }

        return $this->render('index', ['model' => $form]);
    }
}

?>
