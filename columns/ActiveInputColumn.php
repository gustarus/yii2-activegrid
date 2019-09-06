<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace gustarus\activegrid\columns;

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
  protected function renderDataCellContentField($field) {
    return $field->input($this->fieldType, $this->fieldOptions);
  }
}
