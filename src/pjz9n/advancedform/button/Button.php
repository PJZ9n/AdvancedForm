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

namespace pjz9n\advancedform\button;

use JsonSerializable;
use pjz9n\advancedform\button\handler\ButtonHandler;

class Button implements JsonSerializable
{
    /**
     * @param string $text Label text displayed on the button
     * @param string|null $name Name to identify the button
     * @param ButtonHandler|null Handler to handle when a button is selected
     */
    public function __construct(
        protected string         $text,
        protected ?string        $name = null,
        protected ?ButtonHandler $handler = null,
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
        return clone $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;
        return clone $this;
    }

    public function getHandler(): ?ButtonHandler
    {
        return $this->handler;
    }

    public function setHandler(?ButtonHandler $handler): self
    {
        $this->handler = $handler;
        return clone $this;
    }

    /**
     * @return string[]
     * @phpstan-return array<string, string>
     */
    public function jsonSerialize(): array
    {
        return [
            "text" => $this->text,
        ];
    }
}
