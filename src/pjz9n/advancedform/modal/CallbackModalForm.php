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
use pjz9n\advancedform\modal\response\ModalFormResponse;
use pocketmine\player\Player;
use pocketmine\utils\Utils;

class CallbackModalForm extends ModalForm
{
    /**
     * @param string $title Form title
     * @param string $text Message text to display on the form
     * @param Closure|null $handleSelect Called when the button is selected
     * @param Closure|null $handleClose Called when the form is closed
     * @phpstan-param Closure(Player, ModalFormResponse): void|null $handleSelect
     * @phpstan-param Closure(Player): void|null $handleClose
     *
     * @see ModalForm::handleSelect()
     * @see ModalForm::handleClose()
     */
    public static function create(
        string   $title,
        string   $text,
        ?Closure $handleSelect = null,
        ?Closure $handleClose = null,
    ): self
    {
        return new self($title, $text, $handleSelect, $handleClose);
    }

    /**
     * @param string $title Form title
     * @param string $text Message text to display on the form
     * @param Closure|null $handleSelect Called when the button is selected
     * @param Closure|null $handleClose Called when the form is closed
     * @phpstan-param Closure(Player, ModalFormResponse): void|null $handleSelect
     * @phpstan-param Closure(Player): void|null $handleClose
     *
     * @see ModalForm::handleSelect()
     * @see ModalForm::handleClose()
     */
    public function __construct(
        string             $title,
        string             $text,
        protected ?Closure $handleSelect = null,
        protected ?Closure $handleClose = null,
    )
    {
        if ($this->handleSelect !== null) {
            // @formatter:off
            Utils::validateCallableSignature(function (Player $player, ModalFormResponse $response): void {}, $this->handleSelect);
            // @formatter:on
        }

        if ($this->handleClose !== null) {
            // @formatter:off
            Utils::validateCallableSignature(function (Player $player): void {}, $this->handleClose);
            // @formatter:on
        }

        parent::__construct($title, $text);
    }

    /**
     * @phpstan-return Closure(Player, ModalFormResponse): void|null
     */
    public function getHandleSelect(): ?Closure
    {
        return $this->handleSelect;
    }

    /**
     * @phpstan-param Closure(Player, ModalFormResponse): void|null $handleSelect
     */
    public function setHandleSelect(?Closure $handleSelect): self
    {
        $this->handleSelect = $handleSelect;
        return $this;
    }

    /**
     * @phpstan-return Closure(Player): void|null
     */
    public function getHandleClose(): ?Closure
    {
        return $this->handleClose;
    }

    /**
     * @phpstan-param Closure(Player): void|null $handleClose
     */
    public function setHandleClose(?Closure $handleClose): self
    {
        $this->handleClose = $handleClose;
        return $this;
    }

    protected function handleSelect(Player $player, ModalFormResponse $response): void
    {
        if ($this->handleSelect !== null) {
            ($this->handleSelect)($player, $response);
        }
    }

    protected function handleClose(Player $player): void
    {
        if ($this->handleClose !== null) {
            ($this->handleClose)($player);
        }
    }
}
