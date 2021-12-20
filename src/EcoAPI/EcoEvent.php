<?php

namespace EcoAPI;

use EcoAPI\Eco;

use pocketmine\event\plugin\PluginEvent;

class EcoEvent extends PluginEvent{
  
  public function __construct(Money $main){
    $this->main = $main;
  }
}
