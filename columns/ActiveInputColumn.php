<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace gustarus\activegrid\columns;

use gustarus\activegrid\widgets\ActiveGrid;
use gustarus\activegrid\columns\ActiveColumn;
use yii\helpers\Html;

/**
 * Class ActiveInputColumn
 * @package gustarus\activegrid\columns
 */
class ActiveInputColumn extends ActiveColumn {

  /**
   * Тип инпута.
   * @var string
   */
  public $fieldType = 'text';


  /**
   * @inheritdoc
   */
  protected function renderDataCellContentField($field, $fieldOptions, $key, $index) {
    return $field->input($this->fieldType, $fieldOptions);
  }
}
