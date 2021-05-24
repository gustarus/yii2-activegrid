<?php

namespace gustarus\activegrid\data;

use yii\data\ArrayDataProvider;

class UnlimitedArrayDataProvider extends ArrayDataProvider {

  public function init() {
    parent::init();
    $this->pagination = false;
  }
}
