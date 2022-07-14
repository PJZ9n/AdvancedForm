<?php

/*
 * Copyright (c) 2022 PJZ9n.
 *
 * This file is part of AdvancedForm.
 *
 * AdvancedForm is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * AdvancedForm is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with AdvancedForm. If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace pjz9n\advancedform\util;

use pocketmine\utils\AssumptionFailedError;
use function array_values;

final class Utils
{
    /**
     * @phpstan-template T of mixed
     *
     * @param mixed[] $array
     * @phpstan-param array<mixed, T> $array
     *
     * @return mixed[]
     * @phpstan-return list<T>
     */
    public static function arrayToList(array $array): array
    {
        return array_values($array);
    }

    /**
     * Check if the keys in the array are serial numbers
     * NOTE: Here, the list is that the keys are serial numbers in order
     *
     * @param mixed[] $array
     */
    public static function isList(array $array): bool
    {
        $i = 0;
        foreach ($array as $key => $value) {
            if ($key !== $i) {
                return false;
            }
            $i++;
        }
        return true;
    }

    /**
     * @phpstan-template T of mixed
     *
     * @phpstan-param T $value
     * @return T
     */
    public static function assertFalse(mixed $value): mixed
    {
        if ($value === false) throw new AssumptionFailedError("Assertion failed");
        return $value;
    }

    private function __construct()
    {
        //NOOP
    }
}
