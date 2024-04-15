<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\mail\widgets;

use humhub\libs\Html;
use humhub\modules\ui\widgets\DirectoryFilters;
use Yii;

/**
 * SearchFilters displays the filters on the mail messaging searching page
 * @author Luke
 */
class SearchFilters extends DirectoryFilters
{
    /**
     * @inheritdoc
     */
    public $pageUrl = '/mail/search';

    protected function initDefaultFilters()
    {
        $this->addFilter('keyword', [
            'title' => Yii::t('MailModule.search', 'Find messages based on keywords'),
            'placeholder' => Yii::t('MailModule.search', 'Search...'),
            'type' => 'input',
            'inputOptions' => ['autocomplete' => 'off', 'data-highlight' => '.search-results'],
            'wrapperClass' => 'col-md-6 form-search-filter-keyword',
            'afterInput' => Html::submitButton('<span class="fa fa-search"></span>', ['class' => 'form-button-search']),
            'sortOrder' => 100,
        ]);
    }
}
