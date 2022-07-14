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

use pjz9n\advancedform\chain\FormChainListener;
use pjz9n\advancedform\chain\PlayerFormChainMap;
use pocketmine\plugin\Plugin;
use pocketmine\Server;
use PrefixedLogger;

final class AdvancedForm
{
    private static bool $initialized = false;
    private static Plugin $plugin;
    private static PrefixedLogger $logger;

    public static function init(Plugin $plugin): void
    {
        if (self::$initialized) return;//Return silently for practicality

        PlayerFormChainMap::init();
        FormChainListener::register($plugin);
        self::$plugin = $plugin;
        self::$logger = new PrefixedLogger(Server::getInstance()->getLogger(), "AdvancedForm");
    }

    public static function isInitialized(): bool
    {
        return self::$initialized;
    }

    public static function getPlugin(): Plugin
    {
        return self::$plugin;
    }

    public static function getLogger(): PrefixedLogger
    {
        return self::$logger;
    }

    private function __construct()
    {
        //NOOP
    }
}
