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

use pjz9n\advancedform\custom\element\Input;
use pjz9n\advancedform\custom\result\exception\InvalidResponseException;
use function fmod;
use function is_numeric;

class InputResult extends CustomFormResult
{
    public function __construct(
        protected Input $element,
        mixed           $rawValue,
    )
    {
        parent::__construct($rawValue);
    }

    public function getElement(): Input
    {
        return $this->element;
    }

    public function getText(): string
    {
        return $this->rawValue;
    }

    /**
     * @throws InvalidResponseException
     */
    public function getInt(): int
    {
        if (!is_numeric($this->rawValue) || ($this->rawValue !== 0 && fmod((float)$this->rawValue, 1) !== (float)0)) {
            throw new InvalidResponseException("must be int value");
        }
        return (int)$this->rawValue;
    }

    /**
     * @throws InvalidResponseException
     */
    public function getFloat(): float
    {
        if (!is_numeric($this->rawValue)) {
            throw new InvalidResponseException("must be float value");
        }
        return (float)$this->rawValue;
    }
}
