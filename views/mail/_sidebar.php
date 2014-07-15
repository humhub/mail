<div class="well sidebar-nav">
    <ul class="nav nav-list">
        <li class="nav-header">List header</li>
        <li class="<?php if($this->action->id == "members") { echo "active"; } ?>">
            <a href="<?php echo Yii::app()->createUrl('//community/members'); ?>">
                <div class="entry">
                    <div class="user_details"><?php echo Yii::t('MailModule.views_mail_sidebar', 'Members'); ?></div>
                </div>
            </a>
        </li>
        <li class="<?php if($this->action->id == "groups") { echo "active"; } ?>">
            <a href="<?php echo Yii::app()->createUrl('//community/groups'); ?>">
                <div class="entry">
                    <div class="edit_group"><?php echo Yii::t('MailModule.views_mail_sidebar', 'Groups'); ?></div>
                </div>
            </a>
        </li>
        <li class="<?php if($this->action->id == "workspaces") { echo "active"; } ?>">
            <a href="<?php echo Yii::app()->createUrl('//community/workspaces'); ?>">
                <div class="entry">
                    <div class="workspaces"><?php echo Yii::t('MailModule.views_mail_sidebar', 'Spaces'); ?></div>
                </div>
            </a>
        </li>
        <li class="<?php if($this->action->id == "userPosts") { echo "active"; } ?>">
            <a href="<?php echo Yii::app()->createUrl('//community/userPosts'); ?>">
                <div class="entry">
                    <div class="stream"><?php echo Yii::t('MailModule.views_mail_sidebar', 'User Posts'); ?></div>
                </div>
            </a>
        </li>
    </ul>
</div>