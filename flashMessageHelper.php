<?php
/**
 * messageHelper part of renderMessage Plugin
 *
 * @author Denis Chenu <denis@sondages.pro>
 * @copyright 2017 Denis Chenu <http://www.sondages.pro>
 * @license AGPL v3
 * @version 1.0.0
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
namespace renderMessage;
use Yii;
use CClientScript;

class flashMessageHelper{

  /**
   * @var Singleton
   * @access private
   */
  private static $_instance = null;
  /**
   * @var array[]
   * @access private
   */
  private $messages = array();
  /**
  * Construct
  * @param void
  * @return void
  */
  private function init() {

  }
  /* Init
  * @param void
  * @return Singleton
  */
  public static function getInstance() {
     if(is_null(self::$_instance)) {
       self::$_instance = new flashMessageHelper();
     }
     return self::$_instance;
  }
  /**
   * add a flash message to existing flash
   * @param string $message : message to be added
   * @param string $type : (default|success|warning|error)
   * @param string $extraclass 
   * return @void
   */
  public function addFlashMessage($message,$type='info',$extraclass=''){
    $this->messages[]=array(
        'message'=>$message,
        'type'=>$type,
        'extraclass'=>$extraclass,
    );
  }

  /**
   * render the existing public message at this time
   */
  public function renderFlashMessage(){
    if(empty($this->messages)){
        return;
    }
    $renderData= array(
        'renderMessage'=> array(
            'messages'=>$this->messages,
        ),
    );
    $controller = Yii::app()->getController();
    $htmlMessage = Yii::app()->twigRenderer->renderPartial('./subviews/messages/flash_messages', $renderData);
    if(!empty($htmlMessage)) {
        $this->_addAndRegisterPackage();
    }
    return $htmlMessage;
  }

    /**
     * Create package if not exist, register it
     */
    private function _addAndRegisterPackage()
    {
        /* Quit if is done */
        if(array_key_exists('renderFlashMessage',Yii::app()->getClientScript()->packages)) {
            return;
        }
        /* Add package if not exist (allow to use another one in config) */
        if(!Yii::app()->clientScript->hasPackage('renderFlashMessage')) {
            Yii::setPathOfAlias('renderFlashMessage',dirname(__FILE__));
            Yii::app()->clientScript->addPackage('renderFlashMessage', array(
                'basePath'    => 'renderMessage.assets',
                'css'         => array('renderFlashMessage.css'),
                'js'          => array('renderFlashMessage.js'),
                'depends'      =>array('limesurvey-public','template-core'),
            ));
        }
        /* Registering the package */
        Yii::app()->getClientScript()->registerPackage('renderFlashMessage');
    }
}
