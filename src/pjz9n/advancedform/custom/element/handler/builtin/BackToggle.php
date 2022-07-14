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

namespace pjz9n\advancedform\custom\element\handler\builtin;

use pjz9n\advancedform\chain\PlayerFormChainMap;
use pjz9n\advancedform\custom\element\handler\ElementHandler;
use pjz9n\advancedform\custom\element\Toggle;
use pjz9n\advancedform\custom\result\CustomFormResult;
use pjz9n\advancedform\custom\result\ToggleResult;
use pocketmine\form\Form;
use pocketmine\player\Player;

class BackToggle extends Toggle implements ElementHandler
{
    /**
     * @param string[]|null $backTo List of form class names expected to return. Passing null will not filter but will reduce security
     * @phpstan-param list<class-string<Form>>|null $backTo
     */
    public function __construct(
        string           $text,
        protected ?array $backTo,
        ?bool            $default = null,
        ?string          $name = null,
    )
    {
        parent::__construct($text, $default, $name);
    }

    /**
     * @return string[]|null
     * @phpstan-return list<class-string<Form>>|null
     */
    public function getBackTo(): ?array
    {
        return $this->backTo;
    }

    /**
     * @param string[]|null $backTo List of form class names expected to return. Passing null will not filter but will reduce security
     * @phpstan-param list<class-string<Form>>|null $backTo
     */
    public function setBackTo(?array $backTo): self
    {
        $this->backTo = $backTo;
        return $this;
    }

    public function handle(Form $form, CustomFormResult $result, Player $player): bool
    {
        assert($result instanceof ToggleResult);
        if ($result->getValue()) {
            PlayerFormChainMap::get($player)->sendBack($player, $this->backTo);
            return true;
        }
        return false;
    }
}
