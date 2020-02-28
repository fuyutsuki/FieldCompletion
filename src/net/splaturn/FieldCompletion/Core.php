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
class Core extends PluginBase {

  private const OPPOSITE_DAMAGE = [
    0 => 1,
    1 => 0,
    2 => 3,
    3 => 2,
    4 => 5,
    5 => 4,
    6 => 7,
    7 => 6,
    8 => 9,
    9 => 8,
    10 => 11,
    11 => 10,
    12 => 13,
    13 => 12,
    14 => 15,
    15 => 14
  ];

  private const OPPOSITE_DAMAGE_X = [
    0 => 1,
    1 => 0,
    4 => 5,
    5 => 4,
    8 => 9,
    9 => 8,
    12 => 13,
    13 => 12
  ];

  private const OPPOSITE_DAMAGE_Z = [
    2 => 3,
    3 => 2,
    6 => 7,
    7 => 6,
    10 => 11,
    11 => 10,
    14 => 15,
    15 => 14
  ];

	public function onEnable() {
		$this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
	}

	public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
	  if ($sender instanceof Player) {
      $session = Session::get($sender);
	    if ($session->checkParameters()) {
        if (!empty($args[0])) {
          switch (strtolower($args[0])) {
            case "point":
              $session->pointCompletion();
              break;
            case "line":
              $session->lineCompletion();
              break;
          }
        }else {
          return false;
        }
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

  public static function getOppositeSideDamage(int $damage, ?bool $axisX = null): int {
	  if ($axisX !== null) {
      if ($axisX) {
        return self::OPPOSITE_DAMAGE_Z[$damage] ?? $damage;
      }else {
        return self::OPPOSITE_DAMAGE_X[$damage] ?? $damage;
      }
    }else {
	    return self::OPPOSITE_DAMAGE[$damage] ?? $damage;
    }
  }
}
