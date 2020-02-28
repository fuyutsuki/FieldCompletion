<?php

declare(strict_types=1);

namespace net\splaturn\FieldCompletion;

use pocketmine\block\BlockIds;
use pocketmine\block\EndRod;
use pocketmine\block\IronTrapdoor;
use pocketmine\block\Stair;
use pocketmine\block\StandingBanner;
use pocketmine\block\Torch;
use pocketmine\block\Trapdoor;
use pocketmine\block\WallBanner;
use pocketmine\math\Vector2;
use pocketmine\math\Vector3;
use pocketmine\Player;

/**
 * Class Session
 * @package net\splaturn\FieldCompletion
 */
class Session {

  /** @var Session[] */
  private static $sessions = [];

  /** @var Player 実行者 */
  private $executor;
  /** @var Vector2 回転軸 */
  private $rotationAxis;
  /** @var Vector3 始点 */
  private $start;
  /** @var Vector3 終点 */
  private $end;

  public function __construct(Player $player) {
    $this->set($player);
  }

  /**
   * 点対称に補完を実行
   */
  public function pointCompletion() {
    $rotationAxis = $this->getRotationAxis();

    $start = $this->getStart();
    $vec2Start = new Vector2($start->getX(), $start->getZ());
    $newStart = $vec2Start->multiply(-1)->add($rotationAxis->multiply(2));
    $targetStart = new Vector3($newStart->getX(), $start->getY(), $newStart->getY());

    $end = $this->getEnd();
    $vec2End = new Vector2($end->getX(), $end->getZ());
    $newEnd = $vec2End->multiply(-1)->add($rotationAxis->multiply(2));
    $targetEnd = new Vector3($newEnd->getX(), $end->getY(), $newEnd->getY());

    $lev = $this->executor->getLevel();

    $minO = VectorMath::minComponents($start, $end);
    $maxO = VectorMath::maxComponents($start, $end);
    $minN = VectorMath::minComponents($targetStart, $targetEnd);
    $maxN = VectorMath::maxComponents($targetStart, $targetEnd);

    $blocks = [];
    for ($x = $minO->getX(); $x <= $maxO->getX(); $x++) {
      for ($y = $minO->getY(); $y <= $maxO->getY(); $y++) {
        for ($z = $minO->getZ(); $z <= $maxO->getZ(); $z++) {
          $blocks[] = $lev->getBlockAt($x, $y, $z);
        }
      }
    }

    $this->executor->sendMessage("Copied!");

    $count = count($blocks) - 1;
    for ($nx = $minN->getX(); $nx <= $maxN->getX(); $nx++) {
      for ($ny = $maxN->getY(); $ny >= $minN->getY(); $ny--) {
        for ($nz = $minN->getZ(); $nz <= $maxN->getZ(); $nz++) {
          $block = $blocks[$count--];
          if ($block instanceof Stair || $block instanceof Trapdoor || $block instanceof IronTrapdoor || $block instanceof Torch || $block instanceof WallBanner || $block instanceof StandingBanner || $block instanceof EndRod) {
            $block->setDamage(Core::getOppositeSideDamage($block->getDamage()));
          }
          $lev->setBlock(new Vector3($nx, $ny, $nz), $block);
        }
      }
    }
    $this->executor->sendMessage("Done!");
  }

  /**
   * 線対称に補完を実行
   */
  public function lineCompletion() {
    $rotationAxis = $this->getRotationAxis();
    $start = $this->getStart();
    $vec2Start = new Vector2($start->getX(), $start->getZ());
    $end = $this->getEnd();
    $vec2End = new Vector2($end->getX(), $end->getZ());

    $diffStart = $vec2Start->subtract($rotationAxis);
    $diffEnd = $vec2End->subtract($rotationAxis);

    $minX = min($diffStart->x, $diffEnd->x);
    $minZ = min($diffStart->y, $diffEnd->y);

    if ($minX > $minZ) {
      $newStart = $diffStart->y * -1 + $rotationAxis->y;
      $targetStart = new Vector3($start->x, $start->y, $newStart);

      $newEnd = $diffEnd->y * -1 + $rotationAxis->y;
      $targetEnd = new Vector3($end->x, $end->y, $newEnd);
    }else {
      $newStart = $diffStart->x * -1 + $rotationAxis->x;
      $targetStart = new Vector3($newStart, $start->y, $start->z);

      $newEnd = $diffEnd->x * -1 + $rotationAxis->x;
      $targetEnd = new Vector3($newEnd, $end->y, $end->z);
    }

    $lev = $this->executor->getLevel();

    $minO = VectorMath::minComponents($start, $end);
    $maxO = VectorMath::maxComponents($start, $end);
    $minN = VectorMath::minComponents($targetStart, $targetEnd);
    $maxN = VectorMath::maxComponents($targetStart, $targetEnd);

    $blocks = [];
    for ($x = $minO->getX(); $x <= $maxO->getX(); $x++) {
      for ($y = $minO->getY(); $y <= $maxO->getY(); $y++) {
        for ($z = $minO->getZ(); $z <= $maxO->getZ(); $z++) {
          $blocks[] = $lev->getBlockAt($x, $y, $z);
        }
      }
    }

    $this->executor->sendMessage("Copied!");

    $count = count($blocks) - 1;
    if ($minX > $minZ) {
      for ($nx = $maxN->getX(); $nx >= $minN->getX(); $nx--) {
        for ($ny = $maxN->getY(); $ny >= $minN->getY(); $ny--) {
          for ($nz = $minN->getZ(); $nz <= $maxN->getZ(); $nz++) {
            $block = $blocks[$count--];
            if ($block instanceof Stair || $block instanceof Trapdoor || $block instanceof IronTrapdoor || $block instanceof Torch || $block instanceof WallBanner || $block instanceof StandingBanner || $block instanceof EndRod) {
              $block->setDamage(Core::getOppositeSideDamage($block->getDamage(), true));
            }
            $lev->setBlock(new Vector3($nx, $ny, $nz), $block);
          }
        }
      }
    }else {
      for ($nx = $minN->getX(); $nx <= $maxN->getX(); $nx++) {
        for ($ny = $maxN->getY(); $ny >= $minN->getY(); $ny--) {
          for ($nz = $maxN->getZ(); $nz >= $minN->getZ(); $nz--) {
            $block = $blocks[$count--];
            if ($block instanceof Stair || $block instanceof Trapdoor || $block instanceof IronTrapdoor || $block instanceof Torch || $block instanceof WallBanner || $block instanceof StandingBanner || $block instanceof EndRod) {
              $block->setDamage(Core::getOppositeSideDamage($block->getDamage(), false));
            }
            $lev->setBlock(new Vector3($nx, $ny, $nz), $block);
          }
        }
      }
    }
    $this->executor->sendMessage("Done!");
  }

  public function getRotationAxis(): ?Vector2 {
    return $this->rotationAxis ?? null;
  }

  public function setRotationAxis(Vector2 $rotationAxis) {
    $this->rotationAxis = $rotationAxis;
  }

  public function getStart(): ?Vector3 {
    return $this->start ?? null;
  }

  public function setStart(Vector3 $start) {
    $this->start = $start;
  }

  public function getEnd(): ?Vector3 {
    return $this->end ?? null;
  }

  public function setEnd(Vector3 $end) {
    $this->end = $end;
  }

  public function checkParameters(): bool {
    return $this->getRotationAxis() !== null && $this->getStart() !== null && $this->getEnd() !== null;
  }

  public static function get(Player $player): Session {
    $uuid = $player->getUniqueId()->toBinary();
    if (isset(self::$sessions[$uuid])) {
      $session = self::$sessions[$uuid];
      $session->set($player);
      return $session;
    }
    return new Session($player);
  }

  private function set(Player $player) {
    $this->executor = $player;
    $uuid = $player->getUniqueId()->toBinary();
    self::$sessions[$uuid] = $this;
  }
}