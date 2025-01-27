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

namespace MageSteady\CspBackoffice\Model\Rule;

use Magento\Csp\Model\Collector\CspWhitelistXml\Reader;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\StoreManagerInterface;
use MageSteady\CspBackoffice\Model\ResourceModel\Rule as RuleResourceModel;
use MageSteady\CspBackoffice\Model\Rule;
use MageSteady\CspBackoffice\Model\RuleFactory;
use Throwable;

class XmlImporter
{
    public function __construct(
        protected RuleResourceModel $ruleResourceModel,
        protected RuleFactory $ruleFactory,
        protected StoreManagerInterface $storeManager,
        protected Reader $xmlReader
    )
    {
    }

    /**
     * @throws LocalizedException
     */
    public function import(): void
    {
        $connection = $this->ruleResourceModel->getConnection();

        $connection->beginTransaction();

        try {
            $this->ruleResourceModel->deleteAll();

            $xmlPolicy = $this->xmlReader->read('global');
            foreach ($xmlPolicy as $directive => $xmlRules) {
                foreach ($xmlRules as $identifier => $xmlRule) {
                    $rule = $this->createRuleFromXml($directive, $identifier, $xmlRule);
                    $this->ruleResourceModel->save($rule);
                }
            }

            $connection->commit();
        } catch (Throwable $e) {
            $connection->rollBack();

            throw $e;
        }
    }

    protected function createRuleFromXml(string $directive, string $identifier, array $xmlRule): Rule
    {
        $rule = $this->ruleFactory->create();

        $rule
            ->setDirective($directive)
            ->setIdentifier($identifier)
            ->setValueType($xmlRule['type'])
            ->setAlgorithm($xmlRule['algorithm'])
            ->setValue($xmlRule['value'])
            ->setStoreId(array_keys($this->storeManager->getStores(true)));

        return $rule;
    }
}
