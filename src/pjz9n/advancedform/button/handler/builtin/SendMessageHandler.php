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

namespace pjz9n\advancedform\button\handler\builtin;

use pjz9n\advancedform\button\Button;
use pjz9n\advancedform\button\handler\ButtonHandler;
use pocketmine\form\Form;
use pocketmine\player\Player;

class SendMessageHandler implements ButtonHandler
{
    public function __construct(
        protected string $message,
    )
    {
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;
        return clone $this;
    }

    public function handle(Form $form, Button $button, Player $player): bool
    {
        $player->sendMessage($this->message);
        return true;
    }
}
