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
   * @var boolean for version lesser than 3.0 : usage of completed.pstpl
   */
  public $useCompletedTemplate=true;

  /**
   * Contructor
   */
  public function __construct() {
    /* Find actual survey id */
    $this->iSurveyId=(int) Yii::app()->getConfig('surveyID');
    if(!$this->iSurveyId){
      $this->iSurveyId=(int)Yii::app()->request->getParam('surveyid',Yii::app()->request->getParam('sid'));
    }
    /* Find actual template*/
    $oSurvey=\Survey::model()->findByPk($this->iSurveyId);
    if($oSurvey){
      $this->sTemplate=$oSurvey->template;
    } else {
      $this->sTemplate=Yii::app()->getConfig('defaulttemplate');
      $this->iSurveyId = null;
    }
    /* Find actual language*/
    $this->sLanguage=Yii::app()->language;
    if(!$this->sLanguage || Yii::app()->language=='en_US'){// @todo : control if in available language (global or survey)
      if($oSurvey){
        $this->sLanguage=$oSurvey->language;
      } else {
        $this->sLanguage=Yii::app()->getConfig('defaultlang');
      }
    }
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
      case '3_00':
        if(!$title) {
          $title = Yii::app()->getConfig('sitename');
        }
        $oTemplate = \Template::model()->getInstance($this->sTemplate);
        $aSurveyInfo = array(
            'languagecode' => $this->sLanguage,
            'dir' => getLanguageRTL(Yii::app()->language) ? 'rtl' : 'ltr',
            'surveyls_title' => $title,
            'adminemail' => Yii::app()->getConfig("siteadminemail"),
            'adminname' => Yii::app()->getConfig("siteadminname"),
        );
        if($this->iSurveyId) {
          $aSurveyInfo = getSurveyInfo($iSurveyId, App()->getLanguage());
          $oSurvey=\Survey::model()->findByPk($this->iSurveyId);
        } else {
          $oSurvey = new \stdClass();
        }
        $oSurvey->active = "Y";
        //~ $aSurveyInfo['surveyls_title'] = $title;
        $aSurveyInfo['aCompleted']['showDefault'] =false;
        $aSurveyInfo['aCompleted']['sEndText'] =$message;
        $aSurveyInfo['aAssessments']['show'] =false;
        $aSurveyInfo['aCompleted']['aPrintAnswers']['show'] =false;
        $aSurveyInfo['aCompleted']['aPublicStatistics']['show'] =false;
        $aSurveyInfo['aCompleted']['aPublicStatistics']['sSurveylsUrl'] =null;
        $aSurveyInfo['include_content'] = 'submit';
        $aSurveyInfo['active'] = 'Y';
        $renderData = array_merge($renderData, array(
          'oTemplate'         => $oTemplate,
          'sSiteName'         => Yii::app()->getConfig('sitename'),
          'sSiteAdminName'    => Yii::app()->getConfig("siteadminname"),
          'sSiteAdminEmail'   => Yii::app()->getConfig("siteadminemail"),
          'aSurveyInfo'       => $aSurveyInfo,
          'oSurvey'       => $oSurvey,
        ));
        Yii::app()->controller->render("renderMessage.views.3_00.public",$renderData);
        Yii::app()->end();
      default:
        return;
    }

  }

  /**
   * return ls api version needed for helper
   * @return string (0.0|2.6|2.50|3.0)
   **/
  public static function rmLsApiVersion(){
      $lsVersion=App()->getConfig("versionnumber");
      $aVersion=explode(".",$lsVersion);
      $aVersion=array_replace([0,0,0],$aVersion);
      if($aVersion[0]==2 && $aVersion[1]<=6){
        return "2_06";
      }elseif($aVersion[0]==2){
        return "2_50";
      }elseif($aVersion[0]==3){
        return "3_00";
      }
      Yii::log("Unknow API version : $lsVersion",'error','application.plugins.renderMessage');
      return "0_0";
  }
}
