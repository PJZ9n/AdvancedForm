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

namespace pjz9n\advancedform;

use pocketmine\form\Form;

abstract class FormBase implements Form
{
    /**
     * Returns the type of the form
     *
     * @see FormTypes
     */
    abstract static protected function getType(): string;

    /**
     * Returns additional data for the form
     *
     * @return mixed[]
     * @phpstan-return array<string, mixed>
     */
    abstract protected function getAdditionalData(): array;

    /**
     * @param string $title Form title
     */
    public function __construct(
        protected string $title,
    )
    {
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return clone $this;
    }

    /**
     * @return mixed[]
     * @phpstan-return array<string, mixed>
     */
    final public function jsonSerialize(): array
    {
        return array_merge([
            "type" => static::getType(),
            "title" => $this->title,
        ], $this->getAdditionalData());
    }
}
