<?php

namespace humhub\modules\mail\models\forms;

use Yii;
use yii\base\Model;

/**
 * @package humhub.modules.mail.forms
 * @since 0.5
 */
class ReplyMessage extends Model
{

    public $message;

    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return [
            ['message', 'required'],
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
            'message' => Yii::t('MailModule.forms_ReplyMessageForm', 'Message'),
        ];
    }

}
