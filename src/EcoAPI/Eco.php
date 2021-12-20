<?php

namespace EcoAPI;

use pocketmine\Server;
use pocketmine\Player;

use pocketmine\plugin\PluginBase;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;

use pocketmine\event\Listener;

use pocketmine\utils\Config;

use pocketmine\event\player\PlayerJoinEvent;

use EcoAPI\EcoChangeEvent;
use EcoAPI\EcoEvent;

class Eco extends PluginBase implements Listener {
  
  public function onEnable(){
    $this->getLogger()->info("EcoAPI đã bật, hãy trải nghiệm ngay!");
    $this->getServer()->getPluginManager()->registerEvents($this, $this);
    $this->eco = new Config($this->getDataFolder() . "eco.yml", Config::YAML);
  }
  
  public function onDisable(){
    $this->getLogger()->info("EcoAPI đã tắt...");
  }
  
#----------------------------------------------------------------------------------------
  
  public function onJoin(PlayerJoinEvent $event){
    $player = $event->getPlayer();
    if(!$this->eco->exists($player->getName())){
      $this->eco->set($player->getName(), 0);
      $this->eco->save();
      $this->getServer()->getPluginManager()->callEvent(new EcoChangeEvent($this, $player));
    }
  }
  
  public function reduceMoney($player, $eco){
    if($player instanceof Player){
      if(is_numeric($eco)){
         $this->eco->set($player->getName(), ($this->eco->get($player->getName()) - $eco));
         $this->eco->save();
         $this->getServer()->getPluginManager()->callEvent(new EcoChangeEvent($this, $player));
      }
    }
  }
  
  public function addMoney($player, $eco){
    if($player instanceof Player){
      if(is_numeric($eco)){
         $this->eco->set($player->getName(), ($this->eco->get($player->getName()) + $eco));
         $this->eco->save();
         $this->getServer()->getPluginManager()->callEvent(new EcoChangeEvent($this, $player));
      }
    }
  }
  
  public function myMoney($player){
    if($player instanceof Player){
      
      return ($this->eco->get($player->getName()));
    }
  }
  
  public function getAllMoney(){
    return $this->eco->getAll();
  }
  
#----------------------------------------------------------------------------------------
  
  public function onCommand(CommandSender $sender, Command $cmd, String $label, array $args): bool{
    switch($cmd->getName()){
      case "mymoney":
        if($sender instanceof Player){
          $eco = $this->myMoney($sender);
          $sender->sendMessage("§b§l•§6 Số§e Tiền§6 của bạn:§e " . $eco);
        }else{
          $sender->sendMessage("Yêu cầu : Sử dụng lệnh trong game!");
        }
        break;
        
        case "setmoney":
          if($sender instanceof Player){
            if($sender->hasPermission("setmoney.khanggtrann")){
              if(isset($args[0])){
                if(isset($args[1])){
                  $player = $this->getServer()->getPlayer($args[0]);
                  if(!is_numeric($args[1])){
                    $sender->sendMessage("§b§l•§6 Lỗi : Kí tự phải là 1 chữ số!");
                    return true;
                  }
                  if(!$player instanceof Player){
                    $sender->sendMessage("§b§l•§6 Người chơi§a " . $args[0] . " §6không hoạt động!");
                    return true;
                  }
                  
                  $this->eco->set($player->getName(), $args[1]);
                  $this->eco->save();
                  $sender->sendMessage("§b§l•§6 Thành công chỉnh số §eTiền §6của người chơi§a " . $args[0] . " §6thành§e " . $args[1]);
                  $player->sendMessage("§b§l•§6 Số §eTiền§6 của bạn được chỉnh thành§e " . $args[1]);
                  $this->getServer()->getPluginManager()->callEvent(new EcoChangeEvent($this, $player));
                }else{
                  $sender->sendMessage("§b§l•§6 Lệnh: §e/setmoney {người chơi} {số lượng}");
                }
              }else{
                $sender->sendMessage("§b§l•§6 Lệnh: §e/setmoney {người chơi} {số lượng}");
              }
            }
          }else{
            $sender->sendMessage("Yêu cầu : Sử dụng lệnh trong game!");
          }
          break;
   
            case "pay":
              if($sender instanceof Player){
                if(isset($args[0])){
                  if(isset($args[1])){
                    $player2 = $this->getServer()->getPlayer($args[0]);
                    $eco = $this->myMoney($sender);
                    if(!$player2 instanceof Player){
                      $sender->sendMessage("§b§l•§6 Người chơi§a " . $args[0] . " §6không hoạt động!");
                      return true;
                    }
                    if(!is_numeric($args[1])){
                      $sender->sendMessage("§b§l•§6Yêu cầu : Kí tự phải là 1 chữ số");
                      return true;
                    }
                    if($args[0] === $sender->getName()){
                      $sender->sendMessage("§b§l•§6 Không thể tự trao §eTiền§6 cho bản thân!");
                      return true;
                    }
                    if($eco >= $args[1]){
                      $this->reduceMoney($sender, $args[1]);
                      $this->addMoney($player2, $args[1]);
                      $sender->sendMessage("§b§l•§6 Thành công trao§e " . $args[1] . " §e§lVNĐ §6cho§a " . $args[0]);
                      $player2->sendMessage("§b§l•§6 Người chơi§a " . $sender->getName() . " §6đã trao cho bạn§e " . $args[1] . " VNĐ!");
                    }else{
                      $sender->sendMessage("§b§l•§6 Lỗi : Không đủ số Tiền!");
                      return true;
                    }
                  }else{
                    $sender->sendMessage("§b§l•§6 Lệnh: §e/pay {người chơi} {số lượng}");
                  }
                }else{
                  $sender->sendMessage("§b§l•§6 Lệnh: §e/pay {người chơi} {số lượng}");
                }
              }else{
                $sender->sendMessage("Yêu cầu : Sử dụng lệnh trong game!");
              }
              break;
    }
    return true;
  }
}
