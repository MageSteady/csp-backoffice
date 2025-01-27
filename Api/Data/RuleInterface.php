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

namespace MageSteady\CspBackoffice\Api\Data;

interface RuleInterface
{
    public const ALGORITHM = 'algorithm';
    public const VALUE_TYPE = 'value_type';
    public const IDENTIFIER = 'identifier';
    public const VALUE = 'value';
    public const RULE_ID = 'rule_id';
    public const DIRECTIVE = 'directive';
    public const STORE_ID = 'store_id';

    /**
     * Get rule_id
     * @return string|null
     */
    public function getRuleId();

    /**
     * Set rule_id
     * @param string $ruleId
     * @return \MageSteady\CspBackoffice\Api\Data\RuleInterface
     */
    public function setRuleId($ruleId);

    /**
     * Get identifier
     * @return string|null
     */
    public function getIdentifier();

    /**
     * Set identifier
     * @param string $identifier
     * @return \MageSteady\CspBackoffice\Api\Data\RuleInterface
     */
    public function setIdentifier($identifier);

    /**
     * Get directive
     * @return string|null
     */
    public function getDirective();

    /**
     * Set directive
     * @param string $directive
     * @return \MageSteady\CspBackoffice\Api\Data\RuleInterface
     */
    public function setDirective($directive);

    /**
     * Get value_type
     * @return string|null
     */
    public function getValueType();

    /**
     * Set value_type
     * @param string $valueType
     * @return \MageSteady\CspBackoffice\Api\Data\RuleInterface
     */
    public function setValueType($valueType);

    /**
     * Get algorithm
     * @return string|null
     */
    public function getAlgorithm();

    /**
     * Set algorithm
     * @param string $algorithm
     * @return \MageSteady\CspBackoffice\Api\Data\RuleInterface
     */
    public function setAlgorithm($algorithm);

    /**
     * Get value
     * @return string|null
     */
    public function getValue();

    /**
     * Set value
     * @param string $value
     * @return \MageSteady\CspBackoffice\Api\Data\RuleInterface
     */
    public function setValue($value);

    /**
     * Get store_id
     * @return int[]
     */
    public function getStoreId();

    /**
     * Set store_id
     * @param int[] $storeId
     * @return \MageSteady\CspBackoffice\Api\Data\RuleInterface
     */
    public function setStoreId($storeId);
}

