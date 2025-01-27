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

namespace MageSteady\CspBackoffice\Plugin\Magento\Ui;

use Exception;
use Magento\Framework\Api\Search\DocumentInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Ui\Component\Listing\Columns;
use Magento\Ui\Model\Export\MetadataProvider;

class AdaptCspRulesExport
{
    protected bool $isCspExport = false;

    public function __construct(
        protected RequestInterface $request
    )
    {
    }

    /**
     * Export with column ids instead of labels as headers (and exclude necessary columns)
     *
     * @param MetadataProvider $subject
     * @param array $headers
     * @param UiComponentInterface $component
     * @return array
     * @throws Exception
     */
    public function afterGetHeaders(MetadataProvider $subject, array $headers, UiComponentInterface $component): array
    {
        if ($this->request->getControllerName() === 'export' && $component->getName() === 'magesteady_cspbackoffice_rule_listing') {
            $this->isCspExport = true;

            $headers = array_keys($this->getColumnsComponent($component)->getChildComponents());
            return array_diff($headers, ['ids', 'actions']);
        }

        return $headers;
    }

    /**
     * @param UiComponentInterface $component
     * @return UiComponentInterface
     * @throws Exception
     * @see MetadataProvider::getColumnsComponent
     */
    protected function getColumnsComponent(UiComponentInterface $component): UiComponentInterface
    {
        foreach ($component->getChildComponents() as $childComponent) {
            if ($childComponent instanceof Columns) {
                return $childComponent;
            }
        }
        throw new Exception('No columns found'); // @codingStandardsIgnoreLine
    }

    /**
     * Do not replace export values with option labels
     *
     * @param MetadataProvider $subject
     * @param DocumentInterface $document
     * @param array $fields
     * @param array $options
     * @return array
     */
    public function beforeGetRowData(MetadataProvider $subject, DocumentInterface $document, $fields, $options): array
    {
        if ($this->isCspExport) {
            if (is_array($document->getData('store_id'))) {
                $document->setData('store_id', implode(',', $document->getData('store_id')));
            }

            return [$document, $fields, []];
        }

        return [$document, $fields, $options];
    }
}
