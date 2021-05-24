<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace gustarus\activegrid\columns;

use gustarus\activegrid\columns\ActiveColumn;
use yii\helpers\Html;

/**
 * Class ActiveSelectColumn
 * @package gustarus\activegrid\columns
 */
class ActiveSelectColumn extends ActiveColumn {

  /**
   * Данные списка.
   * @var array
   */
  public $fieldData = [];

  /**
   * @inheritdoc
   */
  public $fieldOptions = [
    'class' => 'form-control'
  ];


  /**
   * @inheritdoc
   */
  protected function renderDataCellContentField($field, $fieldOptions, $key, $index) {
    return $field->dropDownList($this->fieldData, $fieldOptions);
  }
}
