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

use pjz9n\advancedform\button\Button;
use pjz9n\advancedform\FormBase;
use pjz9n\advancedform\FormTypes;
use pjz9n\advancedform\modal\response\ModalFormResponse;
use pocketmine\form\FormValidationException;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use function array_map;
use function gettype;
use function implode;
use function is_bool;
use function str_repeat;

abstract class ModalForm extends FormBase
{
    protected static function getType(): string
    {
        return FormTypes::MODAL;
    }

    protected Button $yesButton;
    protected Button $noButton;

    /**
     * @param string $title Form title
     * @param string $text Message text to display on the form
     */
    public function __construct(
        string           $title,
        protected string $text,
        ?Button          $yesButton = null,
        ?Button          $noButton = null,
    )
    {
        $this->yesButton = $yesButton ?? new Button("gui.yes");
        $this->noButton = $noButton ?? new Button("gui.no");

        parent::__construct($title);
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

    public function getYesButton(): Button
    {
        return $this->yesButton;
    }

    public function setYesButton(Button $yesButton): self
    {
        $this->yesButton = $yesButton;
        return $this;
    }

    public function getNoButton(): Button
    {
        return $this->noButton;
    }

    public function setNoButton(Button $noButton): self
    {
        $this->noButton = $noButton;
        return $this;
    }

    final public function handleResponse(Player $player, $data): void
    {
        if (!is_bool($data)) {
            throw new FormValidationException("Expected bool, got " . gettype($data));
        }
        $selectedButton = $data ? $this->yesButton : $this->noButton;
        $handler = $selectedButton->getHandler();
        if ($handler === null || (!$handler->handle($this, $selectedButton, $player))) {
            $this->handleSelect($player, new ModalFormResponse($selectedButton, $data));
        }
    }

    /**
     * Called when the button is selected
     * NOTE: This will not be called if successfully processed by ButtonHandler!
     */
    protected function handleSelect(Player $player, ModalFormResponse $response): void
    {
        //NOOP
    }

    /**
     * @return string[]
     * @phpstan-return array<string, string>
     */
    protected function getAdditionalData(): array
    {
        return [
            "content" => (count($this->messages) <= 0 ? "" : implode(TextFormat::EOL, array_map(fn(string $message): string => $message . TextFormat::RESET, $this->messages)) . str_repeat(TextFormat::EOL, 2)) . $this->text,
            "button1" => $this->yesButton->getText(),
            "button2" => $this->noButton->getText(),
        ];
    }
}
