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

use pjz9n\advancedform\button\handler\ButtonHandler;
use pjz9n\advancedform\button\image\ButtonImage;
use function array_merge;

class ImageButton extends Button
{
    /**
     * @param string $text Label text displayed on the button
     * @param ButtonImage $image Button image displayed with the button
     * @param ButtonHandler|null $handler Handler to handle when a button is selected
     * @param string|null $name Name to identify the button
     */
    public function __construct(
        string                $text,
        protected ButtonImage $image,
        ?ButtonHandler        $handler = null,
        mixed                 $value = null,
        ?string               $name = null,
    )
    {
        parent::__construct($text, $handler, $value, $name);
    }

    public function getImage(): ButtonImage
    {
        return $this->image;
    }

    public function setImage(ButtonImage $image): self
    {
        $this->image = $image;
        return $this;
    }

    /**
     * @return string[]|ButtonImage[]
     * @phpstan-return array<string, string|ButtonImage>
     */
    public function jsonSerialize(): array
    {
        return array_merge(parent::jsonSerialize(), [
            "image" => $this->image,
        ]);
    }
}
