<?php if (count($userMessages) != 0) : ?>
    <?php foreach ($userMessages as $userMessage) : ?>
        <?= $this->render('_messagePreview', ['userMessage' => $userMessage]); ?>
    <?php endforeach; ?>
<?php else : ?>
    <li class="placeholder"> <?= Yii::t('MailModule.views_mail_list', 'There are no messages yet.'); ?></li>
<?php endif; ?>