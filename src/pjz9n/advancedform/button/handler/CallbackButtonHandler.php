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

namespace pjz9n\advancedform\button\handler;

use Closure;
use pjz9n\advancedform\button\Button;
use pocketmine\form\Form;
use pocketmine\player\Player;
use pocketmine\utils\Utils;

class CallbackButtonHandler implements ButtonHandler
{
    /**
     * @param Closure $callback Called when the button is selected (Returns true if the handle succeeds)
     * @phpstan-param Closure(Form, Button, Player): bool $callback
     */
    public function __construct(
        protected Closure $callback,
    )
    {
        // @formatter:off
        Utils::validateCallableSignature(function (Form $form, Button $button, Player $player): bool { return true; }, $this->callback);
        // @formatter:on
    }

    public function handle(Form $form, Button $button, Player $player): bool
    {
        return ($this->callback)($form, $button, $player);
    }
}
