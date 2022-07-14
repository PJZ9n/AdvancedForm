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

use pjz9n\advancedform\custom\result\CustomFormResult;
use pjz9n\advancedform\custom\result\InputResult;
use pocketmine\form\FormValidationException;
use function gettype;
use function is_string;

class InputElement extends Element
{
    public static function getType(): string
    {
        return ElementTypes::INPUT;
    }

    public function __construct(
        string            $text,
        protected ?string $placeHolder = null,
        protected ?string $default = null,
        ?string           $name = null,
    )
    {
        parent::__construct($text, $name);
    }

    public function getPlaceHolder(): ?string
    {
        return $this->placeHolder;
    }

    public function setPlaceHolder(?string $placeHolder): self
    {
        $this->placeHolder = $placeHolder;
        return clone $this;
    }

    public function getDefault(): ?string
    {
        return $this->default;
    }

    public function setDefault(?string $default): self
    {
        $this->default = $default;
        return clone $this;
    }

    public function validate(mixed $value): void
    {
        if (!is_string($value)) {
            throw new FormValidationException("Excepted string, got " . gettype($value));
        }
    }

    public function generateResult(mixed $value): CustomFormResult
    {
        return new InputResult($this, $value);
    }

    /**
     * @return string[]
     * @phpstan-return array<string, string>
     */
    public function jsonSerialize(): array
    {
        return array_merge(
            parent::jsonSerialize(),
            $this->placeHolder === null ? [] : ["placeholder" => $this->placeHolder],
            $this->default === null ? [] : ["default" => $this->default],
        );
    }
}
