<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\mail\search;

use humhub\libs\SearchQuery;
use yii\base\Model;

class SearchRequest extends Model
{
    public string $keyword = '';

    public int $page = 1;

    public int $pageSize = 25;

    public ?SearchQuery $searchQuery = null;

    public function rules()
    {
        return [
            [['keyword'], 'string'],
            [['keyword'], 'required'],
            [['page'], 'integer'],
        ];
    }

    public function getKeywords(): array
    {
        return explode(' ', $this->keyword);
    }

    public function getSearchQuery(): SearchQuery
    {
        if ($this->searchQuery === null) {
            $this->searchQuery = new SearchQuery($this->keyword);
        }

        return $this->searchQuery;
    }
}
