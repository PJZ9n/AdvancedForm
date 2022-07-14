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

use InvalidArgumentException;
use pjz9n\advancedform\custom\element\Element;
use pjz9n\advancedform\custom\element\handler\ElementHandler;
use pjz9n\advancedform\custom\response\CustomFormResponse;
use pjz9n\advancedform\FormBase;
use pjz9n\advancedform\FormTypes;
use pjz9n\advancedform\util\Utils;
use pocketmine\form\FormValidationException;
use pocketmine\player\Player;
use function array_key_exists;
use function array_push;
use function array_search;
use function array_unshift;
use function assert;
use function count;
use function gettype;
use function is_array;

class CustomForm extends FormBase
{
    protected static function getType(): string
    {
        return FormTypes::CUSTOM;
    }

    /**
     * @param string $title Form title
     * @param Element[] $elements
     * @phpstan-param list<Element> $elements
     */
    public function __construct(
        string          $title,
        protected array $elements = [],
    )
    {
        $this->elements = Utils::arrayToList($this->elements);

        parent::__construct($title);
    }

    /**
     * @return Element[]
     * @phpstan-return list<Element>
     */
    public function getElements(): array
    {
        return $this->elements;
    }

    /**
     * @param Element[] $elements
     * @phpstan-param list<Element> $elements
     */
    public function setElements(array $elements): self
    {
        $this->elements = Utils::arrayToList($elements);
        return clone $this;
    }

    public function prependElement(Element $element): self
    {
        array_unshift($this->elements, $element);
        return clone $this;
    }

    /**
     * @param Element[] $elements
     * @phpstan-param list<Element> $elements
     */
    public function prependElements(array $elements): self
    {
        array_unshift($this->elements, ...Utils::arrayToList($elements));
        return clone $this;
    }

    public function appendElement(Element $element): self
    {
        array_push($this->elements, $element);
        return clone $this;
    }

    /**
     * @param Element[] $elements
     * @phpstan-param list<Element> $elements
     */
    public function appendElements(array $elements): self
    {
        array_push($this->elements, ...Utils::arrayToList($elements));
        return clone $this;
    }

    public function removeElement(Element $element): self
    {
        if (($key = array_search($element, $this->elements, true)) === false) {
            throw new InvalidArgumentException("Element does not exists");
        }
        unset($this->elements[$key]);
        $this->elements = Utils::arrayToList($this->elements);
        return clone $this;
    }

    public function removeElementByOffset(int $offset): self
    {
        if (!array_key_exists($offset, $this->elements)) {
            throw new InvalidArgumentException("Element #$offset does not exists");
        }
        unset($this->elements[$offset]);
        $this->elements = Utils::arrayToList($this->elements);
        return clone $this;
    }

    final public function handleResponse(Player $player, $data): void
    {
        if ($data === null) {
            $this->handleClose($player);
            return;
        }
        if (!is_array($data)) {
            throw new FormValidationException("Excepted array, got " . gettype($data));
        }
        if (!Utils::isList($data)) {
            throw new FormValidationException("Excepted list, got no-list array");
        }
        $rawResponseCount = count($data);
        $elementsCount = count($this->elements);
        if ($rawResponseCount !== $elementsCount) {
            throw new FormValidationException("Excepted $elementsCount response(s), got $rawResponseCount response(s)");
        }
        $results = [];
        $handled = false;
        foreach ($data as $offset => $rawResponse) {
            assert(array_key_exists($offset, $this->elements), "The element is here");
            $element = $this->elements[$offset];
            try {
                $element->validate($rawResponse);
            } catch (FormValidationException $exception) {
                throw new FormValidationException("Element #$offset validation failed: " . $exception->getMessage(), previous: $exception);
            }
            $result = $element->generateResult($rawResponse);
            $results[$element->getName() ?? $offset] = $result;
            if ($element instanceof ElementHandler && $element->handle($this, $result, $player)) {
                $handled = true;
            }
        }
        if (!$handled) {
            $this->handleSubmit($player, new CustomFormResponse($results));
        }
    }

    /**
     * Called when the form is submitted
     * NOTE: This will not be called if successfully processed by ElementHandler!
     */
    protected function handleSubmit(Player $player, CustomFormResponse $response): void
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
     * @return Element[][]
     * @phpstan-return array<string, Element[]>
     */
    protected function getAdditionalData(): array
    {
        return [
            "content" => $this->elements,
        ];
    }
}
