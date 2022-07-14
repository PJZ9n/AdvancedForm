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

namespace pjz9n\advancedform\custom\response;

use InvalidArgumentException;
use pjz9n\advancedform\custom\result\CustomFormResult;
use pjz9n\advancedform\custom\result\InputResult;
use pjz9n\advancedform\custom\result\SelectorResult;
use pjz9n\advancedform\custom\result\SliderResult;
use pjz9n\advancedform\custom\result\ToggleResult;
use pjz9n\advancedform\util\Utils;
use function gettype;

class CustomFormResponse
{
    /**
     * @var CustomFormResult[] List of results
     * @phpstan-var list<CustomFormResult>
     */
    protected array $resultsList;

    /**
     * @param CustomFormResult[] $results name=>result Map of results (The order of the values is important)
     * @phpstan-param array<string, CustomFormResult> $results
     */
    public function __construct(
        protected array $results,
    )
    {
        $this->resultsList = Utils::arrayToList($this->results);
    }

    /**
     * @return CustomFormResult[]
     * @phpstan-param array<string, CustomFormResult> $results
     */
    public function getResults(): array
    {
        return $this->results;
    }

    public function getResult(string $name): CustomFormResult
    {
        return $this->results[$name] ?? throw new InvalidArgumentException("Result for $name not exists");
    }

    public function getInputResult(string $name): InputResult
    {
        $result = $this->getResult($name);
        $this->checkType($result, InputResult::class);
        /** @var InputResult $result */
        return $result;
    }

    public function getSelectorResult(string $name): SelectorResult
    {
        $result = $this->getResult($name);
        $this->checkType($result, SelectorResult::class);
        /** @var SelectorResult $result */
        return $result;
    }

    public function getSliderResult(string $name): SliderResult
    {
        $result = $this->getResult($name);
        $this->checkType($result, SliderResult::class);
        /** @var SliderResult $result */
        return $result;
    }

    public function getToggleResult(string $name): ToggleResult
    {
        $result = $this->getResult($name);
        $this->checkType($result, ToggleResult::class);
        /** @var ToggleResult $result */
        return $result;
    }

    /**
     * @return CustomFormResult[]
     * @phpstan-return list<CustomFormResult>
     */
    public function getResultsList(): array
    {
        return $this->resultsList;
    }

    public function getResultByOffset(int $offset): CustomFormResult
    {
        return $this->resultsList[$offset] ?? throw new InvalidArgumentException("Result #$offset not exists");
    }

    public function getInputResultByOffset(int $offset): InputResult
    {
        $result = $this->getResultByOffset($offset);
        $this->checkType($result, InputResult::class);
        /** @var InputResult $result */
        return $result;
    }

    public function getSelectorResultByOffset(int $offset): SelectorResult
    {
        $result = $this->getResultByOffset($offset);
        $this->checkType($result, SelectorResult::class);
        /** @var SelectorResult $result */
        return $result;
    }

    public function getSliderResultByOffset(int $offset): SliderResult
    {
        $result = $this->getResultByOffset($offset);
        $this->checkType($result, SliderResult::class);
        /** @var SliderResult $result */
        return $result;
    }

    public function getToggleResultByOffset(int $offset): ToggleResult
    {
        $result = $this->getResultByOffset($offset);
        $this->checkType($result, ToggleResult::class);
        /** @var ToggleResult $result */
        return $result;
    }

    /**
     * @return mixed[]
     * @phpstan-return array<int|string, mixed>
     */
    public function getAllRawValues(): array
    {
        $values = [];
        foreach ($this->results as $key => $result) {
            $values[$key] = $result->getRawValue();
        }
        foreach ($this->resultsList as $offset => $result) {
            $values[$offset] = $result->getRawValue();
        }
        return $values;
    }

    /**
     * @phpstan-param class-string<CustomFormResult> $class
     */
    private function checkType(CustomFormResult $result, string $class): void
    {
        if (!($result instanceof $class)) {
            throw new InvalidArgumentException("Excepted $class, got " . gettype($result));
        }
    }
}
