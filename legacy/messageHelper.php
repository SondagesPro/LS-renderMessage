<?php
/**
 * messageHelper part of renderMessage Plugin
 *
 * @author Denis Chenu <denis@sondages.pro>
 * @copyright 2017 Denis Chenu <http://www.sondages.pro>
 * @license AGPL v3
 * @version 0.0.2
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

class messageHelper{

  /**
   * @var int the survey id (0 if no survey)
   */
  public $iSurveyId;
  /**
   * @var string the templateName
   */
  public $sTemplate;
  /**
   * @var string the templateName
   */
  public $sLanguage;
  /**
   * @var boolean for version lesser than 3.0 : usage of completed.pstpl
   */
  public $useCompletedTemplate=true;

  /**
   * Contructor
   */
  public function __construct($iSurveyId = null,$sTemplate = null,$sLanguage = null) {
    $oSurvey =null;
    if(is_null($iSurveyId )) {
        /* Find actual survey id */
        $this->iSurveyId=(int) Yii::app()->getConfig('surveyID');
        if(!$this->iSurveyId){
          $this->iSurveyId=(int)Yii::app()->request->getParam('surveyid',Yii::app()->request->getParam('sid'));
        }
        /* Find actual template*/
        $oSurvey=\Survey::model()->findByPk($this->iSurveyId);
        if(!$oSurvey) {
            $this->iSurveyId = null;
        }
    }
    if(is_null($sTemplate)) {
        if($oSurvey){
          $this->sTemplate=$oSurvey->template;
        } else {
          $this->sTemplate=Yii::app()->getConfig('defaulttemplate');
        }
    }
    if(is_null($sTemplate)) {
        $this->sLanguage=Yii::app()->language;
    }
    if(!$this->sLanguage || Yii::app()->language=='en_US'){// @todo : control if in available language (global or survey)
      if($oSurvey && !in_array($this->sLanguage,$oSurvey->getAllLanguages()) ){
        $this->sLanguage=$oSurvey->language;
      } else {
        $this->sLanguage=Yii::app()->getConfig('defaultlang');
      }
    }
    Yii::app()->setLanguage($this->sLanguage);
  }
  /**
   * render the error to be shown
   * @param string $message : message to be shown
   *
   * return @void
   */
  public function render($message,$title=null)
  {
    /* Needed when rendering : we don't send thissurvey */
    Yii::app()->setConfig('surveyID',$this->iSurveyId);
    /* Unsure needed ? For EM ? */
    SetSurveyLanguage($this->iSurveyId, Yii::app()->language);
    $reData=array(
      's_lang'=>Yii::app()->language
    );
    $renderData['message']=templatereplace($message,array(),$reData);
    $lsApiVersion=self::rmLsApiVersion();
    switch($lsApiVersion){
      case '2_06':
        $templateDir=\Template::model()->getTemplatePath($this->sTemplate);
        Yii::app()->controller->layout='bare';
        if (getLanguageRTL(Yii::app()->language)) {
          $renderData['dir'] = ' dir="rtl" ';
        } else {
          $renderData['dir'] = '';
        }
        $renderData['language']=$this->sLanguage;
        $renderData['templateDir']=$templateDir;
        $renderData['useCompletedTemplate']=$this->useCompletedTemplate;
        $renderData['controller']=Yii::app()->getController()->getId();
        Yii::app()->controller->render("renderMessage.views.2_06.public",$renderData);
        Yii::app()->end();
        break;
      case '2_50':
        $oTemplate = \Template::model()->getInstance($this->sTemplate);
        $templateDir= $oTemplate->viewPath;
        Yii::app()->controller->layout='bare';
        if (getLanguageRTL(Yii::app()->language)) {
          $renderData['dir'] = ' dir="rtl" ';
        } else {
          $renderData['dir'] = '';
        }
        $renderData['language']=$this->sLanguage;
        $renderData['templateDir']=$templateDir;
        $renderData['useCompletedTemplate']=$this->useCompletedTemplate;
        Yii::app()->controller->render("renderMessage.views.2_50.public",$renderData);
        Yii::app()->end();
        break;
      default:
        return;
    }

  }

    /**
     * Render some content, just a shortcut in fact
     * @param string html $content
     * @return void
     */
    public static function renderContent($content)
    {
        $renderMessage = new self;
        $renderMessage->render($content);
    }

    /**
     * Render a message inside an alert box
     * @param string $message
     * @param string $type
     * @param string $class extra class
     * @return void
     */
    public static function renderAlert($message,$type='info',$class='')
    {
        switch(self::rmLsApiVersion()){
            case '2_06':
                switch ($type) {
                    case 'info':
                    case 'success':
                        $class="ui-widget-content ui-corner-all";
                        break;
                    case 'warning':
                        $class="ui-state-highlight ui-corner-all";
                        break;
                    case 'danger':
                    case 'warning':
                        $class="ui-state-error ui-corner-all";
                        break;
                }
                $content=Yii::app()->controller->renderPartial("renderMessage.views.2_06.alert",array('class' => $class,'message' =>$message),1);
                $renderMessage = new self;
                $renderMessage->render($content);
                break;
            case '2_50':
            default:
                if(!empty($class)) {
                    $type .= " ".$class;
                }
                $content=Yii::app()->controller->renderPartial("renderMessage.views.2_50.alert",array('type' => $type,'message' =>$message),1);
                $renderMessage = new self;
                $renderMessage->render($content);
        }
    }

  /**
   * return ls api version needed for helper
   * @return string (0.0|2.6|2.50|3.0)
   **/
  public static function rmLsApiVersion(){
      $lsVersion=App()->getConfig("versionnumber");
      if(version_compare($lsVersion,'2.50',"<")) {
        return "2_06";
      }
      if(version_compare($lsVersion,'2.50',">=") && version_compare($lsVersion,'3',"<" ) ) {
        return "2_50";
      }
      Yii::log("Invalid API version : $lsVersion",'error','application.plugins.renderMessage');
      return "0_0";
  }
}
