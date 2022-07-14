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

namespace pjz9n\advancedform\chain;

use pjz9n\advancedform\AdvancedForm;
use pocketmine\form\Form;
use pocketmine\player\Player;
use function array_key_last;
use function array_keys;
use function array_map;
use function array_pop;
use function array_values;
use function count;
use function implode;
use function in_array;

final class FormChain
{
    /**
     * @var Form[]
     * @phpstan-var list<Form>
     */
    private array $forms = [];

    /**
     * @return Form[]
     * @phpstan-return list<Form>
     */
    public function getAll(): array
    {
        return $this->forms;
    }

    public function getCurrent(): ?Form
    {
        if (count($this->forms) <= 0) return null;
        return $this->forms[array_key_last($this->forms)];
    }

    public function getPrevious(): ?Form
    {
        if (count($this->forms) <= 0) return null;
        return $this->forms[array_key_last($this->forms) - 1] ?? null;
    }

    public function push(Form $form): void
    {
        array_push($this->forms, $form);
        AdvancedForm::getLogger()->debug("FormChain: push");
        AdvancedForm::getLogger()->debug($this->debugStringForms());
    }

    public function back(): void
    {
        if (count($this->forms) <= 0) return;
        array_pop($this->forms);
        AdvancedForm::getLogger()->debug("FormChain: back");
        AdvancedForm::getLogger()->debug($this->debugStringForms());
    }

    public function end(): void
    {
        $this->forms = [];
        AdvancedForm::getLogger()->debug("FormChain: end");
        AdvancedForm::getLogger()->debug($this->debugStringForms());
    }

    /**
     * @param string[]|null $backTo List of form class names expected to return. Passing null will not filter but will reduce security
     * @phpstan-param list<class-string<Form>>|null $backTo
     */
    public function sendBack(Player $player, ?array $backTo): void
    {
        $previousForm = $this->getPrevious();
        $this->back();
        if ($previousForm === null) {
            AdvancedForm::getLogger()->debug("FormChain: sendBack: no previous");
        } else {
            if ($backTo !== null) {
                $previousFormClass = $previousForm::class;
                if (!in_array($previousFormClass, $backTo, true)) {
                    AdvancedForm::getLogger()->warning("Failed to back: Excepted class is [ " . implode($backTo) . " ], but the previous form class is $previousFormClass");
                    return;
                }
            }
            FormChainListener::setIgnoreForm();
            $player->sendForm($previousForm);
            AdvancedForm::getLogger()->debug("FormChain: sendBack");
        }
    }

    private function debugStringForms(): string
    {
        return "[ " . implode(", ", array_map(fn(int $offset, Form $form): string => $offset . " => " . $form::class, array_keys($this->forms), array_values($this->forms))) . " ]";
    }
}
