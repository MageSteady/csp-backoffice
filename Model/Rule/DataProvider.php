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

use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use MageSteady\CspBackoffice\Model\ResourceModel\Rule\CollectionFactory;

class DataProvider extends AbstractDataProvider
{
    protected ?array $loadedData = null;

    public function __construct(
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        CollectionFactory $collectionFactory,
        protected DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []
    )
    {
        $this->collection = $collectionFactory->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * @inheritDoc
     */
    public function getData(): ?array
    {
        if ($this->loadedData !== null) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        foreach ($items as $rule) {
            $this->loadedData[$rule->getId()] = $rule->getData();
        }
        $data = $this->dataPersistor->get('magesteady_cspbackoffice_rule');

        if (!empty($data)) {
            $rule = $this->collection->getNewEmptyItem();
            $rule->setData($data);
            $this->loadedData[$rule->getId()] = $rule->getData();
            $this->dataPersistor->clear('magesteady_cspbackoffice_rule');
        }

        return $this->loadedData;
    }
}

