<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\mail\controllers;

use humhub\components\access\ControllerAccess;
use humhub\components\Controller;
use humhub\modules\content\widgets\stream\StreamEntryWidget;
use humhub\modules\content\widgets\stream\WallStreamEntryOptions;
use humhub\modules\mail\search\SearchDriver;
use humhub\modules\mail\search\SearchRequest;
use humhub\modules\mail\search\SearchResultSet;
use Yii;

/**
 * SearchController provides the message searching.
 *
 * @package humhub.modules.mail.controllers
 */
class SearchController extends Controller
{
    /**
     * @inheritdoc
     */
    public $subLayout = '@mail/views/search/_layout';

    /**
     * @note The current search request, required for File highlighting
     */
    public ?SearchRequest $searchRequest = null;

    /**
     * @inheritdoc
     */
    protected function getAccessRules()
    {
        return [[ControllerAccess::RULE_LOGGED_IN_ONLY]];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionResults()
    {
        $resultSet = null;

        $this->searchRequest = new SearchRequest(['pageSize' => 3]);
        if ($this->searchRequest->load(Yii::$app->request->get(), '') && $this->searchRequest->validate()) {
            $resultSet = (new SearchDriver())->search($this->searchRequest);
        }

        $page = $resultSet ? $resultSet->pagination->getPage() + 1 : 1;
        $totalCount = $resultSet ? $resultSet->pagination->totalCount : 0;
        $results = $this->renderResults($resultSet);

        return $this->asJson([
            'content' => $page > 1
                ? $results
                : $this->renderAjax('results', ['results' => $results, 'totalCount' => $totalCount]),
            'page' => $page,
            'isLast' => $results === '' || !$resultSet || $page === $resultSet->pagination->getPageCount(),
        ]);
    }

    private function renderResults($resultSet): ?string
    {
        if (!($resultSet instanceof SearchResultSet)) {
            return null;
        }

        $results = '';
        $options = (new WallStreamEntryOptions())->viewContext(WallStreamEntryOptions::VIEW_CONTEXT_SEARCH);
        foreach ($resultSet->results as $result) {
            $results .= StreamEntryWidget::renderStreamEntry($result->getModel(), $options);
        }

        return $results;
    }
}
