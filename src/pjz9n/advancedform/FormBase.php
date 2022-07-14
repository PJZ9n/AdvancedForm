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

namespace pjz9n\advancedform;

use InvalidArgumentException;
use pjz9n\advancedform\util\Utils;
use pocketmine\form\Form;
use function array_key_exists;
use function array_push;
use function array_search;
use function array_unshift;

abstract class FormBase implements Form
{
    /**
     * Returns the type of the form
     *
     * @see FormTypes
     */
    abstract static protected function getType(): string;

    /**
     * Returns additional data for the form
     *
     * @return mixed[]
     * @phpstan-return array<string, mixed>
     */
    abstract protected function getAdditionalData(): array;

    /**
     * @var string[]
     * @phpstan-var list<string>
     */
    protected array $messages = [];

    /**
     * @param string $title Form title
     */
    public function __construct(
        protected string $title,
    )
    {
    }

    /**
     * Clean the state
     */
    public function clean(): self
    {
        $this->messages = [];
        return $this;
    }

    /**
     * @return string[]
     * @phpstan-return list<string>
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * @param string[] $messages
     * @phpstan-param list<string> $messages
     */
    public function setMessages(array $messages): self
    {
        $this->messages = Utils::arrayToList($messages);
        return $this;
    }

    public function prependMessage(string $message): self
    {
        array_unshift($this->messages, $message);
        return $this;
    }

    /**
     * @param string[] $messages
     * @phpstan-param list<string> $messages
     */
    public function prependMessages(array $messages): self
    {
        array_unshift($this->messages, ...Utils::arrayToList($messages));
        return $this;
    }

    public function appendMessage(string $message): self
    {
        array_push($this->messages, $message);
        return $this;
    }

    /**
     * @param string[] $messages
     * @phpstan-param list<string> $messages
     */
    public function appendMessages(array $messages): self
    {
        array_push($this->messages, ...Utils::arrayToList($messages));
        return $this;
    }

    public function removeMessage(string $message): self
    {
        if (($key = array_search($message, $this->messages, true)) === false) {
            throw new InvalidArgumentException("Message does not exists");
        }
        unset($this->messages[$key]);
        $this->messages = Utils::arrayToList($this->messages);
        return $this;
    }

    public function removeMessageByOffset(int $offset): self
    {
        if (!array_key_exists($offset, $this->messages)) {
            throw new InvalidArgumentException("Message #$offset does not exists");
        }
        unset($this->messages[$offset]);
        $this->messages = Utils::arrayToList($this->messages);
        return $this;
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

    /**
     * @return mixed[]
     * @phpstan-return array<string, mixed>
     */
    final public function jsonSerialize(): array
    {
        return array_merge([
            "type" => static::getType(),
            "title" => $this->title,
        ], $this->getAdditionalData());
    }
}
