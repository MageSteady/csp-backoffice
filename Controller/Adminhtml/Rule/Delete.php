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

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use MageSteady\CspBackoffice\Controller\Adminhtml\Rule;
use MageSteady\CspBackoffice\Model\ResourceModel\Rule as RuleResourceModel;
use MageSteady\CspBackoffice\Model\RuleFactory;

class Delete extends Rule
{
    public function __construct(
        Context $context,
        protected RuleFactory $ruleFactory,
        protected RuleResourceModel $ruleResourceModel
    )
    {
        parent::__construct($context);
    }

    /**
     * Delete action
     *
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        $id = $this->getRequest()->getParam('rule_id');
        if ($id) {
            try {
                $rule = $this->ruleFactory->create();
                $this->ruleResourceModel->load($rule, $id);
                $this->ruleResourceModel->delete($rule);

                $this->messageManager->addSuccessMessage(__('You deleted the Rule.'));

                return $resultRedirect->setPath('*/*/');
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());

                return $resultRedirect->setPath('*/*/edit', ['rule_id' => $id]);
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find a Rule to delete.'));

        return $resultRedirect->setPath('*/*/');
    }
}

