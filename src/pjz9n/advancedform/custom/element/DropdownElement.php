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

namespace pjz9n\advancedform\custom\element;

use pjz9n\advancedform\util\Utils;
use function array_map;
use function array_merge;

class DropdownElement extends SelectorElement
{
    /**
     * @param SelectorOption[] $options
     * @phpstan-param list<SelectorOption> $options
     */
    public static function create(
        string  $text,
        array   $options = [],
        ?int    $default = null,
        ?string $name = null,
    ): self
    {
        $options = Utils::arrayToList($options);

        return new self($text, $options, $default, $name);
    }

    public static function getType(): string
    {
        return ElementTypes::DROPDOWN;
    }

    /**
     * @return string[]|int[]
     * @phpstan-return array<string, string|int>
     */
    public function jsonSerialize(): array
    {
        return array_merge(
            parent::jsonSerialize(),
            [
                "options" => array_map(fn(SelectorOption $option): string => $option->getText(), $this->options),
            ],
        );
    }
}
