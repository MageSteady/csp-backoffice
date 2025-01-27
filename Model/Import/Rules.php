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

namespace MageSteady\CspBackoffice\Model\Import;

use Exception;
use Magento\Eav\Model\Config;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Framework\Stdlib\StringUtils;
use Magento\Framework\Validation\ValidationException;
use Magento\ImportExport\Helper\Data;
use Magento\ImportExport\Model\Import\Entity\AbstractEntity;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;
use Magento\ImportExport\Model\ResourceModel\Helper;
use Magento\ImportExport\Model\ResourceModel\Import\Data as ImportData;
use MageSteady\CspBackoffice\Model\ResourceModel\Rule as RuleResourceModel;
use MageSteady\CspBackoffice\Model\Rule;
use MageSteady\CspBackoffice\Model\RuleFactory;

class Rules extends AbstractEntity
{
    public const ENTITY_CODE = 'csp_rules';
    public const TABLE = 'magesteady_cspbackoffice_rule';

    /**
     * If we should check column names
     */
    protected $needColumnCheck = true;

    /**
     * Need to log in import history
     */
    protected $logInHistory = true;

    /**
     * Valid column names
     */
    protected $validColumnNames = [
        'rule_id',
        'store_id',
        'identifier',
        'directive',
        'value_type',
        'algorithm',
        'value'
    ];

    /**
     * Permanent entity columns.
     *
     * @var string[]
     */
    protected $_permanentAttributes = [
        'store_id',
        'identifier',
        'directive',
        'value_type',
        'algorithm',
        'value'
    ];

    /** @noinspection PhpMissingParentConstructorInspection */
    public function __construct(
        JsonHelper $jsonHelper,
        Data $importExportData,
        ImportData $importData,
        Config $config,
        ResourceConnection $resource,
        Helper $resourceHelper,
        StringUtils $string,
        ProcessingErrorAggregatorInterface $errorAggregator,
        protected RuleFactory $ruleFactory,
        protected RuleResourceModel $ruleResourceModel
    )
    {
        $this->jsonHelper = $jsonHelper;
        $this->_importExportData = $importExportData;
        $this->_resourceHelper = $resourceHelper;
        $this->string = $string;
        $this->errorAggregator = $errorAggregator;
        $this->_dataSourceModel = $importData;
        $this->_connection = $resource->getConnection();
    }

    public function getEntityTypeCode(): string
    {
        return self::ENTITY_CODE;
    }

    /**
     * @return bool
     * @throws LocalizedException
     * @throws Exception
     */
    protected function _importData(): bool
    {
        $this->ruleResourceModel->deleteAll();

        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            foreach ($bunch as $rowNum => $row) {
                if (!$this->validateRow($row, $rowNum)) {
                    continue;
                }

                if ($this->getErrorAggregator()->hasToBeTerminated()) {
                    $this->getErrorAggregator()->addRowToSkip($rowNum);

                    continue;
                }

                $rule = $this->createRuleFromRow($row);
                $this->ruleResourceModel->save($rule);

                $this->countItemsCreated++;
            }
        }

        return true;
    }

    public function validateRow(array $rowData, $rowNum): bool
    {
        if (isset($this->_validatedRows[$rowNum])) {
            return !isset($this->_invalidRows[$rowNum]);
        }
        $this->_validatedRows[$rowNum] = true;

        $rule = $this->createRuleFromRow($rowData);

        try {
            $this->ruleResourceModel->validate($rule);
        } catch (ValidationException $e) {
            $this->addRowError($e->getMessage(), $rowNum);
            return false;
        }

        return true;
    }

    protected function createRuleFromRow(array $rowData): Rule
    {
        $rule = $this->ruleFactory->create();

        $rule
            ->setDirective($rowData['directive'])
            ->setIdentifier($rowData['identifier'])
            ->setValueType($rowData['value_type'])
            ->setAlgorithm($rowData['algorithm'] ?: null)
            ->setValue($rowData['value'])
            ->setStoreId(explode(',', $rowData['store_id']));

        return $rule;
    }
}
