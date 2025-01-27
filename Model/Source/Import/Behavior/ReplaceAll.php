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

namespace MageSteady\CspBackoffice\Model\Source\Import\Behavior;

use Magento\ImportExport\Model\Import;
use Magento\ImportExport\Model\Source\Import\AbstractBehavior;

class ReplaceAll extends AbstractBehavior
{
    /**
     * @inheritdoc
     */
    public function toArray(): array
    {
        return [
            Import::BEHAVIOR_REPLACE => __('Replace all')
        ];
    }

    /**
     * @inheritdoc
     */
    public function getCode(): string
    {
        return 'replace_all';
    }

    /**
     * @inheritdoc
     */
    public function getNotes($entityCode): array
    {
        $messages = ['csp_rules' => [
            Import::BEHAVIOR_REPLACE => __(
                "The existing rules data are replaced with new data.<br/><b>Caution: all existing rules will be removed before importing the file.</b><br/>You can export existing rules from the listing."
            )
        ]];
        return $messages[$entityCode] ?? [];
    }
}
