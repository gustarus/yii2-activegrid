<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace gustarus\activegrid\columns;

use Yii;
use yii\db\ActiveRecord;
use yii\grid\DataColumn;
use yii\widgets\ActiveField;
use gustarus\activegrid\ActiveGrid;

/**
 * Class ActiveInputColumn
 * @package gustarus\activegrid\columns
 */
class ActiveColumn extends DataColumn {

  /**
   * @var ActiveGrid
   */
  public $grid;

  /**
   * @var function
   */
  public $renderField;

  /**
   * @var array
   */
  public $headerOptions = [
    'class' => 'text-center',
  ];

  /**
   * Колбек для отрисовки инпута.
   * @var callable
   */
  public $fieldCallback = false;

  /**
   * Настройки поля.
   * @var
   */
  public $fieldConfig = [
    'template' => "{input}\n{error}",
  ];

  /**
   * Html опции инпута.
   * @var array
   */
  public $fieldOptions = [
    'class' => 'form-control',
  ];

  /**
   * Html опции инпута для шаблона строки.
   * @var array
   */
  public $fieldTemplateOptions = [];


  public function init() {
    parent::init();
    $this->fieldTemplateOptions = array_merge(
      $this->fieldOptions,
      $this->fieldTemplateOptions
    );
  }


  /**
   * @inheritdoc
   */
  protected function renderHeaderCellContent() {
    return $this->header ?: $this->grid->relation->getAttributeLabel($this->attribute);
  }

  /**
   * @inheritdoc
   */
  protected function renderDataCellContent($model, $key, $index) {
    $field = $this->grid->form->field($model, '[' . $index . ']' . $this->attribute, $this->fieldConfig);
    $options = $key === ActiveGrid::TEMPLATE_KEY
      ? $this->fieldTemplateOptions
      : $this->fieldOptions;

    if ($this->renderField) {
      return call_user_func($this->renderField, $this, $field, $model, $options, $key, $index);
    }

    return $this->renderDataCellContentField($field, $options, $key, $index);
  }

  /**
   * Рендерит поле.
   * @param ActiveField $field
   * @param [] $options
   * @param string|number $key
   * @param string|number $index
   * @return string
   */
  protected function renderDataCellContentField($field, $options, $key, $index) {
    Yii::info('You must redefine ' . __METHOD__ . '.');

    return '';
  }
}
