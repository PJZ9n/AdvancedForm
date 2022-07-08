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
use InvalidArgumentException;
use pjz9n\advancedform\menu\button\MenuButton;
use pjz9n\advancedform\util\Utils;
use pocketmine\player\Player;
use pocketmine\utils\Utils as PMUtils;
use function array_key_exists;
use function array_push;
use function array_search;
use function array_unshift;

/**
 * Create a menu form
 */
final class MenuFormBuilder
{
    public static function fromImmutable(MenuFormImmutable $form): self
    {
        return new self($form->getTitle(), $form->getText(), $form->getButtons());
    }

    /**
     * @param string $title Form title
     * @param string $text Text displayed on the form
     * @param MenuButton[] $buttons
     * @phpstan-param list<MenuButton> $buttons
     */
    public static function create(
        string $title,
        string $text,
        array  $buttons = [],
    )
    {
        return new self($title, $text, Utils::arrayToList($buttons));
    }

    /**
     * Calling this is not recommended
     * Use create() instead
     *
     * @param MenuButton[] $buttons
     * @phpstan-param list<MenuButton> $buttons
     *
     * @see MenuFormBuilder::create()
     */
    public function __construct(
        private string $title,
        private string $text,
        private array  $buttons = [],
    )
    {
        $this->buttons = Utils::arrayToList($this->buttons);
    }

    /**
     * @param Closure $handleSelect Called when the button is selected
     * @phpstan-param Closure(Player, MenuFormResponse): void $handleSelect
     * @param Closure|null $handleClose Called when the form is closed
     * @phpstan-param Closure(Player): void|null $handleClose
     */
    public function build(Closure $handleSelect, ?Closure $handleClose = null): MenuFormImmutable
    {
        PMUtils::validateCallableSignature(new CallbackType(
            new ReturnType(BuiltInTypes::VOID),
            new ParameterType("player", Player::class),
            new ParameterType("response", MenuFormResponse::class),
        ), $handleSelect);
        if ($handleClose !== null) {
            PMUtils::validateCallableSignature(new CallbackType(
                new ReturnType(BuiltInTypes::VOID),
                new ParameterType("player", Player::class),
            ), $handleClose);
        }

        // @formatter:off
        return new MenuFormImmutable($this->title, $this->text, $this->buttons, $handleSelect, $handleClose ?? function (Player $player): void {});
        // @formatter:on
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

    /**
     * @return MenuButton[]
     * @phpstan-return list<MenuButton>
     */
    public function getButtons(): array
    {
        return $this->buttons;
    }

    /**
     * @param MenuButton[] $buttons
     * @phpstan-param list<MenuButton> $buttons
     */
    public function setButtons(array $buttons): self
    {
        $this->buttons = Utils::arrayToList($buttons);
        return $this;
    }

    public function prependButton(MenuButton $button): self
    {
        array_unshift($this->buttons, $button);
        return $this;
    }

    /**
     * @param MenuButton[] $buttons
     * @phpstan-param list<MenuButton> $buttons
     */
    public function prependButtons(array $buttons): self
    {
        array_unshift($this->buttons, ...Utils::arrayToList($buttons));
        return $this;
    }

    public function appendButton(MenuButton $button): self
    {
        array_push($this->buttons, $button);
        return $this;
    }

    /**
     * @param MenuButton[] $buttons
     * @phpstan-param list<MenuButton> $buttons
     */
    public function appendButtons(array $buttons): self
    {
        array_push($this->buttons, ...Utils::arrayToList($buttons));
        return $this;
    }

    public function removeButton(MenuButton $button): self
    {
        if (($key = array_search($button, $this->buttons, true)) === false) {
            throw new InvalidArgumentException("Button does not exists");
        }
        unset($this->buttons[$key]);
        $this->buttons = Utils::arrayToList($this->buttons);
        return $this;
    }

    public function removeButtonByOffset(int $offset): self
    {
        if (!array_key_exists($offset, $this->buttons)) {
            throw new InvalidArgumentException("Button #$offset does not exists");
        }
        unset($this->buttons[$offset]);
        $this->buttons = Utils::arrayToList($this->buttons);
        return $this;
    }
}
