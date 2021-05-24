<?php

namespace gustarus\activegrid;

use yii\helpers\ArrayHelper;

class ActiveGridHelper {

  public static function sortRelations($relations, $attributes) {
    $existed = [];
    $new = [];
    foreach ($relations as $relation) {
      if ($relation->id) {
        $existed[] = $relation;
      } else {
        $new[] = $relation;
      }
    }

    ArrayHelper::multisort($existed, $attributes);

    return array_merge($existed, $new);
  }
}
