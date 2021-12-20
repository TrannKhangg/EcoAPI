<?php

namespace RPointAPI;

use EcoAPI\Eco;

use pocketmine\Player;

use EcoAPI\EcoEvent;

class EcoChangeEvent extends EcoEvent{
  
  public function __construct(Money $main, $player){
    $this->main = $main;
    $this->player = $player;
  }
  
  public function getPlayer(){
    return $this->player;
  }
  
  public function getMoney(){
    return $this->main->myMoney($this->player);
  }
}
