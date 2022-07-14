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

namespace pjz9n\advancedform\custom\result;

use pjz9n\advancedform\custom\element\SelectorElement;
use pjz9n\advancedform\custom\element\SelectorOption;

class SelectorResult extends CustomFormResult
{
    public function __construct(
        protected SelectorElement $element,
        mixed                     $rawValue,
    )
    {
        parent::__construct($rawValue);
    }

    public function getElement(): SelectorElement
    {
        return $this->element;
    }

    public function getOffset(): int
    {
        return $this->rawValue;
    }

    public function getOption(): SelectorOption
    {
        return $this->element->getOptions()[$this->rawValue];
    }

    public function getOptionValue(): mixed
    {
        return $this->getOption()->getValue();
    }

    public function getOptionName(): ?string
    {
        return $this->getOption()->getName();
    }
}
