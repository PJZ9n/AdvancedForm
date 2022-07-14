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
use pjz9n\advancedform\custom\result\ToggleResult;
use pocketmine\form\FormValidationException;
use function gettype;
use function is_bool;

class Toggle extends Element
{
    public static function getType(): string
    {
        return ElementTypes::TOGGLE;
    }

    public function __construct(
        string          $text,
        protected ?bool $default = null,
        ?string         $name = null,
    )
    {
        parent::__construct($text, $name);
    }

    public function getDefault(): ?bool
    {
        return $this->default;
    }

    public function setDefault(?bool $default): self
    {
        $this->default = $default;
        return clone $this;
    }

    public function validate(mixed $value): void
    {
        if (!is_bool($value)) {
            throw new FormValidationException("Excepted bool, got " . gettype($value));
        }
    }

    public function generateResult(mixed $value): CustomFormResult
    {
        return new ToggleResult($this, $value);
    }

    /**
     * @return string[]
     * @phpstan-return array<string, string|bool>
     */
    public function jsonSerialize(): array
    {
        return array_merge(
            parent::jsonSerialize(),
            $this->default === null ? [] : ["default" => $this->default],
        );
    }
}
