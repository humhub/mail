
<?php if (count($userMessages) != 0) : ?>
    <?php foreach ($userMessages as $userMessage) : ?>
        <?php echo $this->render('_messagePreview', array('userMessage' => $userMessage)); ?>
    <?php endforeach; ?>
<?php else: ?>
    <li class="placeholder"> <?php echo Yii::t('MailModule.views_mail_list', 'There are no messages yet.'); ?></li>
<?php endif; ?>