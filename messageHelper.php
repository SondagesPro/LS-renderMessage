<?php
/**
 * messageHelper part of renderMessage Plugin
 *
 * @author Denis Chenu <denis@sondages.pro>
 * @copyright 2017-2020 Denis Chenu <http://www.sondages.pro>
 * @license AGPL v3
 * @version 1.0.1
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
use Template;
use Survey;
use Yii;

class messageHelper{

    /**
    * render the error to be shown
    * @param string $content : content twig file to be used
    * @param string|null $layout to be used (must be in a views directory, final name start by layout_ (added here)
    * @param string|null $contentview to be used (layout dependent : in /subviews/content/ for layout 'global', 
    * @param array $aData to be merged from default data
    * return @void
    */
    public function render($content,$layout='global',$contentview='html',$aData=array())
    {
        if(!$layout) {
            $layout = 'global';
        }
        /* Try to find current survey */
        $iSurveyid=(int) Yii::app()->getConfig('surveyID');
        if(!$iSurveyid){
          $iSurveyid=(int)Yii::app()->request->getParam('surveyid',Yii::app()->request->getParam('sid'));
        }
        /* language*/
        $language = App()->getLanguage();
        if($iSurveyid) {
            $oSurvey = Survey::model()->findByPk($iSurveyid);
            if(!$oSurvey) {
                $iSurveyid = null;
            }
            if($oSurvey) {
                if(!$language || !in_array($language,$oSurvey->getAllLanguages() ) ) {
                    $language = $oSurvey->language;
                    App()->setLanguage($language);
                }
                $renderData['aSurveyInfo'] = getSurveyInfo($iSurveyid,$language);
                Template::model()->getInstance(null, $iSurveyid);
            }
        }
        if(!$iSurveyid) {
            $renderData['aSurveyInfo'] = array(
                'surveyls_title' => App()->getConfig('sitename'),
            );
            Template::model()->getInstance(App()->getConfig('defaulttheme'), null);
        }
        $renderData['aSurveyInfo']['active'] = 'Y'; // Didn't show the default warning
        $renderData['aSurveyInfo']['options']['ajaxmode'] = "off"; // Try to disable ajax mode
        $renderData['aSurveyInfo']['include_content'] = $contentview;
        $renderData['renderMessage'] = array(
            'content' => $content,
        );
        $renderData = array_merge_recursive($renderData,$aData);
        Yii::app()->twigRenderer->renderTemplateFromFile('layout_'.$layout.'.twig', $renderData,false);
        Yii::app()->end();
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
        $renderMessage = new self;
        $extraData = array(
            'renderMessage' => array(
                'alert'=> array(
                    'content' => $message,
                    'type' => $type,
                    'extraclass' => $class,
                ),
            ),
        );
        $renderMessage->render($message,'global','alert',$extraData);
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
     * Add a flash message to be displayed
     * @param string $message
     * @param string $type
     * @return void
     */
    public static function addFlashMessage($message,$type='info')
    {
        $controller = Yii::app()->getController()->getId();
        if($controller=='admin') {
            Yii::app()->setFlashMessage($message, $class);
            return;
        }
        $renderFlashMessage = \renderMessage\flashMessageHelper::getInstance();
        $renderFlashMessage->addFlashMessage($message,$type);
    }

}
