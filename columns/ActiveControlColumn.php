<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace gustarus\activegrid\columns;

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use gustarus\activegrid\widgets\ActiveGrid;

/**
 * Class ActiveControlColumn
 * @package gustarus\activegrid\columns
 */
class ActiveControlColumn extends ActiveColumn {

  /**
   * 'default' => [
   *    'label' => '<i class="fas fa-comment"></i>&nbsp;Hello!',
   *    'options' => [],
   * ]
   * @var array
   */
  public $controls = [];

  /**
   * @inheritdoc
   */
  public function renderDataCellContent($model, $key, $index) {
    $controls = [];
    foreach ($this->controls as $control) {
      $controls[] = Html::button($control['label'], $control['options']);
    }

    return join('', $controls);
  }
}
