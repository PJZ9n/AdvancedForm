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

use pjz9n\advancedform\custom\result\CustomFormResult;
use pjz9n\advancedform\custom\result\SliderResult;
use pocketmine\form\FormValidationException;
use function array_merge;
use function gettype;
use function is_float;

class SliderElement extends Element
{
    public static function getType(): string
    {
        return ElementTypes::SLIDER;
    }

    public function __construct(
        string           $text,
        protected float  $min,
        protected float  $max,
        protected float  $step,
        protected ?float $default = null,
        ?string          $name = null,
    )
    {
        parent::__construct($text, $name);
    }

    public function getMin(): float
    {
        return $this->min;
    }

    public function setMin(float $min): self
    {
        $this->min = $min;
        return clone $this;
    }

    public function getMax(): float
    {
        return $this->max;
    }

    public function setMax(float $max): self
    {
        $this->max = $max;
        return clone $this;
    }

    public function getStep(): float
    {
        return $this->step;
    }

    public function setStep(float $step): self
    {
        $this->step = $step;
        return clone $this;
    }

    public function getDefault(): ?float
    {
        return $this->default;
    }

    public function setDefault(?float $default): self
    {
        $this->default = $default;
        return clone $this;
    }

    public function validate(mixed $value): void
    {
        if ((!is_int($value)) && (!is_float($value))) {
            throw new FormValidationException("Excepted int or float, got " . gettype($value));
        }
        if ($value < $this->min || $value > $this->max) {
            throw new FormValidationException("Excepted range $this->min ... $this->max, got " . $value);
        }
        //TODO: Add step validations (float has an error)
    }

    public function generateResult(mixed $value): CustomFormResult
    {
        return new SliderResult($this, $value);
    }

    /**
     * @return string[]|float[]
     * @phpstan-return array<string, string|float>
     */
    public function jsonSerialize(): array
    {
        return array_merge(
            parent::jsonSerialize(),
            [
                "min" => $this->min,
                "max" => $this->max,
                "step" => $this->step,
            ],
            $this->default === null ? [] : ["default" => $this->default],
        );
    }
}
