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

namespace MageSteady\CspBackoffice\Ui\Component\Listing\Column;

use Magento\Framework\Escaper;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\System\Store as SystemStore;
use Magento\Ui\Component\Listing\Columns\Column;

class Store extends Column
{
    protected string $storeKey;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        protected SystemStore $systemStore,
        protected Escaper $escaper,
        protected StoreManagerInterface $storeManager,
        array $components = [],
        array $data = [],
        $storeKey = 'store_id'
    )
    {
        $this->storeKey = $storeKey;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $item[$this->getData('name')] = $this->prepareItem($item);
            }
        }

        return $dataSource;
    }

    /**
     * Get data
     *
     * @param array $item
     * @return string
     */
    protected function prepareItem(array $item)
    {
        $content = '';
        if (!empty($item[$this->storeKey])) {
            $origStores = $item[$this->storeKey];
        }

        if (empty($origStores)) {
            return '';
        }
        if (!is_array($origStores)) {
            $origStores = [$origStores];
        }
        if (in_array(0, $origStores) && count($origStores) === 1) {
            return __('Admin');
        }

        $data = $this->systemStore->getStoresStructure(in_array(0, $origStores), $origStores);

        foreach ($data as $website) {
            $content .= '&#8226; ';
            $content .= $website['value'] === 0 ? (__('Admin') . '<br/>') : $website['label'];
            if (array_key_exists('children', $website)) {
                foreach ($website['children'] as $group) {
                    $content .= ' > ' . $this->escaper->escapeHtml($group['label']);
                    foreach ($group['children'] as $store) {
                        $content .= ' > ' . $this->escaper->escapeHtml($store['label']) . "<br/>";
                    }
                }
            }
        }

        return $content;
    }
}
