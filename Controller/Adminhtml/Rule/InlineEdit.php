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
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use MageSteady\CspBackoffice\Model\ResourceModel\Rule as RuleResourceModel;
use MageSteady\CspBackoffice\Model\RuleFactory;

class InlineEdit extends Action
{
    public const ADMIN_RESOURCE = 'MageSteady_CspBackoffice::Csp';

    public function __construct(
        Context $context,
        protected JsonFactory $jsonFactory,
        protected RuleFactory $ruleFactory,
        protected RuleResourceModel $ruleResourceModel
    )
    {
        parent::__construct($context);
    }

    /**
     * Inline edit action
     *
     * @return ResultInterface
     * @throws LocalizedException
     */
    public function execute(): ResultInterface
    {
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];

        if ($this->getRequest()->getParam('isAjax')) {
            $postItems = $this->getRequest()->getParam('items', []);
            if (!count($postItems)) {
                $messages[] = __('Please correct the data sent.');
                $error = true;
            } else {
                foreach (array_keys($postItems) as $ruleId) {
                    $rule = $this->ruleFactory->create();
                    $this->ruleResourceModel->load($rule, $ruleId);
                    try {
                        $rule->setData(array_merge($rule->getData(), $postItems[$ruleId]));
                        $this->ruleResourceModel->save($rule);
                    } catch (Exception $e) {
                        $messages[] = "[Rule ID: $ruleId]  {$e->getMessage()}";
                        $error = true;
                    }
                }
            }
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }
}

