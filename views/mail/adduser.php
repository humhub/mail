<div class="modal-dialog">
    <div class="modal-content">
        <?php
        $form = $this->beginWidget('CActiveForm', array(
            'id' => 'invite-message-form',
            'enableAjaxValidation' => false,
        ));
        ?>
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"
                id="myModalLabel"><?php echo Yii::t("MailModule.views_mail_adduser", "Add more participants to your conversation..."); ?></h4>
        </div>
        <div class="modal-body">
            <?php echo $form->textField($inviteForm, 'recipient', array('id' => 'addUserFrom_mail')); ?>
            <?php echo $form->error($inviteForm, 'recipient'); ?>
            <?php
            // attach mention widget to it
            $this->widget('application.modules_core.user.widgets.UserPickerWidget', array(
                'inputId' => 'addUserFrom_mail',
                'model' => $inviteForm, // CForm Instanz
                'attribute' => 'recipient',
                'userGuid' => Yii::app()->user->guid,
                'focus' => true,
            ));
            ?>
        </div>
        <div class="modal-footer">
            <hr/>
            <?php
            echo HHtml::ajaxButton(Yii::t('MailModule.views_mail_adduser', 'Send'), array('//mail/mail/adduser', 'id' => $inviteForm->message->id), array(
                'type' => 'POST',
                'beforeSend' => 'function(){ $("#adduser-loader").removeClass("hidden"); }',
                'success' => 'function(html){ $("#globalModal").html(html); }',
                    ), array('class' => 'btn btn-primary'));
            ?>
            <button type="button" class="btn btn-primary"
                    data-dismiss="modal"><?php echo Yii::t('MailModule.views_mail_adduser', 'Close'); ?></button>

            <div class="col-md-1 modal-loader">
                <div id="adduser-loader" class="loader loader-small hidden"></div>
            </div>
        </div>

        <?php $this->endWidget(); ?>
    </div>
</div>


