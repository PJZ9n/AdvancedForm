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

namespace pjz9n\advancedform\modal;

use pocketmine\player\Player;

abstract class ModalForm
{
    /**
     * Builds and returns the form object
     * This is used when send form to the player
     */
    final public function build(): ModalFormImmutable
    {
        return $this->getBuilder()->build(function (Player $player, bool $choice): void {
            $this->handleChoice($player, $choice);
        });
    }

    /**
     * Called when the button is choiced
     */
    abstract protected function handleChoice(Player $player, bool $choice): void;

    /**
     * Returns a form builder
     */
    abstract protected function getBuilder(): ModalFormBuilder;
}
