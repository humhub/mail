<?php

namespace humhub\modules\mail\models;

use humhub\libs\Helpers;
use humhub\libs\MarkdownPreview;
use humhub\modules\content\widgets\richtext\RichText;
use Yii;
use humhub\modules\mail\live\NewUserMessage;
use humhub\modules\mail\notifications\ConversationNotificationCategory;

use humhub\modules\notification\targets\BaseTarget;
use humhub\modules\notification\targets\MailTarget;
use humhub\components\ActiveRecord;
use humhub\models\Setting;
use humhub\modules\user\models\User;
use yii\db\Expression;

/**
 * This is the model class for table "message".
 *
 * The followings are the available columns in table 'message':
 * @property integer $id
 * @property string $title
 * @property string $created_at
 * @property integer $created_by
 * @property string $updated_at
 * @property integer $updated_by
 * @property-read  User $originator
 *
 * The followings are the available model relations:
 * @property MessageEntry[] $messageEntries
 * @property User[] $users
 *
 * @package humhub.modules.mail.models
 * @since 0.5
 */
class Message extends ActiveRecord
{

    /**
     * @return string the associated database table name
     */
    public static function tableName()
    {
        return 'message';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return [
            [['created_by', 'updated_by'], 'integer'],
            [['title'], 'string', 'max' => 255],
            [['created_at', 'updated_at'], 'safe'],
        ];
    }

    public function getEntries($from = null)
    {
        $query = $this->hasMany(MessageEntry::class, ['message_id' => 'id']);
        $query->addOrderBy(['created_at' => SORT_ASC]);

        if($from) {
            $query->andWhere(['>', 'message_entry.id', $from]);
        }

        return $query;
    }

    public function getUsers()
    {
        return $this->hasMany(User::class, ['id' => 'user_id'])
                        ->viaTable('user_message', ['message_id' => 'id']);
    }

    public function getUserMessage($userId = null)
    {
        if(!$userId) {
            $userId = Yii::$app->user->id;
        }

        return UserMessage::findOne([
            'user_id' => $userId,
            'message_id' => $this->id
        ]);
    }

    public function isParticipant($user)
    {
        foreach ($this->users as $participant) {
            if ($participant->guid === $user->guid) {
                return true;
            }
        }
        return false;
    }

    public function getOriginator()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'title' => Yii::t('MailModule.base', 'Title'),
            'created_at' => Yii::t('MailModule.base', 'Created At'),
            'created_by' => Yii::t('MailModule.base', 'Created By'),
            'updated_at' => Yii::t('MailModule.base', 'Updated At'),
            'updated_by' => Yii::t('MailModule.base', 'Updated By'),
        );
    }

    /**
     * Returns the last message of this conversation
     */
    public function getLastEntry()
    {
        return MessageEntry::find()->where(['message_id' => $this->id])->orderBy('created_at DESC')->limit(1)->one();
    }

    /**
     * Deletes message entry by given Id
     * If it's the last entry, the whole message will be deleted.
     *
     * @param MessageEntry $entry
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function deleteEntry($entry)
    {
        if ($entry->message->id == $this->id) {
            if (count($this->entries) > 1) {
                $entry->delete();
            } else {
                $this->delete();
            }
        }
    }

    /**
     * User leaves a message
     *
     * If it's the last user, the whole message will be deleted.
     *
     * @param int $userId
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function leave($userId)
    {
        $userMessage = UserMessage::findOne(array(
                    'message_id' => $this->id,
                    'user_id' => $userId
        ));

        if (count($this->users) > 2) {
            $userMessage->delete();
        } else {
            $this->delete();
        }
    }

    /**
     * Marks a message as seen for given userId
     *
     * @param int $userId
     */
    public function seen($userId)
    {
        // Update User Message Entry
        $userMessage = UserMessage::findOne(array(
                    'user_id' => $userId,
                    'message_id' => $this->id
        ));
        if ($userMessage !== null) {
            $userMessage->last_viewed = new Expression('NOW()');
            $userMessage->save();
        }
    }

    /**
     * Deletes a message, including all dependencies.
     */
    public function delete()
    {
        foreach (MessageEntry::findAll(array('message_id' => $this->id)) as $messageEntry) {
            $messageEntry->delete();
        }

        foreach (UserMessage::findAll(array('message_id' => $this->id)) as $userMessage) {
            $userMessage->delete();
        }

        parent::delete();
    }

    /**
     * Notify given user, about this message
     * An email will sent.
     * @param $user User
     */
    public function notify($user)
    {
        /* @var $mailTarget BaseTarget */
        $mailTarget = Yii::$app->notification->getTarget(MailTarget::class);

        if(!$mailTarget || !$mailTarget->isCategoryEnabled(new ConversationNotificationCategory(), $user)) {
            return;
        }

        $andAddon = "";
        if (count($this->users) > 2) {
            $counter = count($this->users) - 1;
            $andAddon = Yii::t('MailModule.models_Message', "and {counter} other users", ["{counter}" => $counter]);
        }

        Yii::setAlias('@mailmodule', Yii::$app->getModule('mail')->getBasePath());

        Yii::$app->i18n->setUserLocale($user);

        $mail = Yii::$app->mailer->compose([
            'html' => '@mailmodule/views/emails/NewMessage',
            'text' => '@mailmodule/views/emails/plaintext/NewMessage'
        ], [
            'message' => $this,
            'originator' => $this->originator,
            'andAddon' => $andAddon,
            'entry' => $this->getLastEntry(),
            'user' => $user,
        ]);

        $mail->setFrom([Yii::$app->settings->get('mailer.systemEmailAddress') => Yii::$app->settings->get('mailer.systemEmailName')]);
        $mail->setTo($user->email);
        $mail->setSubject(Yii::t('MailModule.models_Message', 'New message from {senderName}', array("{senderName}" => \yii\helpers\Html::encode($this->originator->displayName))));
        $mail->send();

        Yii::$app->i18n->autosetLocale();
    }

    public function getPreview()
    {
        return RichText::preview($this->getLastEntry()->content, 300);
    }

    /**
     * @param User $recipient
     * @return bool
     */
    public function addRecepient(User $recipient, $originator = false)
    {
        $userMessage = new UserMessage([
            'message_id' => $this->id,
            'user_id' => $recipient->id
        ]);

        if($originator) {
            $userMessage->is_originator = 1;
            $userMessage->last_viewed = new Expression('NOW()');
        }

        return $userMessage->save();

    }
}
