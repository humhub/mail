<?php

namespace humhub\modules\mail\models;

use Yii;
use DateTime;
use DateInterval;
use humhub\modules\mail\Module;
use humhub\modules\user\models\User;
use humhub\modules\mail\models\states\MessageUserLeft;
use humhub\modules\mail\models\states\MessageUserJoined;

/**
 * ConfigureForm defines the configurable fields.
 *
 * @package humhub\modules\mail\models
 * @author Akopian Gaik
 */
class Config extends \yii\base\Model
{
    public $showInTopNav;

    public $newUserRestrictionEnabled = 0;

    public $newUserSinceDays = 3;

    public $newUserConversationRestriction = 2;

    public $newUserMessageRestriction = 5;

    public $userConversationRestriction = 15;

    public $userMessageRestriction = null;

    public $enableMessageUserJoined = true;

    public $enableMessageUserLeft = true;

    /**
     * The settings array, containing the necessary settings for the MessageUserJoined and MessageUserLeft states.
     *
     * @var array
     */
    private $settings = [
        MessageUserJoined::SETTING_ENABLE_MESSAGE_USER_JOINED => true,
        MessageUserLeft::SETTING_ENABLE_MESSAGE_USER_LEFT => true,
    ];

    public function init()
    {
        parent::init();
        $module = $this->getModule();
        $this->showInTopNav = !$module->hideInTopNav();
        $this->newUserRestrictionEnabled = (int) $module->settings->get('newUserRestrictionEnabled', $this->newUserRestrictionEnabled);
        $this->newUserSinceDays = (int) $module->settings->get('newUserSinceDays', $this->newUserSinceDays);
        $this->newUserConversationRestriction = (int) $module->settings->get('newUserConversationRestriction', $this->newUserConversationRestriction);
        $this->newUserMessageRestriction = (int) $module->settings->get('newUserMessageRestriction', $this->newUserMessageRestriction);
        $this->userConversationRestriction = (int) $module->settings->get('userConversationRestriction', $this->userConversationRestriction);
        $this->userMessageRestriction = (int) $module->settings->get('userMessageRestriction', $this->userMessageRestriction);
        $this->enableMessageUserJoined = (bool) $module->settings->get('enableMessageUserJoined', $this->enableMessageUserJoined);
        $this->enableMessageUserLeft = (bool) $module->settings->get('enableMessageUserLeft', $this->enableMessageUserLeft);
    }

    /**
     * @return Module
     */
    public static function getModule()
    {
        return Yii::$app->getModule('mail');
    }

    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return [
            [['showInTopNav', 'newUserRestrictionEnabled', 'enableMessageUserJoined', 'enableMessageUserLeft'], 'boolean'],
            [['newUserConversationRestriction',
                'newUserMessageRestriction',
                'userConversationRestriction',
                'userMessageRestriction'], 'integer', 'min' => 0],
            ['newUserSinceDays', 'integer', 'min' => 1],
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
            'showInTopNav' => Yii::t('MailModule.base', 'Show menu item in top Navigation'),
            'newUserRestrictionEnabled' => Yii::t('MailModule.base', 'Seperate restrictions for new users'),
            'newUserSinceDays' => Yii::t('MailModule.base', 'Until a user is member since (days)'),
            'newUserConversationRestriction' => Yii::t('MailModule.base', 'Max number of new conversations allowed for a new user per day'),
            'newUserMessageRestriction' => Yii::t('MailModule.base', 'Max number of messages allowed for a new user per day'),
            'userConversationRestriction' => Yii::t('MailModule.base', 'Max number of new conversations allowed for a user per day'),
            'userMessageRestriction' => Yii::t('MailModule.base', 'Max messages allowed per day'),
            'enableMessageUserJoined' => Yii::t('MailModule.base', 'Enable "User Joined" message'),
            'enableMessageUserLeft' => Yii::t('MailModule.base', 'Enable "User Left" message'),
        ];
    }

    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        $module = static::getModule();
        $module->settings->set('showInTopNav', $this->showInTopNav);
        $module->settings->set('newUserRestrictionEnabled', $this->newUserRestrictionEnabled);
        $module->settings->set('newUserSinceDays', $this->newUserSinceDays);
        $module->settings->set('newUserConversationRestriction', $this->newUserConversationRestriction);
        $module->settings->set('newUserMessageRestriction', $this->newUserMessageRestriction);
        $module->settings->set('userConversationRestriction', $this->userConversationRestriction);
        $module->settings->set('userMessageRestriction', $this->userMessageRestriction);
        $module->settings->set('enableMessageUserJoined', $this->enableMessageUserJoined);
        $module->settings->set('enableMessageUserLeft', $this->enableMessageUserLeft);
        return true;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        // Update the settings for MessageUserJoined and MessageUserLeft states
        $module = $this->getModule();
        $module->settings->set(MessageUserJoined::SETTING_ENABLE_MESSAGE_USER_JOINED, $this->enableMessageUserJoined);
        $module->settings->set(MessageUserLeft::SETTING_ENABLE_MESSAGE_USER_LEFT, $this->enableMessageUserLeft);
    }

    /**
     * @inheritdoc
     */
    public function afterDelete()
    {
        parent::afterDelete();

        // Inform about user left if the corresponding setting is enabled
        if ($this->settings[MessageUserLeft::SETTING_ENABLE_MESSAGE_USER_LEFT]) {
            MessageUserLeft::inform($this->message, $this->user);
        }
    }

    public function canCreateConversation(User $originator)
    {
        if ($originator->isSystemAdmin()) {
            return true;
        }

        $maxConversations = $this->isNewUser($originator)
            ? $this->newUserConversationRestriction
            : $this->userConversationRestriction;

        return empty($maxConversations) || $this->getConversationCount($originator) < $maxConversations;
    }

    public function isNewUser(User $originator)
    {
        if (empty($this->newUserRestrictionEnabled)) {
            return false;
        }

        return (new DateTime($originator->created_at))->diff(new DateTime())->days < $this->newUserSinceDays;
    }

    public function getConversationCount($originator)
    {
        $module = static::getModule();
        $lastTS = $module->settings->contentContainer($originator)->get('conversationCountTime');

        if (!$lastTS) {
            $module->settings->contentContainer($originator)->set('conversationCountTime', time());
            $module->settings->contentContainer($originator)->set('conversationCount', 0);
            return 0;
        }

        $lastDate = (new \DateTime())->setTimestamp($lastTS);
        $today = (new \DateTime())->setTime(0, 0, 0);

        if ($today > $lastDate) {
            $module->settings->contentContainer($originator)->set('conversationCountTime', time());
            $module->settings->contentContainer($originator)->set('conversationCount', 0);
            return 0;
        }

        return (int) static::getModule()->settings->contentContainer($originator)->get('conversationCount', 0);
    }

    public function reset($originator)
    {
        $module = static::getModule();
        $module->settings->contentContainer($originator)->set('conversationCountTime', null);
        $module->settings->contentContainer($originator)->set('conversationCount', null);
    }

    public function incrementConversationCount($originator)
    {
        static::getModule()->settings->contentContainer($originator)->set('conversationCount', ($this->getConversationCount($originator) + 1));
    }
}
