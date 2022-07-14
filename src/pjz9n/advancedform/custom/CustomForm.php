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
use pjz9n\advancedform\custom\element\Input;
use pjz9n\advancedform\custom\element\Label;
use pjz9n\advancedform\custom\element\Selector;
use pjz9n\advancedform\custom\element\Slider;
use pjz9n\advancedform\custom\element\Toggle;
use pjz9n\advancedform\custom\highlight\HighlightInfo;
use pjz9n\advancedform\custom\response\CustomFormResponse;
use pjz9n\advancedform\FormBase;
use pjz9n\advancedform\FormTypes;
use pjz9n\advancedform\util\Utils;
use pocketmine\form\FormValidationException;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use WeakMap;
use function array_key_exists;
use function array_map;
use function array_merge;
use function array_push;
use function array_search;
use function array_unshift;
use function assert;
use function count;
use function gettype;
use function implode;
use function is_array;

abstract class CustomForm extends FormBase
{
    protected static function getType(): string
    {
        return FormTypes::CUSTOM;
    }

    protected ?CustomFormResponse $setDefaultsResponse = null;

    /**
     * @phpstan-var WeakMap<Element, HighlightInfo>
     */
    protected WeakMap $highlightElements;

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
        $this->highlightElements = new WeakMap();
        $this->elements = Utils::arrayToList($this->elements);

        parent::__construct($title);
    }

    /**
     * Highlight the element
     */
    public function setHighlight(Element $element, bool $highlight = true, string $prefix = TextFormat::BOLD . TextFormat::YELLOW): self
    {
        if ($highlight) {
            $this->highlightElements[$element] = new HighlightInfo($prefix);
        } else {
            unset($this->highlightElements[$element]);
        }
        return clone $this;
    }

    /**
     * @see CustomForm::setHighlight()
     */
    public function setHighlightByName(string $name, bool $highlight = true, string $prefix = TextFormat::BOLD . TextFormat::YELLOW): self
    {
        return $this->setHighlight($this->getElement($name) ?? throw new InvalidArgumentException("Element $name does not exists"), $highlight, $prefix);
    }

    /**
     * @see CustomForm::setHighlight()
     */
    public function setHighlightByOffset(int $offset, bool $highlight = true, string $prefix = TextFormat::BOLD . TextFormat::YELLOW): self
    {
        return $this->setHighlight($this->getElementByOffset($offset) ?? throw new InvalidArgumentException("Element #$offset does not exists"), $highlight, $prefix);
    }

    public function clearHighlights(): self
    {
        $this->highlightElements = new WeakMap();
        return clone $this;
    }

    /**
     * Sets the default value for the element based on the response
     * This is useful if you want to resubmit the same form.
     * Form data not reset while player is filling
     */
    public function setDefaults(CustomFormResponse $response): self
    {
        $this->setDefaultsResponse = $response;
        return clone $this;
    }

    public function clearDefaults(): self
    {
        $this->setDefaultsResponse = null;
        return clone $this;
    }

    /**
     * @return Element[]
     * @phpstan-return list<Element>
     */
    public function getElements(): array
    {
        return $this->elements;
    }

    public function getElement(string $name): ?Element
    {
        foreach ($this->elements as $element) {
            if ($element->getName() === $name) {
                return $element;
            }
        }
        return null;
    }

    public function getElementByOffset(int $offset): ?Element
    {
        return $this->elements[$offset] ?? null;
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

    public function clean(): self
    {
        $this->clearHighlights();
        $this->clearDefaults();

        parent::clean();
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
        $elementsCount += count($this->messages);
        if ($rawResponseCount !== $elementsCount) {
            throw new FormValidationException("Excepted $elementsCount response(s), got $rawResponseCount response(s)");
        }
        $results = [];
        $handled = false;
        foreach ($data as $offset => $rawResponse) {
            $offset -= count($this->messages);
            if ($offset < 0) {
                continue;//skip message label(s)
            }
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
        $elements = [];
        foreach ($this->elements as $offset => $element) {
            if (isset($this->highlightElements[$element])) {
                /** @var HighlightInfo $info */
                $info = $this->highlightElements[$element];
                $element = clone $element;
                $element->setText($info->getPrefix() . $element->getText());
            }
            if ($this->setDefaultsResponse !== null) {
                switch (true) {
                    case $element instanceof Input:
                    case $element instanceof Selector:
                    case $element instanceof Slider:
                    case $element instanceof Toggle:
                        $element = clone $element;
                        $element->setDefault($this->setDefaultsResponse->getResultByOffset($offset)->getRawValue());
                        break;
                }
            }
            $elements[] = $element;
        }
        return [
            "content" => array_merge(
                count($this->messages) <= 0 ? [] : [new Label(implode(TextFormat::EOL, array_map(fn(string $message): string => $message . TextFormat::RESET, $this->messages)))],
                $elements,
            ),
        ];
    }
}
