<div class="container">
    <div class="row">
        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <?php echo Yii::t('MailModule.views_mail_index', 'Inbox') ?>
                    <?php echo CHtml::link(Yii::t('MailModule.views_mail_index', 'New message'), $this->createUrl('//mail/mail/create'), array('class' => 'btn btn-info pull-right', 'data-toggle' => 'modal', 'data-target' => '#globalModal')); ?>
                </div>

                <hr>
                <ul id="inbox" class="media-list">
                    <?php if (count($userMessages) != 0) : ?>
                        <?php foreach ($userMessages as $userMessage) : ?>
                            <?php $this->renderPartial('_messagePreview', array('userMessage' => $userMessage)); ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="placeholder"><?php echo Yii::t('MailModule.views_mail_index', 'There are no messages yet.'); ?></li>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="pagination-container">
                <?php
                $this->widget('CLinkPager', array(
                    'pages' => $pagination,
                    'maxButtonCount' => 5,
                    'header' => '',
                    'nextPageLabel' => '<i class="fa fa-step-forward"></i>',
                    'prevPageLabel' => '<i class="fa fa-step-backward"></i>',
                    'firstPageLabel' => '<i class="fa fa-fast-backward"></i>',
                    'lastPageLabel' => '<i class="fa fa-fast-forward"></i>',
                    'htmlOptions' => array('class' => 'pagination'),
                ));
                ?>
            </div>
        </div>
        <div class="col-md-8 messages">
            <div id="mail_message_details">
                <div class="loader"></div>
            </div>
        </div>
    </div>
</div>
