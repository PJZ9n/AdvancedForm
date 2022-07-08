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

namespace pjz9n\advancedform\menu;

use pocketmine\player\Player;

abstract class MenuForm
{
    /**
     * Builds and returns the form object
     * This is used when send form to the player
     */
    final public function build(): MenuFormImmutable
    {
        return $this->getBuilder()->build(function (Player $player, MenuFormResponse $response): void {
            $this->handleSelect($player, $response);
        }, function (Player $player): void {
            $this->handleClose($player);
        });
    }

    /**
     * Called when the button is selected
     */
    abstract protected function handleSelect(Player $player, MenuFormResponse $response): void;

    /**
     * Returns a form builder
     */
    abstract protected function getBuilder(): MenuFormBuilder;

    /**
     * Called when the form is closed
     */
    protected function handleClose(Player $player): void
    {
        //NOOP
    }
}
