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

namespace MageSteady\CspBackoffice\Plugin\Magento\Framework;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;

class ForceCspConfigValues
{
    public const CONFIG_IS_ENABLED_REPORTS = 'magesteady_csp_backoffice/general/enable_violation_reports';
    public const CONFIG_IS_ENABLED_RESTRICT_ADMINHTML = 'magesteady_csp_backoffice/general/enable_restrict_mode_adminhtml';
    public const CONFIG_IS_ENABLED_RESTRICT_FRONTEND = 'magesteady_csp_backoffice/general/enable_restrict_mode_frontend';

    public function __construct(
        protected StoreManagerInterface $storeManager,
        protected ScopeConfigInterface $scopeConfig
    )
    {
    }

    /**
     * Force report_uri configuration to point to CSP Backoffice controller
     *
     * @param ScopeConfigInterface $subject
     * @param mixed $value
     * @param string $path
     * @return mixed
     */
    public function afterGetValue(ScopeConfigInterface $subject, mixed $value, mixed $path): mixed
    {
        $value = $this->handleReportUriConfig($value, $path);
        $value = $this->handleReportOnlyConfig($value, $path);

        return $value;
    }

    protected function handleReportUriConfig(mixed $value, mixed $path): mixed
    {
        if (!str_starts_with($path, 'csp/') || !str_ends_with($path, '/report_uri')) {
            return $value;
        }

        if (!$this->scopeConfig->getValue(self::CONFIG_IS_ENABLED_REPORTS)) {
            return $value;
        }

        return $this->storeManager->getDefaultStoreView()?->getBaseUrl() . 'magesteady_cspbackoffice/report';
    }

    protected function handleReportOnlyConfig(mixed $value, mixed $path): mixed
    {
        if ($path === 'csp/mode/storefront/report_only'
            && $this->scopeConfig->getValue(self::CONFIG_IS_ENABLED_RESTRICT_FRONTEND, 'store')) {
            return 0;
        }

        if ($path === 'csp/mode/admin/report_only'
            && $this->scopeConfig->getValue(self::CONFIG_IS_ENABLED_RESTRICT_ADMINHTML)) {
            return 0;
        }

        return $value;
    }
}
