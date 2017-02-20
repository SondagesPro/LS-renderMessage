<?php
/**
 * Plugin helper for limesurvey : quick render a message to public user
 *
 * @author Denis Chenu <denis@sondages.pro>
 * @copyright 2017 Denis Chenu <http://www.sondages.pro>
 * @license AGPL v3
 * @version 0.0.1
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */
class renderMessage extends \ls\pluginmanager\PluginBase {

  static protected $description = 'An helper for other plugins : render any message to public using the good template.';
  static protected $name = 'renderMessage';

  public function init()
  {
    $this->subscribe('afterPluginLoad');
    $this->subscribe('beforeCloseHtml');

  }

  /**
   * Set the alias to get the file
   */
  public function afterPluginLoad()
  {
    Yii::setPathOfAlias('renderMessage', dirname(__FILE__));
  }

  /**
   * Set the alias to get the file
   */
  public function beforeCloseHtml()
  {
    $renderFlasMessage = \renderMessage\flashMessageHelper::getInstance();
    $renderFlasMessage->renderFlashMessage();
  }
}
