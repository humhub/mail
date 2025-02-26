<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\mail\search;

use humhub\interfaces\MetaSearchProviderInterface;
use humhub\modules\mail\models\forms\InboxFilterForm;
use humhub\services\MetaSearchService;
use Yii;

/**
 * Mail Meta Search Provider
 */
class SearchProvider implements MetaSearchProviderInterface
{
    private ?MetaSearchService $service = null;
    public ?string $keyword = null;
    public string|array|null $route = '/mail/mail/index';

    /**
     * @inheritdoc
     */
    public function getName(): string
    {
        return Yii::t('MailModule.base', 'Messages');
    }

    /**
     * @inheritdoc
     */
    public function getSortOrder(): int
    {
        return 400;
    }

    /**
     * @inheritdoc
     */
    public function getRoute(): string|array
    {
        return $this->route;
    }

    /**
     * @inheritdoc
     */
    public function getAllResultsText(): string
    {
        return $this->getService()->hasResults()
            ? Yii::t('base', 'Show all results')
            : Yii::t('MailModule.base', 'Advanced Messages Search');
    }

    /**
     * @inheritdoc
     */
    public function getIsHiddenWhenEmpty(): bool
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function getResults(int $maxResults): array
    {
        $filter = new InboxFilterForm(['term' => $this->getKeyword()]);
        $filter->apply();
        $totalCount = $filter->query->count();
        $resultsQuery = $filter->query->limit($maxResults);

        $results = [];
        foreach ($resultsQuery->all() as $userMessage) {
            $results[] = new SearchRecord($userMessage);
        }

        return [
            'totalCount' => $totalCount,
            'results' => $results,
        ];
    }

    /**
     * @inheritdoc
     */
    public function getService(): MetaSearchService
    {
        if ($this->service === null) {
            $this->service = new MetaSearchService($this);
        }

        return $this->service;
    }

    /**
     * @inheritdoc
     */
    public function getKeyword(): ?string
    {
        return $this->keyword;
    }
}
