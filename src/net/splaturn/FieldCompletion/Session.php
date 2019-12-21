<?php

declare(strict_types=1);

namespace net\splaturn\FieldCompletion;

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
    $this->executor = $player;
    $this->set($player);
  }

  /**
   * 補完を実行
   */
  public function completion() {
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
          $lev->setBlock(new Vector3($nx, $ny, $nz), $blocks[$count--]);
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
    return self::$sessions[$uuid] ?? new Session($player);
  }

  private function set(Player $player) {
    $uuid = $player->getUniqueId()->toBinary();
    self::$sessions[$uuid] = $this;
  }
}