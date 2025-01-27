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

namespace MageSteady\CspBackoffice\Controller\Adminhtml\Rule;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\Controller\ResultInterface;
use MageSteady\CspBackoffice\Controller\Adminhtml\Rule;

class NewAction extends Rule
{
    public function __construct(
        Context $context,
        protected ForwardFactory $resultForwardFactory
    )
    {
        parent::__construct($context);
    }

    /**
     * New action
     *
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        return $this->resultForwardFactory->create()->forward('edit');
    }
}

