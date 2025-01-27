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

namespace MageSteady\CspBackoffice\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Directive implements OptionSourceInterface
{
    /**
     * @var string[] Exhaustive list of directives as imposed by Laminas framework
     * @see \Laminas\Http\Header\ContentSecurityPolicy::$validDirectiveNames
     */
    protected array $validDirectiveNames = [
        // As per http://www.w3.org/TR/CSP/#directives
        // Fetch directives
        'child-src',
        'connect-src',
        'default-src',
        'font-src',
        'frame-src',
        'img-src',
        'manifest-src',
        'media-src',
        'object-src',
        'prefetch-src',
        'script-src',
        'script-src-elem',
        'script-src-attr',
        'style-src',
        'style-src-elem',
        'style-src-attr',
        'worker-src',

        // Document directives
        'base-uri',
        'plugin-types',
        'sandbox',

        // Navigation directives
        'form-action',
        'frame-ancestors',
        'navigate-to',

        // Reporting directives
        'report-uri',
        'report-to',

        // Other directives
        'block-all-mixed-content',
        'require-sri-for',
        'require-trusted-types-for',
        'trusted-types',
        'upgrade-insecure-requests',
    ];

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        return array_map(static function ($directive) {
            return ['value' => $directive, 'label' => __($directive)];
        }, $this->validDirectiveNames);
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray(): array
    {
        return array_map(static function ($directive) {
            return [$directive => __($directive)];
        }, $this->validDirectiveNames);
    }
}
