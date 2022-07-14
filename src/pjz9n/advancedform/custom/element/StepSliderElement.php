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

use function array_map;
use function array_merge;

class StepSliderElement extends SelectorElement
{
    public static function getType(): string
    {
        return ElementTypes::STEP_SLIDER;
    }

    public function __construct(string $text, array $options = [], ?int $default = null, ?string $name = null)
    {
        parent::__construct($text, $options, $default, $name);
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
                "steps" => array_map(fn(SelectorOption $option): string => $option->getText(), $this->options),
            ],
        );
    }
}
