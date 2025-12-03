<?php

use humhub\components\View;
use humhub\modules\mail\helpers\Url;
use humhub\modules\mail\models\Message;
use humhub\modules\mail\models\MessageEntry;
use humhub\helpers\MailStyleHelper;
use humhub\modules\user\models\User;
use humhub\widgets\mails\MailButton;
use humhub\widgets\mails\MailButtonList;

/* @var $this View */
/* @var $user User */
/* @var $sender User */
/* @var $message Message */
/* @var $entry MessageEntry */
/* @var $headline string */
/* @var $senderUrl string */
/* @var $content string */
/* @var $subHeadline string */
?>
<!-- START LAYOUT-1/1 -->
<tr>
    <td align="center" valign="top"   class="fix-box">

        <!-- start  container width 600px -->
        <table width="600"  align="center" border="0" cellspacing="0" cellpadding="0" class="container" style="background-color: <?= MailStyleHelper::getBackgroundColorMain() ?>">


            <tr>
                <td valign="top">

                    <!-- start container width 560px -->
                    <table width="540"  align="center" border="0" cellspacing="0" cellpadding="0" class="full-width" style="background-color: <?= MailStyleHelper::getBackgroundColorMain() ?>">


                        <!-- start text content -->
                        <tr>
                            <td valign="top">
                                <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center" >
                                    <tr>
                                        <td valign="top" width="auto" align="center">
                                            <!-- start button -->
                                            <table border="0" align="center" cellpadding="0" cellspacing="0">
                                                <tr>
                                                    <td width="auto"  align="center" valign="middle" height="28" style=" background-color: <?= MailStyleHelper::getBackgroundColorMain() ?>; background-clip: padding-box; font-size:26px; font-family: <?= MailStyleHelper::getFontFamily() ?>; text-align:center; color:<?= MailStyleHelper::getTextColorSoft2() ?>; font-weight: 300; padding:0 18px">

                                                        <span style="color: <?= MailStyleHelper::getTextColorMain() ?>; font-weight: 300;">
                                                            <?= $headline ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                            </table>
                                            <!-- end button -->
                                        </td>
                                    </tr>



                                </table>
                            </td>
                        </tr>
                        <!-- end text content -->


                    </table>
                    <!-- end  container width 560px -->
                </td>
            </tr>
        </table>
        <!-- end  container width 600px -->
    </td>
</tr>

<!-- END LAYOUT-1/1 -->


<!-- START LAYOUT-1/2 -->
<tr>
    <td align="center" valign="top" class="fix-box">

        <!-- start  container width 600px -->
        <table width="600" align="center" border="0" cellspacing="0" cellpadding="0" class="container"
               style="background-color: <?= MailStyleHelper::getBackgroundColorMain() ?>; border-radius: 0 0 4px 4px">
            <tr>
                <td valign="top">

                    <!-- start container width 560px -->
                    <table width="560" align="center" border="0" cellspacing="0" cellpadding="0" class="full-width"
                           style="background-color: <?= MailStyleHelper::getBackgroundColorMain() ?>">

                        <!-- start image and content -->
                        <tr>
                            <td valign="top" width="100%">

                                <!-- start content left -->
                                <table width="100%" border="0" cellspacing="0" cellpadding="0" align="left">

                                    <!--start space height -->
                                    <tr>
                                        <td height="20"></td>
                                    </tr>
                                    <!--end space height -->


                                    <!-- start content top-->
                                    <tr>
                                        <td valign="top" align="left">

                                            <table border="0" cellspacing="0" cellpadding="0" align="left">
                                                <tr>

                                                    <td valign="top" align="left" style="padding-right:20px;">
                                                        <!-- START: USER IMAGE -->
                                                        <a href="<?= $senderUrl ?>">
                                                            <img
                                                                src="<?= $sender->getProfileImage()->getUrl("", true); ?>"
                                                                width="50"
                                                                alt=""
                                                                style="max-width:50px; display:block !important; border-radius: 4px;"
                                                                border="0" hspace="0" vspace="0"/>
                                                        </a>
                                                        <!-- END: USER IMAGE -->
                                                    </td>


                                                    <td valign="top">

                                                        <table width="100%" border="0" cellspacing="0" cellpadding="0"
                                                               align="left">

                                                            <tr>
                                                                <td style="font-size: 13px; line-height: 22px; font-family: <?= MailStyleHelper::getFontFamily() ?>; color:<?= MailStyleHelper::getTextColorMain() ?>; font-weight:300; text-align:left; ">

                                                                    <strong><?= $subHeadline ?></strong>
                                                                    <br><br>
                                                                    <div style="display:inline-block;background-color:<?= MailStyleHelper::getBackgroundColorSecondary() ?>;border-radius:4px;padding:15px;">
                                                                        <?= $content ?>
                                                                    </div>

                                                                </td>
                                                            </tr>

                                                        </table>

                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <!-- end  content top-->

                                    <!--start space height -->
                                    <tr>
                                        <td height="15" class="col-underline"></td>
                                    </tr>
                                    <!--end space height -->
                                    <tr>
                                        <td valign="top" width="auto" align="center">
                                            <?= MailButtonList::widget(['buttons' => [
                                                MailButton::widget([
                                                    'url' => Url::toMessenger($message, true),
                                                    'text' => Yii::t('MailModule.base', 'Reply now'),
                                                ]),
                                            ]]) ?>
                                        </td>

                                    </tr>


                                    <!--start space height -->
                                    <tr>
                                        <td height="15" class="col-underline"></td>
                                    </tr>
                                    <!--end space height -->


                                </table>
                                <!-- end content left -->


                            </td>
                        </tr>
                        <!-- end image and content -->

                    </table>
                    <!-- end  container width 560px -->
                </td>
            </tr>
        </table>
        <!-- end  container width 600px -->
    </td>
</tr>
<!-- END LAYOUT-1/2 -->
