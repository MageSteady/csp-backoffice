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

namespace MageSteady\CspBackoffice\Ui\Component\Listing;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class Clear implements ButtonProviderInterface
{
    public function __construct(
        protected UrlInterface $url
    )
    {
    }

    public function getButtonData(): array
    {
        return [
            'label' => __('Clear Violation Reports'),
            'class' => 'delete',
            'on_click' => 'deleteConfirm(\'' . __(
                    'Are you sure you want to do this?'
                ) . '<br><br><b>' . __('This will remove all violation reports.') . '</b>\', \'' . $this->getClearUrl() . '\')',
            'sort_order' => 20,
        ];
    }

    /**
     * Get URL for clear button
     *
     * @return string
     */
    public function getClearUrl(): string
    {
        return $this->url->getUrl('*/*/clear', []);
    }
}
