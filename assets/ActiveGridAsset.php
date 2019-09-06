<?php
/**
 * Created by PhpStorm.
 * User: supreme
 * Date: 03.05.14
 * Time: 20:56
 */

namespace gustarus\activegrid\assets;

use yii\web\AssetBundle;

class ActiveGridAsset extends AssetBundle {

  /**
   * @inheritdoc
   */
  public $sourcePath = '@gustarus/activegrid/public';

  /**
   * @inheritdoc
   */
  public $js = [
    'js/ActiveGrid.js',
  ];

  /**
   * @inheritdoc
   */
  public $css = [
    'css/grid.css',
  ];

  /**
   * @inheritdoc
   */
  public $depends = [
    'yii\web\JqueryAsset',
  ];
} 
