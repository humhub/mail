<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\mail\search;

use humhub\modules\mail\models\MessageEntry;
use Yii;
use yii\data\Pagination;
use yii\db\ActiveQuery;

class SearchDriver
{
    public function search(SearchRequest $request): SearchResultSet
    {
        $query = MessageEntry::find();

        $query->andWhere(['LIKE', 'message_entry.content', $request->keyword]);

        $this->addQueryFilterVisibility($query);

        $query->orderBy(['message_entry.created_at' => SORT_DESC]);

        $resultSet = new SearchResultSet();
        $resultSet->pagination = new Pagination();
        $resultSet->pagination->totalCount = $query->count();
        $resultSet->pagination->pageSize = $request->pageSize;
        $resultSet->pagination->setPage($request->page - 1, true);

        $query->offset($resultSet->pagination->offset)->limit($resultSet->pagination->limit);

        foreach ($query->all() as $entry) {
            $resultSet->results[] = $entry;
        }

        return $resultSet;
    }

    protected function addQueryFilterVisibility(ActiveQuery $query): ActiveQuery
    {
        return $query->leftJoin('user_message', 'user_message.message_id = message_entry.message_id')
            ->andWhere(['user_message.user_id' => Yii::$app->user->id]);
    }
}
