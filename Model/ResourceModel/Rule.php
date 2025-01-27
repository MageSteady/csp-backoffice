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

namespace MageSteady\CspBackoffice\Model\ResourceModel;

use Exception;
use Magento\Framework\App\Cache\Frontend\Pool;
use Magento\Framework\Cache\FrontendInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Validation\ValidationException;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use MageSteady\CspBackoffice\Api\Data\RuleInterface;
use MageSteady\CspBackoffice\Model\Config\Source\Algorithm;
use MageSteady\CspBackoffice\Model\Config\Source\Directive;
use MageSteady\CspBackoffice\Model\Config\Source\ValueType;
use MageSteady\CspBackoffice\Model\Rule as RuleModel;

class Rule extends AbstractDb
{
    public const CACHE_IDENTIFIER = 'csp_rules';
    protected FrontendInterface $cache;

    public function __construct(
        Context $context,
        protected Algorithm $algorithm,
        protected Directive $directive,
        protected ValueType $valueType,
        protected MetadataPool $metadataPool,
        protected StoreManagerInterface $storeManager,
        protected EntityManager $entityManager,
        Pool $cacheFrontendPool,
        $connectionName = null
    )
    {
        parent::__construct($context, $connectionName);
        $this->cache = $cacheFrontendPool->get('config');
    }

    /**
     * @inheritDoc
     */
    protected function _construct(): void
    {
        $this->_init('magesteady_cspbackoffice_rule', 'rule_id');
    }

    /**
     * Delete all rules
     *
     * @return int
     * @throws LocalizedException
     */
    public function deleteAll(): int
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return $this->getConnection()->delete($this->getMainTable());
    }

    /**
     * @inheritDoc
     */
    public function delete(AbstractModel $object)
    {
        $this->_beforeDelete($object);
        $this->entityManager->delete($object);
        $this->_afterDelete($object);

        return $this;
    }

    /**
     * Clear cache after delete
     *
     * @param AbstractModel $object
     * @return $this
     */
    protected function _afterDelete(AbstractModel $object)
    {
        $this->clearCache();

        return $this;
    }

    protected function clearCache(): void
    {
        foreach ($this->storeManager->getStores(true) as $store) {
            $this->cache->remove(self::CACHE_IDENTIFIER . '_' . $store->getId());
        }
    }

    /**
     * @inheritDoc
     */
    public function getConnection()
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return $this->metadataPool->getMetadata(RuleInterface::class)->getEntityConnection();
    }

    /**
     * Load an object
     *
     * @param RuleModel|AbstractModel $object
     * @param mixed $value
     * @param string $field field to load by (defaults to model id)
     * @return $this
     * @throws LocalizedException
     */
    public function load(AbstractModel $object, $value, $field = null)
    {
        $ruleId = $this->getRuleId($object, $value, $field);
        if ($ruleId) {
            $this->entityManager->load($object, $ruleId);
        }
        return $this;
    }

    /**
     * Get rule id.
     *
     * @param AbstractModel $object
     * @param mixed $value
     * @param string $field
     * @return bool|int|string
     * @throws LocalizedException
     * @throws Exception
     */
    protected function getRuleId(AbstractModel $object, $value, $field = null)
    {
        $entityMetadata = $this->metadataPool->getMetadata(RuleInterface::class);
        if (!is_numeric($value) && $field === null) {
            $field = 'identifier';
        } elseif (!$field) {
            $field = $entityMetadata->getIdentifierField();
        }
        $entityId = $value;
        if ($field !== $entityMetadata->getIdentifierField() || $object->getStoreId()) {
            $select = $this->_getLoadSelect($field, $value, $object);
            $select->reset(Select::COLUMNS)
                ->columns($this->getMainTable() . '.' . $entityMetadata->getIdentifierField())
                ->limit(1);
            $result = $this->getConnection()->fetchCol($select);
            $entityId = count($result) ? $result[0] : false;
        }
        return $entityId;
    }

    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @param RuleModel|AbstractModel $object
     * @return Select
     * @throws LocalizedException
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $entityMetadata = $this->metadataPool->getMetadata(RuleInterface::class);
        $linkField = $entityMetadata->getLinkField();

        $select = parent::_getLoadSelect($field, $value, $object);

        if ($object->getStoreId()) {
            $stores = [(int)$object->getStoreId(), Store::DEFAULT_STORE_ID];

            $select->join(
                ['mcrs' => $this->getTable('magesteady_cspbackoffice_rule_store')],
                $this->getMainTable() . '.' . $linkField . ' = mcrs.' . $linkField,
                ['store_id']
            )
                ->where('mcrs.store_id in (?)', $stores)
                ->order('store_id DESC')
                ->limit(1);
        }

        return $select;
    }

    /**
     * Get store ids to which specified item is assigned
     *
     * @param int $id
     * @return array
     * @noinspection PhpUnhandledExceptionInspection
     */
    public function lookupStoreIds(int $id): array
    {
        $connection = $this->getConnection();

        $entityMetadata = $this->metadataPool->getMetadata(RuleInterface::class);
        $linkField = $entityMetadata->getLinkField();

        $select = $connection->select()
            ->from(['mcrs' => $this->getTable('magesteady_cspbackoffice_rule_store')], 'store_id')
            ->join(
                ['mcr' => $this->getMainTable()],
                'mcrs.' . $linkField . ' = mcr.' . $linkField,
                []
            )
            ->where('mcr.' . $entityMetadata->getIdentifierField() . ' = :rule_id');

        return $connection->fetchCol($select, ['rule_id' => $id]);
    }

    /**
     * Save an object.
     *
     * @param AbstractModel $object
     * @return $this
     * @throws Exception
     */
    public function save(AbstractModel $object)
    {
        $this->_beforeSave($object);
        $this->entityManager->save($object);
        $this->_afterSave($object);

        return $this;
    }

    /**
     * Validate rule before save
     *
     * @param RuleModel|AbstractModel $object
     * @return $this
     * @throws ValidationException
     */
    protected function _beforeSave(AbstractModel $object): self
    {
        $this->validate($object);

        return $this;
    }

    /**
     * @throws ValidationException
     */
    public function validate(RuleModel $rule): void
    {
        if (empty($rule->getIdentifier())) {
            throw new ValidationException(__('Identifier cannot be empty'));
        }

        if (empty($rule->getDirective())) {
            throw new ValidationException(__('Directive cannot be empty'));
        }

        if (empty($rule->getValueType())) {
            throw new ValidationException(__('Value type cannot be empty'));
        }

        if (empty($rule->getValue())) {
            throw new ValidationException(__('Value cannot be empty'));
        }

        if (empty($rule->getStoreId())) {
            throw new ValidationException(__('Store ID cannot be empty'));
        }

        if ($rule->getValueType() === ValueType::HASH && empty($rule->getAlgorithm())) {
            throw new ValidationException(__('Algorithm cannot be empty if value type is "hash"'));
        }

        if (!empty($rule->getAlgorithm()) && !array_key_exists($rule->getAlgorithm(), array_merge(...$this->algorithm->toArray()))) {
            throw new ValidationException(__('Invalid algorithm'));
        }

        if (!array_key_exists($rule->getDirective(), array_merge(...$this->directive->toArray()))) {
            throw new ValidationException(__('Invalid directive'));
        }

        if (!array_key_exists($rule->getValueType(), array_merge(...$this->valueType->toArray()))) {
            throw new ValidationException(__('Invalid value type'));
        }

        if (!empty(array_diff($rule->getStoreId(), array_keys($this->storeManager->getStores(true))))) {
            throw new ValidationException(__('Invalid store ID'));
        }

        if (!$this->getIsUniqueRuleToStores($rule)) {
            throw new ValidationException(
                __('A rule with the same identifier and same directive already exists in the selected store.')
            );
        }
    }

    /**
     * Check for unique couple <identifier,directive> of rule to selected store(s).
     *
     * @param AbstractModel $object
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     * @noinspection PhpUnhandledExceptionInspection
     */
    public function getIsUniqueRuleToStores(AbstractModel $object): bool
    {
        $entityMetadata = $this->metadataPool->getMetadata(RuleInterface::class);
        $linkField = $entityMetadata->getLinkField();

        $stores = (array)$object->getData('store_id');

        $select = $this->getConnection()->select()
            ->from(['mcr' => $this->getMainTable()])
            ->join(
                ['mcrs' => $this->getTable('magesteady_cspbackoffice_rule_store')],
                'mcr.' . $linkField . ' = mcrs.' . $linkField,
                []
            )
            ->where('mcr.identifier = ?  ', $object->getData('identifier'))
            ->where('mcr.directive = ?  ', $object->getData('directive'))
            ->where('mcrs.store_id IN (?)', $stores);

        if ($object->getId()) {
            $select->where('mcr.' . $entityMetadata->getIdentifierField() . ' <> ?', $object->getId());
        }

        if ($this->getConnection()->fetchRow($select)) {
            return false;
        }

        return true;
    }

    /**
     * Clear cache after save
     *
     * @param AbstractModel $object
     * @return $this
     */
    protected function _afterSave(AbstractModel $object)
    {
        $this->clearCache();

        return $this;
    }
}

