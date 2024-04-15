<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\mail\search;

use humhub\modules\mail\models\MessageEntry;
use yii\data\Pagination;

/**
 * SearchResultSet
 *
 * @author luke
 */
class SearchResultSet
{
    /**
     * @var MessageEntry[]
     */
    public array $results = [];

    /**
     * @var Pagination
     */
    public Pagination $pagination;
}
