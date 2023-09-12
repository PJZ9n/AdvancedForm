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

namespace pjz9n\advancedform\custom;

use Closure;
use pjz9n\advancedform\custom\element\Element;
use pjz9n\advancedform\custom\response\CustomFormResponse;
use pocketmine\player\Player;
use pocketmine\utils\Utils;

class CallbackCustomForm extends CustomForm
{
    /**
     * @param string $title Form title
     * @param Closure|null $handleSubmit Called when the form is submitted
     * @phpstan-param Closure(Player, CustomFormResponse): void|null $handleSubmit
     * @param Closure|null $handleClose Called when the form is closed
     * @phpstan-param Closure(Player): void|null $handleClose
     * @param Element[] $elements
     * @phpstan-param list<Element> $elements
     */
    public static function create(
        string   $title,
        ?Closure $handleSubmit = null,
        ?Closure $handleClose = null,
        array    $elements = [],
    ): self
    {
        return new self($title, $handleSubmit, $handleClose, $elements);
    }

    /**
     * @param string $title Form title
     * @param Closure|null $handleSubmit Called when the form is submitted
     * @phpstan-param Closure(Player, CustomFormResponse): void|null $handleSubmit
     * @param Closure|null $handleClose Called when the form is closed
     * @phpstan-param Closure(Player): void|null $handleClose
     * @param Element[] $elements
     * @phpstan-param list<Element> $elements
     */
    public function __construct(
        string             $title,
        protected ?Closure $handleSubmit = null,
        protected ?Closure $handleClose = null,
        array              $elements = [],
    )
    {
        if ($this->handleSubmit !== null) {
            // @formatter:off
            Utils::validateCallableSignature(function (Player $player, CustomFormResponse $response): void {}, $this->handleSubmit);
            // @formatter:on
        }
        if ($this->handleClose !== null) {
            // @formatter:off
            Utils::validateCallableSignature(function (Player $player): void {}, $this->handleClose);
            // @formatter:on
        }

        parent::__construct($title, $elements);
    }

    /**
     * @phpstan-return Closure(Player, CustomFormResponse): void|null
     */
    public function getHandleSubmit(): ?Closure
    {
        return $this->handleSubmit;
    }

    /**
     * @phpstan-param Closure(Player, CustomFormResponse): void|null $handleSubmit
     */
    public function setHandleSubmit(?Closure $handleSubmit): self
    {
        $this->handleSubmit = $handleSubmit;
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

    protected function handleSubmit(Player $player, CustomFormResponse $response): void
    {
        if ($this->handleSubmit !== null) {
            ($this->handleSubmit)($player, $response);
        }
    }

    protected function handleClose(Player $player): void
    {
        if ($this->handleClose !== null) {
            ($this->handleClose)($player);
        }
    }
}
