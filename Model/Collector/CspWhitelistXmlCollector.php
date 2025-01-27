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

namespace MageSteady\CspBackoffice\Model\Collector;

use Magento\Csp\Api\PolicyCollectorInterface;
use Magento\Csp\Model\Collector\CspWhitelistXmlCollector as MagentoCspWhitelistXmlCollector;
use Magento\Csp\Model\Policy\FetchPolicy;
use Magento\Framework\App\Cache\Frontend\Pool;
use Magento\Framework\App\Cache\StateInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\State;
use Magento\Framework\Cache\FrontendInterface;
use Magento\Store\Model\StoreManagerInterface;
use MageSteady\CspBackoffice\Model\Config\Source\ValueType;
use MageSteady\CspBackoffice\Model\ResourceModel\Rule as RuleResourceModel;
use MageSteady\CspBackoffice\Model\ResourceModel\Rule\CollectionFactory;
use MageSteady\CspBackoffice\Model\Rule;

class CspWhitelistXmlCollector implements PolicyCollectorInterface
{
    public const CONFIG_IS_ENABLED = 'magesteady_csp_backoffice/general/enable_rules_management';
    protected FrontendInterface $cache;
    protected array $rules = [];

    public function __construct(
        protected ScopeConfigInterface $scopeConfig,
        protected MagentoCspWhitelistXmlCollector $magentoCollector,
        protected CollectionFactory $ruleCollectionFactory,
        Pool $cacheFrontendPool,
        protected StoreManagerInterface $storeManager,
        protected StateInterface $cacheState,
        protected State $appState
    )
    {
        $this->cache = $cacheFrontendPool->get('config');
    }

    /**
     * @inheritDoc
     */
    public function collect(array $defaultPolicies = []): array
    {
        // if module is disabled, use the default behavior
        if (!$this->scopeConfig->getValue(self::CONFIG_IS_ENABLED, 'store')) {
            return $this->magentoCollector->collect($defaultPolicies);
        }

        // append policies
        $rules = $this->getCachedRules();
        foreach ($rules as $directive => $valuesByType) {
            $defaultPolicies[] = new FetchPolicy(
                $directive,
                false,
                $valuesByType[ValueType::HOST] ?? [],
                [],
                false,
                false,
                false,
                [],
                $valuesByType[ValueType::HASH] ?? [],
                false,
                false
            );
        }

        return $defaultPolicies;
    }

    /**
     * Get rules by using cache if available in priority, and build the rules map if not yet generated
     *
     * @return array
     */
    public function getCachedRules(): array
    {
        // get from memory
        if (!empty($this->rules)) {
            return $this->rules;
        }

        /** @noinspection PhpUnhandledExceptionInspection */
        $storeId = $this->appState->getAreaCode() === 'adminhtml' ? 0 : (int)$this->storeManager->getStore()->getId();
        $cacheKey = RuleResourceModel::CACHE_IDENTIFIER . '_' . $storeId;

        // try to load cache if available
        if ($this->cacheState->isEnabled('config')
            && ($cache = $this->cache->load($cacheKey))
            && $decodedCache = json_decode($cache, true)) {

            return $this->rules = $decodedCache;
        }

        // build the rules map
        $policyMap = $this->buildPolicyMap($storeId);

        // store cache
        if ($this->cacheState->isEnabled('config')) {
            $this->cache->save(json_encode($policyMap), $cacheKey);
        }

        return $this->rules = $policyMap;
    }

    /**
     * Build policy map from rules stores in database
     *
     * @param int $storeId
     * @return array
     */
    public function buildPolicyMap(int $storeId): array
    {
        // find rules by current store
        $rules = $this->ruleCollectionFactory
            ->create()
            ->addStoreFilter($storeId, false);

        // group rules by directive and value type
        $policyMap = [];
        /** @var Rule $rule */
        foreach ($rules as $rule) {
            $valueType = $rule->getValueType();
            $key = $valueType === ValueType::HOST ? $rule->getIdentifier() : $rule->getValue();
            $value = $valueType === ValueType::HOST ? $rule->getValue() : $rule->getAlgorithm();

            $policyMap[$rule->getDirective()][$valueType][$key] = $value;
        }

        return $policyMap;
    }
}
