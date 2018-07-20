<?php
/**
 * Plugin helper for limesurvey : quick render a message to public user
 *
 * @author Denis Chenu <denis@sondages.pro>
 * @copyright 2017-2018 Denis Chenu <http://www.sondages.pro>
 * @license AGPL v3
 * @version 1.3.0
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
class renderMessage extends PluginBase {

    static protected $description = 'An helper for other plugins : render any message to public using the good template.';
    static protected $name = 'renderMessage';

    public function init()
    {
        $oPlugin = Plugin::model()->find("name = :name",array("name"=>get_class($this)));
        if($oPlugin && $oPlugin->active) {
          $this->_setConfig();
        }
        $this->subscribe('getPluginTwigPath');
        $this->subscribe('beforeTwigRenderTemplate');
    }

    /**
     * Add flash width javascript , currently done only via javascript …
     */
    public function beforeTwigRenderTemplate()
    {
        $iSurveyId = $this->getEvent()->get('surveyId');
        /* Add flash message */
        $flashMessageHelper = \renderMessage\flashMessageHelper::getInstance();
        $flashMessageHelper->renderFlashMessage();
    }


    /**
     * Add some views for this and other plugin
     */
    public function getPluginTwigPath()
    {
        $viewPath = dirname(__FILE__)."/views";
        $this->getEvent()->append('add', array($viewPath));
    }
    /**
    * Set the alias to get the file and replace twig by own Class
    */
    public function _setConfig()
    {
        if(version_compare(Yii::app()->getConfig('versionnumber'),"3","<=")) {
            Yii::setPathOfAlias('renderMessage', dirname(__FILE__).DIRECTORY_SEPARATOR."legacy");
            return;
        }
        Yii::setPathOfAlias('renderMessage', dirname(__FILE__));
        if(version_compare(Yii::app()->getConfig('versionnumber'),"3.10",">=") ) { /* @see https://github.com/LimeSurvey/LimeSurvey/pull/1078 */
            return;
        }
        $lsConfig = require(APPPATH . 'config/internal' . EXT);
        $twigRenderer = $lsConfig['components']['twigRenderer'];
        Yii::import('renderMessage.renderMessageETwigViewRenderer',true);
        $twigRenderer['class'] = 'renderMessage.renderMessageETwigViewRenderer';
        Yii::app()->setComponent('twigRenderer',$twigRenderer);
    }

}
