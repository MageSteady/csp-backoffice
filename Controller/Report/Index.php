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

namespace MageSteady\CspBackoffice\Controller\Report;

use JsonException;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use MageSteady\CspBackoffice\Model\ResourceModel\ViolationReport;
use MageSteady\CspBackoffice\Model\ViolationReportFactory;

class Index implements HttpPostActionInterface, CsrfAwareActionInterface
{
    public function __construct(
        protected RequestInterface $request,
        protected RawFactory $resultRawFactory,
        protected ViolationReportFactory $reportFactory,
        protected ViolationReport $reportResourceModel
    )
    {
    }

    /**
     * Store violation report sent by the browser
     *
     * @return ResultInterface
     * @throws AlreadyExistsException
     */
    public function execute(): ResultInterface
    {
        $body = file_get_contents("php://input");

        try {
            $json = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return $this->resultRawFactory->create()->setHttpResponseCode(400);
        }

        $report = $this->reportFactory->create();
        $report->setData('json', $body);

        if (isset($json['body'])) {
            if (isset($json['body']['blockedURL'])) {
                $report->setData('blocked_url', $json['body']['blockedURL']);
            }
            if (isset($json['body']['documentURL'])) {
                $report->setData('document_url', $json['body']['documentURL']);
            }
            if (isset($json['body']['effectiveDirective'])) {
                $report->setData('effective_directive', $json['body']['effectiveDirective']);
            }
        }

        $this->reportResourceModel->save($report);

        return $this->resultRawFactory->create()->setHttpResponseCode(200);
    }

    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }

    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }
}
