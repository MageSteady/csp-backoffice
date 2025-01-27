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

class Algorithm implements OptionSourceInterface
{
    protected array $algorithms = [null, 'sha256', 'sha384', 'sha512'];

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        return array_map(static function ($directive) {
            if ($directive === null) {
                return ['value' => null, 'label' => __('None')];
            }
            return ['value' => $directive, 'label' => __($directive)];
        }, $this->algorithms);
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray(): array
    {
        $array = [];
        foreach ($this->algorithms as $algorithm) {
            if ($algorithm !== null) {
                $array[] = [$algorithm => __($algorithm)];
            }
        }

        return $array;
    }
}
