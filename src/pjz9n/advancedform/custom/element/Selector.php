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

namespace pjz9n\advancedform\custom\element;

use InvalidArgumentException;
use pjz9n\advancedform\custom\result\CustomFormResult;
use pjz9n\advancedform\custom\result\SelectorResult;
use pjz9n\advancedform\util\Utils;
use pocketmine\form\FormValidationException;
use function array_key_exists;
use function array_merge;
use function array_push;
use function array_search;
use function array_unshift;
use function gettype;
use function is_int;

abstract class Selector extends Element
{
    /**
     * @param SelectorOption[] $options
     * @phpstan-param list<SelectorOption> $options
     */
    public function __construct(
        string          $text,
        protected array $options = [],
        protected ?int  $default = null,
        ?string         $name = null,
    )
    {
        $this->options = Utils::arrayToList($this->options);

        parent::__construct($text, $name);
    }

    /**
     * @return SelectorOption[]
     * @phpstan-return list<SelectorOption>
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param SelectorOption[] $options
     * @phpstan-param list<SelectorOption> $options
     */
    public function setOptions(array $options): self
    {
        $this->options = Utils::arrayToList($options);
        return $this;
    }

    public function prependOption(SelectorOption $option): self
    {
        array_unshift($this->options, $option);
        return $this;
    }

    /**
     * @param SelectorOption[] $options
     * @phpstan-param list<SelectorOption> $options
     */
    public function prependOptions(array $options): self
    {
        array_unshift($this->options, ...Utils::arrayToList($options));
        return $this;
    }

    public function appendOption(SelectorOption $option): self
    {
        array_push($this->options, $option);
        return $this;
    }

    /**
     * @param SelectorOption[] $options
     * @phpstan-param list<SelectorOption> $options
     */
    public function appendOptions(array $options): self
    {
        array_push($this->options, ...Utils::arrayToList($options));
        return $this;
    }

    public function removeOption(SelectorOption $option): self
    {
        if (($key = array_search($option, $this->options, true)) === false) {
            throw new InvalidArgumentException("SelectorOption does not exists");
        }
        unset($this->options[$key]);
        $this->options = Utils::arrayToList($this->options);
        return $this;
    }

    public function removeOptionByOffset(int $offset): self
    {
        if (!array_key_exists($offset, $this->options)) {
            throw new InvalidArgumentException("SelectorOption #$offset does not exists");
        }
        unset($this->options[$offset]);
        $this->options = Utils::arrayToList($this->options);
        return $this;
    }

    public function getDefault(): ?int
    {
        return $this->default;
    }

    public function setDefault(?int $default): self
    {
        $this->default = $default;
        return $this;
    }

    public function validate(mixed $value): void
    {
        if (!is_int($value)) {
            throw new FormValidationException("Excepted int, got " . gettype($value));
        }
    }

    public function generateResult(mixed $value): CustomFormResult
    {
        return new SelectorResult($this, $value);
    }

    /**
     * @return string[]|int[]
     * @phpstan-return array<string, string|int>
     */
    public function jsonSerialize(): array
    {
        return array_merge(
            parent::jsonSerialize(),
            $this->default === null ? [] : ["default" => $this->default],
        );
    }
}
