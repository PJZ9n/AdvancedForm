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
use pocketmine\player\Player;
use pocketmine\utils\Utils;

/**
 * Create a Modal Form
 */
final class ModalFormBuilder
{
    public static function fromImmutable(ModalFormImmutable $form): self
    {
        return new self($form->getTitle(), $form->getText(), $form->getButton1Text(), $form->getButton2Text());
    }

    /**
     * @param string $title Form title
     * @param string $text Text displayed on the form
     * @param string $button1Text Text displayed on top button
     * @param string $button2Text Text displayed on bottom button
     */
    public static function create(
        string $title,
        string $text,
        string $button1Text = "gui.yes",
        string $button2Text = "gui.no",
    ): self
    {
        return new self($title, $text, $button1Text, $button2Text);
    }

    /**
     * Calling this is not recommended
     * Use create() instead
     *
     * @see ModalFormBuilder::create()
     */
    public function __construct(
        private string $title,
        private string $text,
        private string $button1Text = "gui.yes",
        private string $button2Text = "gui.no",
    )
    {
    }

    /**
     * @phpstan-param Closure(Player, bool): void $handleChoice Called when the button is choiced
     */
    public function build(Closure $handleChoice): ModalFormImmutable
    {
        Utils::validateCallableSignature(new CallbackType(
            new ReturnType(BuiltInTypes::VOID),
            new ParameterType("player", Player::class),
            new ParameterType("choice", BuiltInTypes::BOOL),
        ), $handleChoice);

        return new ModalFormImmutable($this->title, $this->text, $this->button1Text, $this->button2Text, $handleChoice);
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;
        return $this;
    }

    public function getButton1Text(): string
    {
        return $this->button1Text;
    }

    public function setButton1Text(string $button1Text): self
    {
        $this->button1Text = $button1Text;
        return $this;
    }

    public function getButton2Text(): string
    {
        return $this->button2Text;
    }

    public function setButton2Text(string $button2Text): self
    {
        $this->button2Text = $button2Text;
        return $this;
    }
}
