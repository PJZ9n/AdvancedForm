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

use Closure;
use DaveRandom\CallbackValidator\BuiltInTypes;
use DaveRandom\CallbackValidator\CallbackType;
use DaveRandom\CallbackValidator\ParameterType;
use DaveRandom\CallbackValidator\ReturnType;
use pjz9n\advancedform\FormBase;
use pjz9n\advancedform\FormTypes;
use pocketmine\form\FormValidationException;
use pocketmine\player\Player;
use pocketmine\utils\Utils;
use function gettype;
use function is_bool;

/**
 * Form with two buttons to select yes or no
 */
class ModalFormImmutable extends FormBase
{
    protected static function getType(): string
    {
        return FormTypes::MODAL;
    }

    /**
     * @param string $title Form title
     * @param string $text Text displayed on the form
     * @param string $button1Text Text displayed on top button
     * @param string $button2Text Text displayed on bottom button
     * @phpstan-param Closure(Player, bool): void $handleChoice Called when the button is choiced
     *
     * @internal It is recommended to use MenuFormBuilder to make this
     * @see MenuFormBuilder
     */
    public function __construct(
        string          $title,
        private string  $text,
        private string  $button1Text,
        private string  $button2Text,
        private Closure $handleChoice,
    )
    {
        Utils::validateCallableSignature(new CallbackType(
            new ReturnType(BuiltInTypes::VOID),
            new ParameterType("player", Player::class),
            new ParameterType("choice", BuiltInTypes::BOOL),
        ), $this->handleChoice);

        parent::__construct($title);
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getButton1Text(): string
    {
        return $this->button1Text;
    }

    public function getButton2Text(): string
    {
        return $this->button2Text;
    }

    final public function handleResponse(Player $player, $data): void
    {
        if (!is_bool($data)) {
            throw new FormValidationException("Expected bool, got " . gettype($data));
        }
        ($this->handleChoice)($player, $data);
    }

    /**
     * @return string[]
     * @phpstan-return array<string, string>
     */
    protected function getAdditionalData(): array
    {
        return [
            "content" => $this->text,
            "button1" => $this->button1Text,
            "button2" => $this->button2Text,
        ];
    }
}
