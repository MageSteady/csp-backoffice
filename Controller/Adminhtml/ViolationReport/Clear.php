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

namespace MageSteady\CspBackoffice\Controller\Adminhtml\ViolationReport;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;
use MageSteady\CspBackoffice\Model\ResourceModel\ViolationReport;

class Clear extends Action
{
    public const ADMIN_RESOURCE = 'MageSteady_CspBackoffice::Csp';

    public function __construct(
        Context $context,
        protected ViolationReport $reportResourceModel
    )
    {
        parent::__construct($context);
    }

    public function execute(): ResultInterface
    {
        $this->reportResourceModel->deleteAll();

        $this->messageManager->addSuccessMessage(__('Violation Reports were successfully cleared.'));

        return $this->resultRedirectFactory->create()->setPath('*/*/index');
    }
}
