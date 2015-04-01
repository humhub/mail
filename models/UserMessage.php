<?php

/**
 * This is the model class for table "user_message".
 *
 * The followings are the available columns in table 'user_message':
 * @property integer $message_id
 * @property integer $user_id
 * @property integer $is_originator
 * @property string $last_viewed
 * @property string $created_at
 * @property integer $created_by
 * @property string $updated_at
 * @property integer $updated_by
 *
 * @package humhub.modules.mail.models
 * @since 0.5
 */
class UserMessage extends HActiveRecord
{

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return UserMessage the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'user_message';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('message_id, user_id', 'required'),
            array('message_id, user_id, is_originator, created_by, updated_by', 'numerical', 'integerOnly' => true),
            array('last_viewed, created_at, updated_at', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('message_id, user_id, is_originator, last_viewed, created_at, created_by, updated_at, updated_by', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'message' => array(self::BELONGS_TO, 'Message', 'message_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'message_id' => 'Message',
            'user_id' => 'User',
            'is_originator' => 'Is Originator',
            'last_viewed' => 'Last Viewed',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        );
    }

}
