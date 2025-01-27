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

namespace MageSteady\CspBackoffice\Model\Collector\CspWhitelistXml;

use DOMElement;
use Magento\Framework\Config\ConverterInterface;

class Converter implements ConverterInterface
{
    /**
     * @inheritDoc
     */
    public function convert($source): array
    {
        $policyConfig = [];

        $policies = $source->getElementsByTagName('policy');
        /** @var DOMElement $policy */
        foreach ($policies as $policy) {
            if ($policy->nodeType !== XML_ELEMENT_NODE) {
                continue;
            }
            $id = $policy->attributes->getNamedItem('id')->nodeValue;
            if (!array_key_exists($id, $policyConfig)) {
                $policyConfig[$id] = [];
            }
            /** @var DOMElement $value */
            foreach ($policy->getElementsByTagName('value') as $value) {
                $policyConfig[$id][$value->attributes->getNamedItem('id')->nodeValue] = [
                    'type' => $value->attributes->getNamedItem('type')->nodeValue,
                    'algorithm' => $value->attributes->getNamedItem('algorithm')?->nodeValue,
                    'value' => $value->nodeValue
                ];
            }
        }

        return $policyConfig;
    }
}
