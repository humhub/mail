<?php


namespace humhub\modules\mail\widgets;


use Yii;

class TimeAgo extends \humhub\widgets\TimeAgo
{
    public function renderDateTime($elapsed)
    {
        // Assure time is always set
        if(isset(Yii::$app->params['formatter']['timeAgoHideTimeAfter'])) {
            $timeAgoHideTimeAfter = Yii::$app->params['formatter']['timeAgoHideTimeAfter'];
            Yii::$app->params['formatter']['timeAgoHideTimeAfter'] = false;

            $result = parent::renderDateTime($elapsed);

            Yii::$app->params['formatter']['timeAgoHideTimeAfter'] = $timeAgoHideTimeAfter;
            return $result;
        }

        return parent::renderDateTime($elapsed);
    }
}