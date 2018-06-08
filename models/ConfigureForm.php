<?php

namespace humhub\modules\mail\models;

use Yii;
use humhub\modules\mail\Module;

/**
 * ConfigureForm defines the configurable fields.
 *
 * @package humhub\modules\mail\models
 * @author Akopian Gaik
 */
class ConfigureForm extends \yii\base\Model
{

    public $showInTopNav;

    public function init()
    {
        parent::init();
        $module = $this->getModule();
        $this->showInTopNav = !$module->showInTopNav();
    }

    /**
     * @return Module
     */
    public function getModule()
    {
        return Yii::$app->getModule('mail');
    }

    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return [
            ['showInTopNav', 'boolean']
        ];
    }

    /**
     * Declares customized attribute labels.
     * If not declared here, an attribute would have a label that is
     * the same as its name with the first letter in upper case.
     */
    public function attributeLabels()
    {
        return [
            'showInTopNav' => Yii::t('CfilesModule.base', 'Show icon on top navigation.')
        ];
    }

    public function save()
    {
        if(!$this->validate()) {
            return false;
        }

        $module = $this->getModule();
        $module->settings->set('showInTopNav', $this->showInTopNav);
        return true;
    }
}
