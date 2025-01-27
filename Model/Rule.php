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

use Magento\Framework\Model\AbstractModel;
use MageSteady\CspBackoffice\Api\Data\RuleInterface;

class Rule extends AbstractModel implements RuleInterface
{

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(ResourceModel\Rule::class);
    }

    /**
     * @inheritDoc
     */
    public function getRuleId()
    {
        return $this->getData(self::RULE_ID);
    }

    /**
     * @inheritDoc
     */
    public function setRuleId($ruleId)
    {
        return $this->setData(self::RULE_ID, $ruleId);
    }

    /**
     * @inheritDoc
     */
    public function getIdentifier()
    {
        return $this->getData(self::IDENTIFIER);
    }

    /**
     * @inheritDoc
     */
    public function setIdentifier($identifier)
    {
        return $this->setData(self::IDENTIFIER, $identifier);
    }

    /**
     * @inheritDoc
     */
    public function getDirective()
    {
        return $this->getData(self::DIRECTIVE);
    }

    /**
     * @inheritDoc
     */
    public function setDirective($directive)
    {
        return $this->setData(self::DIRECTIVE, $directive);
    }

    /**
     * @inheritDoc
     */
    public function getValueType()
    {
        return $this->getData(self::VALUE_TYPE);
    }

    /**
     * @inheritDoc
     */
    public function setValueType($valueType)
    {
        return $this->setData(self::VALUE_TYPE, $valueType);
    }

    /**
     * @inheritDoc
     */
    public function getAlgorithm()
    {
        return $this->getData(self::ALGORITHM);
    }

    /**
     * @inheritDoc
     */
    public function setAlgorithm($algorithm)
    {
        return $this->setData(self::ALGORITHM, $algorithm);
    }

    /**
     * @inheritDoc
     */
    public function getValue()
    {
        return $this->getData(self::VALUE);
    }

    /**
     * @inheritDoc
     */
    public function setValue($value)
    {
        return $this->setData(self::VALUE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getStoreId()
    {
        return $this->getData(self::STORE_ID);
    }

    /**
     * @inheritDoc
     */
    public function setStoreId($storeId)
    {
        return $this->setData(self::STORE_ID, $storeId);
    }

    /**
     * Receive page store ids
     *
     * @return int[]
     */
    public function getStores()
    {
        return $this->hasData('stores') ? $this->getData('stores') : $this->getData('store_id');
    }
}

