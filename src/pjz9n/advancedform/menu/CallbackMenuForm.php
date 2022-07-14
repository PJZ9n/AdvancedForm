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
use pjz9n\advancedform\button\Button;
use pjz9n\advancedform\menu\response\MenuFormResponse;
use pocketmine\player\Player;
use pocketmine\utils\Utils;

class CallbackMenuForm extends MenuForm
{
    /**
     * @param string $title Form title
     * @param string $text Message text to display on the form
     * @param Closure|null $handleSelect Called when the button is selected
     * @phpstan-param Closure(Player, MenuFormResponse): void|null $handleSelect
     * @param Closure|null $handleClose Called when the form is closed
     * @phpstan-param Closure(Player): void|null $handleClose
     * @param Button[] $buttons List of selectable buttons
     * @phpstan-param list<Button> $buttons
     *
     * @see MenuForm::handleSelect()
     * @see MenuForm::handleClose()
     */
    public static function create(
        string   $title,
        string   $text,
        ?Closure $handleSelect = null,
        ?Closure $handleClose = null,
        array    $buttons = [],
    )
    {
        if ($handleSelect !== null) {
            // @formatter:off
            Utils::validateCallableSignature(function (Player $player, MenuFormResponse $response): void {}, $handleSelect);
            // @formatter:on
        }
        if ($handleClose !== null) {
            // @formatter:off
            Utils::validateCallableSignature(function (Player $player): void {}, $handleClose);
            // @formatter:on
        }

        return new self($title, $text, $handleSelect, $handleClose, $buttons);
    }

    /**
     * @param string $title Form title
     * @param string $text Message text to display on the form
     * @param Closure|null $handleSelect Called when the button is selected
     * @phpstan-param Closure(Player, MenuFormResponse): void|null $handleSelect
     * @param Closure|null $handleClose Called when the form is closed
     * @phpstan-param Closure(Player): void|null $handleClose
     * @param Button[] $buttons List of selectable buttons
     * @phpstan-param list<Button> $buttons
     *
     * @see MenuForm::handleSelect()
     * @see MenuForm::handleClose()
     */
    public function __construct(
        string             $title,
        string             $text,
        protected ?Closure $handleSelect = null,
        protected ?Closure $handleClose = null,
        array              $buttons = [],
    )
    {
        if ($this->handleSelect !== null) {
            // @formatter:off
            Utils::validateCallableSignature(function (Player $player, MenuFormResponse $response): void {}, $this->handleSelect);
            // @formatter:on
        }
        if ($this->handleClose !== null) {
            // @formatter:off
            Utils::validateCallableSignature(function (Player $player): void {}, $this->handleClose);
            // @formatter:on
        }

        parent::__construct($title, $text, $buttons);
    }

    /**
     * @phpstan-return Closure(Player, MenuFormResponse): void|null
     */
    public function getHandleSelect(): ?Closure
    {
        return $this->handleSelect;
    }

    /**
     * @phpstan-param Closure(Player, MenuFormResponse): void|null $handleSelect
     */
    public function setHandleSelect(?Closure $handleSelect): self
    {
        $this->handleSelect = $handleSelect;
        return clone $this;
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
        return clone $this;
    }

    protected function handleSelect(Player $player, MenuFormResponse $response): void
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
