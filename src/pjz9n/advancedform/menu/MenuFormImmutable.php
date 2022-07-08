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

use Closure;
use DaveRandom\CallbackValidator\BuiltInTypes;
use DaveRandom\CallbackValidator\CallbackType;
use DaveRandom\CallbackValidator\ParameterType;
use DaveRandom\CallbackValidator\ReturnType;
use pjz9n\advancedform\FormBase;
use pjz9n\advancedform\FormTypes;
use pjz9n\advancedform\menu\button\MenuButton;
use pjz9n\advancedform\util\Utils;
use pocketmine\form\FormValidationException;
use pocketmine\player\Player;
use pocketmine\utils\Utils as PMUtils;
use function array_key_exists;
use function count;
use function gettype;
use function is_int;

/**
 * Form with multiple buttons like a menu
 */
class MenuFormImmutable extends FormBase
{
    protected static function getType(): string
    {
        return FormTypes::MENU;
    }

    /**
     * @param string $title Form title
     * @param string $text Text displayed on the form
     * @param MenuButton[] $buttons
     * @phpstan-param list<MenuButton> $buttons
     * @param Closure $handleSelect Called when the button is selected
     * @phpstan-param Closure(Player, MenuFormResponse): void $handleSelect
     * @param Closure $handleClose Called when the form is closed
     * @phpstan-param Closure(Player): void $handleClose
     *
     * @internal It is recommended to use MenuFormBuilder to make this
     * @see MenuFormBuilder
     */
    public function __construct(
        string          $title,
        private string  $text,
        private array   $buttons,
        private Closure $handleSelect,
        private Closure $handleClose,
    )
    {
        $this->buttons = Utils::arrayToList($this->buttons);
        PMUtils::validateCallableSignature(new CallbackType(
            new ReturnType(BuiltInTypes::VOID),
            new ParameterType("player", Player::class),
            new ParameterType("response", MenuFormResponse::class),
        ), $this->handleSelect);
        PMUtils::validateCallableSignature(new CallbackType(
            new ReturnType(BuiltInTypes::VOID),
            new ParameterType("player", Player::class),
        ), $this->handleClose);

        parent::__construct($title);
    }

    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @return MenuButton[]
     */
    public function getButtons(): array
    {
        return $this->buttons;
    }

    final public function handleResponse(Player $player, $data): void
    {
        if ($data === null) {
            ($this->handleClose)($player);
            return;
        }
        if (!is_int($data)) {
            throw new FormValidationException("Excepted int, got " . gettype($data));
        }
        $buttonsCount = count($this->buttons);
        if ($data < 0 || $data >= $buttonsCount) {
            throw new FormValidationException("Excepted range 0-" . ($buttonsCount - 1) . ", got $data");
        }
        assert(array_key_exists($data, $this->buttons), "There is a button");
        ($this->handleSelect)($player, new MenuFormResponse($data, $this->buttons[$data]));
    }

    /**
     * @return string[]
     * @phpstan-return array<string, string|MenuButton[]>
     */
    protected function getAdditionalData(): array
    {
        return [
            "content" => $this->text,
            "buttons" => $this->buttons,
        ];
    }
}
