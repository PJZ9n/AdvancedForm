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

namespace pjz9n\advancedform\menu\button;

use JsonSerializable;
use pjz9n\advancedform\menu\button\icon\MenuButtonImage;
use function array_merge;

class MenuButton implements JsonSerializable
{
    public function __construct(
        private string           $text,
        private ?MenuButtonImage $image = null,
        private ?string          $name = null,
    )
    {
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getImage(): ?MenuButtonImage
    {
        return $this->image;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return mixed[]
     * @phpstan-return array<string, string|MenuButtonImage>
     */
    public function jsonSerialize(): array
    {
        return array_merge(
            ["text" => $this->text],
            $this->image === null ? [] : ["image" => $this->image],
        );
    }
}