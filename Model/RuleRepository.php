<?php
/**
 * @copyright   MageSteady (https://www.magesteady.com)
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0 or later
 *
 * This file was written by the MageSteady team.
 *
 * This program is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 */
declare(strict_types=1);

namespace MageSteady\CspBackoffice\Model;

use Exception;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\EntityManager\HydratorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use MageSteady\CspBackoffice\Api\Data\RuleInterface;
use MageSteady\CspBackoffice\Api\Data\RuleInterfaceFactory;
use MageSteady\CspBackoffice\Api\Data\RuleSearchResultsInterfaceFactory;
use MageSteady\CspBackoffice\Api\RuleRepositoryInterface;
use MageSteady\CspBackoffice\Model\ResourceModel\Rule as RuleResourceModel;
use MageSteady\CspBackoffice\Model\ResourceModel\Rule\CollectionFactory as RuleCollectionFactory;

class RuleRepository implements RuleRepositoryInterface
{
    public function __construct(
        protected RuleResourceModel $resourceModel,
        protected RuleInterfaceFactory $ruleFactory,
        protected RuleCollectionFactory $ruleCollectionFactory,
        protected RuleSearchResultsInterfaceFactory $searchResultsFactory,
        protected CollectionProcessorInterface $collectionProcessor,
        protected HydratorInterface $hydrator,
        protected StoreManagerInterface $storeManager
    )
    {
    }

    /**
     * @inheritDoc
     */
    public function save(RuleInterface $rule): RuleInterface
    {
        if (empty($rule->getStoreId())) {
            $rule->setStoreId([$this->storeManager->getStore()->getId()]);
        }

        if ($rule->getId() && !$rule->getOrigData()) {
            /** @noinspection CallableParameterUseCaseInTypeContextInspection */
            $rule = $this->hydrator->hydrate($this->get($rule->getId()), $this->hydrator->extract($rule));
        }

        try {
            $this->resourceModel->save($rule);
        } catch (Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the rule: %1',
                $exception->getMessage()
            ));
        }

        return $rule;
    }

    /**
     * @inheritDoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->ruleCollectionFactory->create();

        $this->collectionProcessor->process($searchCriteria, $collection);

        $searchResults = $this->searchResultsFactory
            ->create()
            ->setSearchCriteria($searchCriteria);

        $items = [];
        foreach ($collection as $rule) {
            $items[] = $rule;
        }

        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($ruleId): bool
    {
        return $this->delete($this->get($ruleId));
    }

    /**
     * @inheritDoc
     */
    public function delete(RuleInterface $rule): bool
    {
        try {
            $ruleModel = $this->ruleFactory->create();
            $this->resourceModel->load($ruleModel, $rule->getRuleId());
            $this->resourceModel->delete($ruleModel);
        } catch (Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Rule: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function get($ruleId): RuleInterface
    {
        $rule = $this->ruleFactory->create();
        $this->resourceModel->load($rule, $ruleId);

        if (!$rule->getId()) {
            throw new NoSuchEntityException(__('Rule with id "%1" does not exist.', $ruleId));
        }

        return $rule;
    }
}

