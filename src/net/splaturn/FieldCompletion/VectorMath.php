<?php

declare(strict_types=1);

namespace net\splaturn\FieldCompletion;

use pocketmine\math\Vector3;
use function max;
use function min;

/**
 * Class VectorMath
 * @package net\splaturn\FieldCompletion
 */
class VectorMath {

  /**
   * Returns a new Vector3 taking the maximum of each component in the input vectors.
   *
   * @param Vector3 ...$positions
   *
   * @return Vector3
   */
  public static function maxComponents(Vector3 ...$positions) : Vector3{
    $xList = $yList = $zList = [];
    foreach($positions as $position){
      $xList[] = $position->x;
      $yList[] = $position->y;
      $zList[] = $position->z;
    }
    return new Vector3(max($xList), max($yList), max($zList));
  }

  /**
   * Returns a new Vector3 taking the minimum of each component in the input vectors.
   *
   * @param Vector3 ...$positions
   *
   * @return Vector3
   */
  public static function minComponents(Vector3 ...$positions) : Vector3{
    $xList = $yList = $zList = [];
    foreach($positions as $position){
      $xList[] = $position->x;
      $yList[] = $position->y;
      $zList[] = $position->z;
    }
    return new Vector3(min($xList), min($yList), min($zList));
  }

}