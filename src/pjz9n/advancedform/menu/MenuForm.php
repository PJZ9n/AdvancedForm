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

use InvalidArgumentException;
use pjz9n\advancedform\button\Button;
use pjz9n\advancedform\FormBase;
use pjz9n\advancedform\FormTypes;
use pjz9n\advancedform\menu\response\MenuFormResponse;
use pjz9n\advancedform\util\Utils;
use pocketmine\form\FormValidationException;
use pocketmine\player\Player;
use function array_key_exists;
use function gettype;
use function is_int;

class MenuForm extends FormBase
{
    protected static function getType(): string
    {
        return FormTypes::MENU;
    }

    /**
     * @param string $title Form title
     * @param string $text Message text to display on the form
     * @param Button[] $buttons List of selectable buttons
     * @phpstan-param list<Button> $buttons
     */
    public function __construct(
        string           $title,
        protected string $text,
        protected array  $buttons = [],
    )
    {
        $this->buttons = Utils::arrayToList($this->buttons);

        parent::__construct($title);
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;
        return clone $this;
    }

    /**
     * @return Button[]
     * @phpstan-return list<Button>
     */
    public function getButtons(): array
    {
        return $this->buttons;
    }

    /**
     * @param Button[] $buttons
     * @phpstan-param list<Button> $buttons
     */
    public function setButtons(array $buttons): self
    {
        $this->buttons = Utils::arrayToList($buttons);
        return clone $this;
    }

    public function prependButton(Button $button): self
    {
        array_unshift($this->buttons, $button);
        return clone $this;
    }

    /**
     * @param Button[] $buttons
     * @phpstan-param list<Button> $buttons
     */
    public function prependButtons(array $buttons): self
    {
        array_unshift($this->buttons, ...Utils::arrayToList($buttons));
        return clone $this;
    }

    public function appendButton(Button $button): self
    {
        array_push($this->buttons, $button);
        return clone $this;
    }

    /**
     * @param Button[] $buttons
     * @phpstan-param list<Button> $buttons
     */
    public function appendButtons(array $buttons): self
    {
        array_push($this->buttons, ...Utils::arrayToList($buttons));
        return clone $this;
    }

    public function removeButton(Button $button): self
    {
        if (($key = array_search($button, $this->buttons, true)) === false) {
            throw new InvalidArgumentException("Button does not exists");
        }
        unset($this->buttons[$key]);
        $this->buttons = Utils::arrayToList($this->buttons);
        return clone $this;
    }

    public function removeButtonByOffset(int $offset): self
    {
        if (!array_key_exists($offset, $this->buttons)) {
            throw new InvalidArgumentException("Button #$offset does not exists");
        }
        unset($this->buttons[$offset]);
        $this->buttons = Utils::arrayToList($this->buttons);
        return clone $this;
    }

    final public function handleResponse(Player $player, $data): void
    {
        if ($data === null) {
            $this->handleClose($player);
        } else if (is_int($data)) {
            $buttonsCount = count($this->buttons);
            if ($data < 0 || $data >= $buttonsCount) {
                throw new  FormValidationException("Excepted range 0 ... " . ($buttonsCount - 1) . ", got $data");
            }
            assert(array_key_exists($data, $this->buttons), "There is a button here");
            $selectedButton = $this->buttons[$data];
            $handler = $selectedButton->getHandler();
            if ($handler === null || (!$handler->handle($this, $selectedButton, $player))) {
                $this->handleSelect($player, new MenuFormResponse($selectedButton, $data));
            }
        } else {
            throw new FormValidationException("Excepted int or null, got " . gettype($data));
        }
    }

    /**
     * Called when the button is selected
     * NOTE: This will not be called if successfully processed by ButtonHandler!
     */
    protected function handleSelect(Player $player, MenuFormResponse $response): void
    {
        //NOOP
    }

    /**
     * Called when the form is closed
     */
    protected function handleClose(Player $player): void
    {
        //NOOP
    }

    /**
     * @return mixed[]
     * @phpstan-return array<string, string|Button[]>
     */
    protected function getAdditionalData(): array
    {
        return [
            "content" => $this->text,
            "buttons" => $this->buttons,
        ];
    }
}
