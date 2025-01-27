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

namespace MageSteady\CspBackoffice\Cron;

use Magento\Framework\App\Config\ScopeConfigInterface;
use MageSteady\CspBackoffice\Model\ResourceModel\ViolationReport;

class CleanViolationReports
{
    public function __construct(
        protected ViolationReport $reportResourceModel,
        protected ScopeConfigInterface $scopeConfig
    )
    {
    }

    /**
     * Clean violation reports every day at midnight
     *
     * @return void
     */
    public function execute(): void
    {
        $this->reportResourceModel->cleanOldReports((int)$this->scopeConfig->getValue('magesteady_csp_backoffice/general/history_size'));
    }
}
