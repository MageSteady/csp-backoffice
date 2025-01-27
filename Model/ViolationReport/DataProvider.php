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

namespace MageSteady\CspBackoffice\Model\ViolationReport;

use IntlDateFormatter;
use JsonException;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Escaper;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use MageSteady\CspBackoffice\Model\ResourceModel\ViolationReport\CollectionFactory;

class DataProvider extends AbstractDataProvider
{
    /**
     * @var array
     */
    protected array $loadedData;
    /**
     * @inheritDoc
     */
    protected $collection;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        protected DataPersistorInterface $dataPersistor,
        protected Escaper $escaper,
        protected TimezoneInterface $timezone,
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
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        foreach ($items as $report) {
            // beautify JSON and escape it
            $json = $report->getJson();
            if (!empty($json)) {
                try {
                    $json = json_encode(json_decode($json, true, 512, JSON_THROW_ON_ERROR), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                } catch (JsonException) {
                    $json = $report->getJson();
                }
                $json = $this->escaper->escapeHtml($json);
                $report->setData('json', '<pre><code>' . $json . '</code></pre>');
            }

            // beautify date
            $report->setCreatedAt($this->timezone->formatDate($report->getCreatedAt(), IntlDateFormatter::MEDIUM, true));

            $this->loadedData[$report->getId()] = $report->getData();
        }
        $data = $this->dataPersistor->get('magesteady_cspbackoffice_violationreport');

        if (!empty($data)) {
            $report = $this->collection->getNewEmptyItem();
            $report->setData($data);
            $this->loadedData[$report->getId()] = $report->getData();
            $this->dataPersistor->clear('magesteady_cspbackoffice_violationreport');
        }

        return $this->loadedData;
    }
}

