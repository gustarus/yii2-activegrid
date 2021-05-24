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
 * Class ActiveCheckboxColumn
 * @package gustarus\activegrid\columns
 */
class ActiveCheckboxColumn extends ActiveColumn {

  public $enclosedByLabel = true;

  /**
   * @inheritdoc
   */
  protected function renderDataCellContentField($field, $fieldOptions, $key, $index) {
    return $field->checkbox($fieldOptions, $this->enclosedByLabel);
  }
}
