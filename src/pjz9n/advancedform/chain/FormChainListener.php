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

use JsonException;
use pjz9n\advancedform\AdvancedForm;
use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\form\Form;
use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;
use pocketmine\network\mcpe\protocol\ModalFormResponsePacket;
use pocketmine\network\PacketHandlingException;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use pocketmine\scheduler\ClosureTask;
use pocketmine\Server;
use ReflectionClass;
use ReflectionException;
use function array_key_exists;
use function json_decode;

final class FormChainListener implements Listener
{
    private static bool $ignoreForm = false;

    public static function register(Plugin $plugin): void
    {
        Server::getInstance()->getPluginManager()->registerEvents(new self, $plugin);
    }

    public static function setIgnoreForm(bool $ignoreForm = true): void
    {
        self::$ignoreForm = $ignoreForm;
        AdvancedForm::getLogger()->debug("FormChain: set ignore to " . self::$ignoreForm);
    }

    /**
     * @throws ReflectionException
     */
    public function onSubmitForm(DataPacketSendEvent $event): void
    {
        foreach ($event->getPackets() as $packet) {
            if ($packet instanceof ModalFormRequestPacket) {
                foreach ($event->getTargets() as $target) {
                    $player = $target->getPlayer();
                    if ($player === null) continue;
                    if (self::$ignoreForm) {
                        self::$ignoreForm = false;
                        AdvancedForm::getLogger()->debug("FormChain: ignored");
                        return;
                    }
                    $formId = $packet->formId;
                    //It will be added to the Player::$forms queue after it completes sending the packet and should be delayed
                    AdvancedForm::getPlugin()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($player, $formId): void {
                        $forms = $this->getForms($player);
                        assert(array_key_exists($formId, $forms), "Form is sent");
                        $form = $forms[$formId];
                        AdvancedForm::getLogger()->debug("FormChain: schedule: run: $formId");
                        PlayerFormChainMap::get($player)->push($form);
                    }), 1);
                    AdvancedForm::getLogger()->debug("FormChain: schedule: $formId");
                }
            }
        }
    }

    /**
     * @throws PacketHandlingException
     */
    public function onReceiveForm(DataPacketReceiveEvent $event): void
    {
        $player = $event->getOrigin()->getPlayer();
        if ($player === null) return;
        $packet = $event->getPacket();
        if ($packet instanceof ModalFormResponsePacket) {
            if (!array_key_exists($packet->formId, $this->getForms($player))) {
                AdvancedForm::getLogger()->debug("FormChain: receive: unexcepted form response");
                return;
            }
            $decodedFormData = null;
            if ($packet->cancelReason === null && $packet->formData !== null) {
                try {
                    $decodedFormData = json_decode($packet->formData, true, 2, JSON_THROW_ON_ERROR);
                } catch(JsonException $e) {
                    throw PacketHandlingException::wrap($e, "Failed to decode form response data");
                }
            }
            if ($decodedFormData === null) {
                //When close the form, the form chain is end
                //TODO: Closing the form may not send a response
                AdvancedForm::getLogger()->debug("FormChain: end: close form");
                PlayerFormChainMap::get($player)->end();
            }
        }
    }

    /**
     * @return Form[]
     * @phpstan-return array<int, Form>
     *
     * @throws ReflectionException
     */
    private function getForms(Player $player): array
    {
        $playerClass = new ReflectionClass($player);
        $formsProperty = $playerClass->getProperty("forms");
        $formsProperty->setAccessible(true);
        return $formsProperty->getValue($player);
    }
}
