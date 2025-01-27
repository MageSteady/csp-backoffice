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

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use MageSteady\CspBackoffice\Controller\Adminhtml\ViolationReport;
use MageSteady\CspBackoffice\Model\ResourceModel\ViolationReport as ViolationReportResourceModel;
use MageSteady\CspBackoffice\Model\ViolationReportFactory;

class Edit extends ViolationReport
{
    public function __construct(
        Context $context,
        protected Registry $coreRegistry,
        protected PageFactory $resultPageFactory,
        protected ViolationReportFactory $violationReportFactory,
        protected ViolationReportResourceModel $violationReportResourceModel
    )
    {
        parent::__construct($context);
    }

    /**
     * Edit action
     *
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $id = $this->getRequest()->getParam('violationreport_id');
        $report = $this->violationReportFactory->create();

        if ($id) {
            $this->violationReportResourceModel->load($report, $id);
            if (!$report->getId()) {
                $this->messageManager->addErrorMessage(__('This violation report no longer exists.'));
                /** @var Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }
        $this->coreRegistry->register('magesteady_cspbackoffice_violationreport', $report);

        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $this->initPage($resultPage)->addBreadcrumb(__('Violation Report'), __('Violation Report'));

        $resultPage->getConfig()->getTitle()->prepend(__('Violation Reports'));
        $resultPage->getConfig()->getTitle()->prepend(__('Violation Report %1', $report->getId()));
        return $resultPage;
    }
}

