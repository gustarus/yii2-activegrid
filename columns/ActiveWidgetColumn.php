<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace gustarus\activegrid\columns;

/**
 * Class ActiveWidgetColumn
 * @package gustarus\activegrid\columns
 */
class ActiveWidgetColumn extends ActiveColumn {

  /**
   * Настройки виджета.
   * @var array
   */
  public $widgetConfig;


  /**
   * @inheritdoc
   */
  protected function renderDataCellContentField($field) {
    return $field->widget($this->widgetConfig);
  }
}
