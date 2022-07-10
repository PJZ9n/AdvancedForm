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

namespace pjz9n\advancedform\menu\button\handler;

use Closure;
use DaveRandom\CallbackValidator\BuiltInTypes;
use DaveRandom\CallbackValidator\CallbackType;
use DaveRandom\CallbackValidator\ParameterType;
use DaveRandom\CallbackValidator\ReturnType;
use pjz9n\advancedform\menu\button\icon\MenuButtonImage;
use pjz9n\advancedform\menu\MenuFormImmutable;
use pocketmine\player\Player;
use pocketmine\utils\Utils as PMUtils;

/**
 * If this button is selected, Call a callback
 */
class CallbackMenuButton extends HandlerMenuButton
{
    /**
     * @inheritDoc
     *
     * @param Closure $callback Called when the button is selected. return true if handle is successful (form handler will not be called).
     * @phpstan-param Closure(Player, MenuFormImmutable, CallbackMenuButton): bool $callback
     */
    public function __construct(
        string           $text,
        private Closure  $callback,
        ?MenuButtonImage $image = null,
        ?string          $name = null,
    )
    {
        PMUtils::validateCallableSignature(new CallbackType(
            new ReturnType(BuiltInTypes::BOOL),
            new ParameterType("player", Player::class),
            new ParameterType("form", MenuFormImmutable::class),
            new ParameterType("button", CallbackMenuButton::class),
        ), $this->callback);
        parent::__construct($text, $image, $name);
    }

    /**
     * @phpstan-param Closure(Player, MenuFormImmutable, CallbackMenuButton): bool $callback
     */
    public function getCallback(): Closure
    {
        return $this->callback;
    }

    public function handle(Player $player, MenuFormImmutable $form): bool
    {
        return ($this->callback)($player, $form, $this);
    }
}
