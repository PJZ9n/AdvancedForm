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

namespace pjz9n\advancedform\menu\button\icon;

use JsonSerializable;

class MenuButtonImage implements JsonSerializable
{
    /**
     * @see ImageTypes
     */
    public function __construct(
        private string $type,
        private string $data,
    )
    {
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getData(): string
    {
        return $this->data;
    }

    /**
     * @return string[]
     * @phpstan-return array<string, string>
     */
    public function jsonSerialize(): array
    {
        return [
            "type" => $this->type,
            "data" => $this->data,
        ];
    }
}
