<?php

declare(strict_types=1);

namespace net\splaturn\FieldCompletion;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;

/**
 * Class Core
 * @package net\splaturn\FieldCompletion
 */
class Core extends PluginBase{

	public function onEnable() {
		$this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
	}

	public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
	  if ($sender instanceof Player) {
      $session = Session::get($sender);
	    if ($session->checkParameters()) {
        $session->completion();
      }else {
	      $sender->sendMessage(TextFormat::RED . "{$this->getPrefix()} 先に回転軸、始点、終点の3つを設定してください");
      }
    }else {
	    $sender->sendMessage(TextFormat::RED . "{$this->getPrefix()} ゲーム内から実行して下さい");
    }
	  return true;
  }

  public function getPrefix(): string {
	  return "[{$this->getDescription()->getPrefix()}]";
  }
}
