<?php

/*
*
*    ____   ____  _____                 __  __ _____
*   |  _ \ / __ \|  __ \     /\        |  \/  |  __ \
*   | |_) | |  | | |__) |   /  \ ______| \  / | |__) |
*   |  _ <| |  | |  _  /   / /\ \______| |\/| |  ___/
*   | |_) | |__| | | \ \  / ____ \     | |  | | |
*   |____/ \____/|_|  \_\/_/    \_\    |_|  |_|_|
*
* This program is free software: you can redistribute it and/or modify 
* it under the terms of the GNU Lesser General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* @author Bora Team
* @link http://bora.kodmadeni.com/
*
*
*/

namespace bora\command\defaults;

use bora\command\CommandSender;
use bora\lang\TranslationContainer;
use bora\utils\TextFormat;

class ClearCommand extends VanillaCommand{
    public function __construct(string $name){
        parent::__construct(
            $name,
            "%bora.command.clear.description",
            "%bora.command.clear.usage",
            ["ci", "clearinventory"]
        );
        $this->setPermission("bora.command.clear");
    }

    public function execute(CommandSender $cs, string $label, array $args){
        if (!$this->testPermission($cs)){
            return true;
        }
        if (isset($args[0])){
            $hedef = $cs->getServer()->getPlayer($args[0]);
            if ($hedef == null){
                $cs->sendMessage(new TranslationContainer(TextFormat::RED . "%commands.generic.player.notFound"));
            }else{
                $hedef->getInventory()->clearAll();
                $hedef->sendMessage(new TranslationContainer("%bora.command.clear.success.hedef"));
                $cs->sendMessage(new TranslationContainer(TextFormat::RED . "%bora.command.clear.success.cs"));
            }
        }else{
            $cs->sendMessage(new TranslationContainer(TextFormat::RED . "%bora.command.clear.usage"));
        }
        return true;
    }
}
