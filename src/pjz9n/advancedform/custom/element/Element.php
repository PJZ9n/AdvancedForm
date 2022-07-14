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

use JsonSerializable;
use pjz9n\advancedform\custom\result\CustomFormResult;
use pocketmine\form\FormValidationException;

abstract class Element implements JsonSerializable
{
    /**
     * Returns the type of element
     *
     * @see ElementTypes
     */
    abstract public static function getType(): string;

    /**
     * Validate the value
     *
     * @throws FormValidationException
     */
    abstract public function validate(mixed $value): void;

    /**
     * Create a result object from the values
     * Guaranteed to be passed a validated value
     */
    abstract public function generateResult(mixed $value): CustomFormResult;

    public function __construct(
        protected string  $text,
        protected ?string $name = null,
    )
    {
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string[]
     * @phpstan-return array<string, string>
     */
    public function jsonSerialize(): array
    {
        return [
            "type" => $this->getType(),
            "text" => $this->text,
        ];
    }
}
