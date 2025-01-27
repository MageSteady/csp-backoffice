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

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use MageSteady\CspBackoffice\Model\ResourceModel\ViolationReport\CollectionFactory;

class ViolationReport extends AbstractDb
{
    public function __construct(
        Context $context,
        protected CollectionFactory $reportCollectionFactory,
        $connectionName = null
    )
    {
        parent::__construct($context, $connectionName);
    }

    /**
     * @inheritDoc
     */
    protected function _construct(): void
    {
        $this->_init('magesteady_cspbackoffice_violationreport', 'violationreport_id');
    }

    /**
     * Delete all reports
     *
     * @return int
     */
    public function deleteAll(): int
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return $this->getConnection()->delete($this->getMainTable());
    }

    /**
     * Clean old reports and keep the configured history size
     *
     * @param int $historySize
     * @return void
     */
    public function cleanOldReports(int $historySize): void
    {
        $connection = $this->getConnection();

        $lastIds = $this->reportCollectionFactory
            ->create()
            ->addFieldToSelect(['violationreport_id'])
            ->setOrder('violationreport_id')
            ->setPageSize($historySize)
            ->getColumnValues('violationreport_id');

        /** @noinspection PhpUnhandledExceptionInspection */
        $connection->delete($this->getMainTable(), ['violationreport_id NOT IN (?)' => $lastIds]);
    }
}

