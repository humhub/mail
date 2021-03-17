<?php


namespace humhub\modules\mail\widgets;


use Yii;

class TimeAgo extends \humhub\widgets\TimeAgo
{
    public $badge = false;

    public function renderDateTime($elapsed)
    {
        // TODO: From HumHub 1.7 the timeAgoHideTimeAfter can be configured within the widget
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

    public function renderTimeAgo()
    {
        $result = parent::renderTimeAgo();

        if($this->badge) {
            $result = '<span class="badge">' . $result . '</span>';
        }

        return $result;
    }
}