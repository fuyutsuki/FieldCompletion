<?php

declare(strict_types=1);

namespace net\splaturn\FieldCompletion;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\ItemIds;
use pocketmine\math\Vector2;

/**
 * Class EventListener
 * @package net\splaturn\FieldCompletion
 */
class EventListener implements Listener {

  /** @var Core */
  private $core;

  public function __construct(Core $core) {
    $this->core = $core;
  }

  public function onBreakBlock(BlockBreakEvent $ev) {
    $p = $ev->getPlayer();
    if ($p->getInventory()->getItemInHand()->getId() === ItemIds::NETHER_QUARTZ_ORE && !$p->isSneaking()) {
      Session::get($p)->setStart($ev->getBlock());
      $p->sendMessage("{$this->core->getPrefix()} 始点を設定しました");
      $ev->setCancelled();
    }
  }

  public function onPlaceBlock(BlockPlaceEvent $ev) {
    $p = $ev->getPlayer();
    if ($p->getInventory()->getItemInHand()->getId() === ItemIds::NETHER_QUARTZ_ORE && !$p->isSneaking()) {
      Session::get($p)->setEnd($ev->getBlock());
      $p->sendMessage("{$this->core->getPrefix()} 終点を設定しました");
      $ev->setCancelled();
    }
  }

  public function onInteractBlock(PlayerInteractEvent $ev) {
    $p = $ev->getPlayer();
    if ($p->getInventory()->getItemInHand()->getId() === ItemIds::NETHER_QUARTZ_ORE) {
      $a = $ev->getAction();
      if ($a === PlayerInteractEvent::RIGHT_CLICK_BLOCK && $p->isSneaking()) {
        $b = $ev->getBlock();
        Session::get($p)->setRotationAxis(new Vector2($b->getX(), $b->getZ()));
        $p->sendMessage("{$this->core->getPrefix()} 回転軸を設定しました");
        $ev->setCancelled();
      }
    }
  }

}