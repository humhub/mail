<?php

namespace humhub\modules\mail\models;

use humhub\components\ActiveRecord;
use humhub\modules\mail\Module;
use humhub\modules\ui\icon\widgets\Icon;
use humhub\modules\user\models\User;
use Yii;
use yii\db\Expression;
use yii\helpers\Html;

/**
 * This class represents a single conversation.
 *
 * The followings are the available columns in table 'message':
 * @property int $id
 * @property string $title
 * @property string $created_at
 * @property int $created_by
 * @property string $updated_at
 * @property int $updated_by
 * @property-read  User $originator
 * @property-read MessageEntry $lastEntry
 * @property-read MessageEntry $lastEntryRelation
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
    private ?MessageEntry $_lastEntry = null;
    private ?int $_userCount = null;

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
            [['title'], 'trim'],
            [['created_at', 'updated_at'], 'safe'],
        ];
    }

    public function getEntryUpdates($from = null)
    {
        // Eager-load the author so ConversationEntry::isOwnMessage() / the entry template
        // don't lazy-load a User row per entry.
        $query = $this->hasMany(MessageEntry::class, ['message_id' => 'id'])->with('user');
        $query->addOrderBy(['created_at' => SORT_ASC]);

        // Normalize $from: only a strictly positive integer counts as a valid cursor.
        // (Avoids PHP loose-comparison pitfalls, e.g. the string "0" being falsy but != null.)
        $from = is_numeric($from) ? (int) $from : null;

        if ($from !== null && $from > 0) {
            $query->andWhere(['>', 'message_entry.id', $from]);
        }

        // Always bound the result set, otherwise an invalid/empty cursor (e.g. from=0 or
        // no cursor at all) would load the entire conversation in one go.
        $query->limit(Module::getModuleInstance()->conversationUpdatePageSize);

        return $query;
    }

    /**
     * @param int|null $from
     * @return MessageEntry[]
     */
    public function getEntryPage($from = null)
    {
        // Eager-load the author (see getEntryUpdates()) - covers the initial /mail/show render
        // and /mail/load-more, not just /mail/update.
        $query = $this->getEntries()->with('user');
        $query->addOrderBy(['created_at' => SORT_DESC]);

        // Normalize $from: only a strictly positive integer counts as a valid cursor.
        $from = is_numeric($from) ? (int) $from : null;
        $hasCursor = ($from !== null && $from > 0);

        if ($hasCursor) {
            $query->andWhere(['<', 'message_entry.id', $from]);
        }

        $module = Module::getModuleInstance();
        $limit = $hasCursor ? $module->conversationUpdatePageSize : $module->conversationInitPageSize;
        $query->limit($limit);

        return array_reverse($query->all());
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEntries()
    {
        return $this->hasMany(MessageEntry::class, ['message_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * @param int|null $userId
     * @return UserMessage|null
     */
    public function getUserMessage($userId = null)
    {
        if (!$userId) {
            if (Yii::$app->user->isGuest) {
                return null;
            }
            $userId = Yii::$app->user->id;
        }

        return UserMessage::findOne([
            'user_id' => $userId,
            'message_id' => $this->id,
        ]);
    }

    /**
     * @param User|int|string|null $user
     * @return bool
     */
    public function isParticipant($user = null): bool
    {
        $userId = $user instanceof User
            ? $user->id
            : (is_scalar($user) ? (int) $user : Yii::$app->user->id);

        if (!$userId) {
            return false;
        }

        // Already eager-loaded (e.g. inbox list via InboxFilterForm) - check in memory, no query.
        if ($this->isRelationPopulated('users')) {
            foreach ($this->users as $participant) {
                if ($participant->id === $userId) {
                    return true;
                }
            }

            return false;
        }

        return UserMessage::find()
            ->where(['message_id' => $this->id, 'user_id' => $userId])
            ->exists();
    }

    /**
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getUsers()
    {
        return $this->hasMany(User::class, ['id' => 'user_id'])
            ->viaTable('user_message', ['message_id' => 'id']);
    }

    public function getUsersCount(): int
    {
        // Already eager-loaded (e.g. inbox list via InboxFilterForm) - count in memory, no query.
        if ($this->isRelationPopulated('users')) {
            return count($this->users);
        }

        if ($this->_userCount === null) {
            $this->_userCount = $this->getUsers()->count();
        }

        return $this->_userCount;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOriginator()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => Yii::t('MailModule.base', 'Subject'),
            'created_at' => Yii::t('MailModule.base', 'Created At'),
            'created_by' => Yii::t('MailModule.base', 'Created By'),
            'updated_at' => Yii::t('MailModule.base', 'Updated At'),
            'updated_by' => Yii::t('MailModule.base', 'Updated By'),
        ];
    }

    /**
     * Relation declaration for the last entry of this conversation. Kept as a relation so
     * isRelationPopulated()/populateRelation() work with it (see getLastEntry() and
     * populateLastEntries() below), but it's intentionally NOT meant to be batch-eager-loaded via
     * ->with('lastEntryRelation'): Yii's hasOne + ORDER BY DESC bucketing trick correctly resolves
     * to the newest entry per message_id, but to do so it first has to fetch *every* entry (with
     * full content) of *every* conversation in the batch before discarding all but one row per
     * conversation - unbounded for long conversations. Use populateLastEntries() instead to fill
     * this relation for a batch of messages with one cheap, properly scoped query.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLastEntryRelation()
    {
        return $this->hasOne(MessageEntry::class, ['message_id' => 'id'])
            ->orderBy(['created_at' => SORT_DESC]);
    }

    /**
     * Batch-loads the last entry (with its author eager-loaded) of each given Message and
     * populates the 'lastEntryRelation' relation on each - the N+1-safe alternative to
     * ->with('lastEntryRelation') (see the doc comment on getLastEntryRelation()).
     *
     * Runs 2 queries total regardless of how many messages are passed: one self-join query finds
     * and fetches the last entry per message_id in a single round-trip (a MAX(id) GROUP BY,
     * scoped to just the given messages, joined back onto message_entry for the full row), and
     * one more for the eager-loaded authors.
     *
     * @param Message[] $messages
     */
    public static function populateLastEntries(array $messages): void
    {
        if (empty($messages)) {
            return;
        }

        $messageIds = array_unique(array_map(fn(Message $message) => $message->id, $messages));

        $lastPerMessage = MessageEntry::find()
            ->select(['message_id', 'max_id' => new Expression('MAX(id)')])
            ->where(['message_id' => $messageIds])
            ->groupBy('message_id');

        $lastEntries = MessageEntry::find()
            ->innerJoin(['last' => $lastPerMessage], 'last.message_id = message_entry.message_id AND last.max_id = message_entry.id')
            ->with('user')
            ->indexBy('message_id')
            ->all();

        foreach ($messages as $message) {
            $message->populateRelation('lastEntryRelation', $lastEntries[$message->id] ?? null);
        }
    }

    /**
     * Returns the last message of this conversation
     * @return MessageEntry|null
     */
    public function getLastEntry(): ?MessageEntry
    {
        if ($this->isRelationPopulated('lastEntryRelation')) {
            return $this->lastEntryRelation;
        }

        if ($this->_lastEntry === null) {
            $this->_lastEntry = MessageEntry::find()
                ->where(['message_id' => $this->id])
                ->orderBy('created_at DESC')
                ->limit(1)
                ->one();
        }

        return $this->_lastEntry;
    }

    /**
     * @param bool $includeMe
     * @return \yii\web\IdentityInterface|null|User
     * @throws \Throwable
     * @throws \yii\base\InvalidConfigException
     */
    public function getLastActiveParticipant(bool $includeMe = false): User
    {
        // In a conversation with at most 2 people, "the last active participant who isn't me" can
        // only ever be the other participant - no need for a dedicated query if we already have the
        // (eager-loaded) participant list in memory.
        if (!$includeMe && $this->isRelationPopulated('users') && count($this->users) <= 2) {
            foreach ($this->users as $participant) {
                if ($participant->id !== Yii::$app->user->id) {
                    return $participant;
                }
            }
        }

        $query = MessageEntry::find()->where(['message_id' => $this->id])->orderBy('created_at DESC')->limit(1);

        if (!$includeMe) {
            $query->andWhere(['<>', 'user_id', Yii::$app->user->id]);
        }

        $entry = $query->one();

        $user = $entry ? $entry->user : $this->getUsers()->andWhere(['<>', 'user.id', Yii::$app->user->id])->one();

        return $user ?: Yii::$app->user->getIdentity();
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
            if ($this->getEntries()->count() > 1) {
                $entry->delete();
            } else {
                $this->delete();
            }
        }
    }

    /**
     * Mark this message as unread
     *
     * @param int|null $userId
     */
    public function markUnread($userId = null)
    {
        $userMessage = $this->getUserMessage($userId);
        if ($userMessage) {
            $userMessage->last_viewed = null;
            $userMessage->save();
        }
    }

    /**
     * Pin this message
     *
     * @param int|null $userId
     * @param bool $pin
     */
    public function pin($userId = null, bool $pin = true)
    {
        $userMessage = $this->getUserMessage($userId);
        if ($userMessage) {
            $userMessage->pinned = $pin;
            $userMessage->save();
        }
    }

    /**
     * Unpin this message
     *
     * @param int|null $userId
     */
    public function unpin($userId = null)
    {
        $this->pin($userId, false);
    }

    /**
     * User leaves a message
     *
     * If it's the last user, the whole message will be deleted.
     *
     * @param int|null $userId
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function leave($userId = null)
    {
        $userMessage = $this->getUserMessage($userId);
        if (!$userMessage) {
            return;
        }

        if (count($this->users) > 1) {
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
        $userMessage = UserMessage::findOne([
            'user_id' => $userId,
            'message_id' => $this->id,
        ]);
        if ($userMessage !== null) {
            $userMessage->last_viewed = date('Y-m-d G:i:s');
            $userMessage->save();
        }
    }

    /**
     * Deletes a message, including all dependencies.
     */
    public function delete()
    {
        foreach (MessageEntry::findAll(['message_id' => $this->id]) as $messageEntry) {
            $messageEntry->delete();
        }

        foreach (UserMessage::findAll(['message_id' => $this->id]) as $userMessage) {
            $userMessage->delete();
        }

        parent::delete();
    }

    /**
     * @param User $recipient
     * @param bool $originator
     * @param bool $informAfterAdd Notify about user joining with state badge
     * @return bool
     */
    public function addRecepient(User $recipient, bool $originator = false, bool $informAfterAdd = true): bool
    {
        $userMessage = new UserMessage([
            'message_id' => $this->id,
            'user_id' => $recipient->id,
            'informAfterAdd' => $informAfterAdd,
        ]);

        if ($originator) {
            $userMessage->is_originator = 1;
            $userMessage->last_viewed = date('Y-m-d G:i:s');
        }

        return $userMessage->save();

    }

    /**
     * Get users which don't want to receive messages from the current User
     *
     * @return User[]
     */
    public function getBlockers(): array
    {
        $blockerUsers = [];

        foreach ($this->users as $user) {
            if (!$user->isCurrentUser() && $user->isBlockedForUser()) {
                $blockerUsers[] = $user;
            }
        }

        return $blockerUsers;
    }

    /**
     * Get names of the users which don't want to receive messages from the current User
     *
     * @param bool Encode names
     * @return string[]
     */
    public function getBlockerNames(bool $encode = true): array
    {
        $blockerNames = [];

        foreach ($this->getBlockers() as $user) {
            $blockerName = $user->getDisplayName();
            if ($encode) {
                $blockerName = Html::encode($blockerName);
            }
            $blockerNames[] = $blockerName;
        }

        return $blockerNames;
    }

    /**
     * Check if current user cannot reply to at least one recipient of this conversation
     *
     * @return bool
     */
    public function isBlocked(): bool
    {
        foreach ($this->users as $user) {
            if (!$user->isCurrentUser() && $user->isBlockedForUser()) {
                return true;
            }
        }

        return false;
    }

    public function isPinned($userId = null): bool
    {
        $userMessage = $this->getUserMessage($userId);
        return $userMessage && $userMessage->pinned;
    }

    /**
     * @param int|null $userId
     * @param bool|null $isPinned pass the already known pinned state (e.g. from a
     * preloaded UserMessage) to avoid an extra lookup via {@see isPinned()}
     * @return Icon|null
     */
    public function getPinIcon($userId = null, ?bool $isPinned = null): ?Icon
    {
        if ($isPinned ?? $this->isPinned($userId)) {
            return Icon::get('map-pin')
                ->tooltip(Yii::t('MailModule.base', 'Pinned'))
                ->color('var(--bs-danger)');
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function refresh()
    {
        $this->_lastEntry = null;
        return parent::refresh();
    }

    public function canEditTitle(): bool
    {
        // Allow editing the title only if the message has a title.
        if (!$this->title) {
            return false;
        }

        if (Yii::$app->user->isGuest) {
            return false;
        }

        $currentUser = Yii::$app->user->getIdentity();

        // Only the creator is allowed
        if ($this->created_by === $currentUser->id) {
            return true;
        }

        // But if there's only 1 participant left, then this participant should be allowed as well
        if ($this->getUsersCount() === 1 && $this->isParticipant($currentUser)) {
            return true;
        }

        return false;
    }
}
