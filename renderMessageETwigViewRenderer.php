<?php
/**
 * Twig view renderer, plugin overload
 *
 * @inheritdoc
 * Currently is more a copy/paste of original file, just replace function to use own private $_twig var and private function
 * Only function updated are addRecursiveTemplatesPath for the new event
 *
 * @author Denis Chenu <contact@sondages.pro>
 *
 * @version 0.1.0
 * @see LSETwigViewRenderer version  1.1.15
 */
class renderMessageETwigViewRenderer extends LSETwigViewRenderer
{
    /**
     * The twig render
     */
    private $_twig;


    /**
     * @inheritdoc
     */
    public function renderQuestion($sView, $aData)
    {
        $this->_twig  = parent::getTwig(); // Twig object
        $loader       = $this->_twig->getLoader(); // Twig Template loader
        $requiredView = Yii::getPathOfAlias('application.views').$sView; // By default, the required view is the core view
        $loader->setPaths(App()->getBasePath().'/views/'); // Core views path

        $oQuestionTemplate   = QuestionTemplate::getInstance(); // Question template instance has been created at top of qanda_helper::retrieveAnswers()
        $sTemplateFolderName = $oQuestionTemplate->getQuestionTemplateFolderName(); // Get the name of the folder for that question type.

        // Check if question use a custom template and that it provides its own twig view
        if ($sTemplateFolderName) {
            $bTemplateHasThisView = $oQuestionTemplate->checkIfTemplateHasView($sView); // A template can change only one of the view of the question type. So other views should be rendered by core.

            if ($bTemplateHasThisView) {
                $sQTemplatePath = $oQuestionTemplate->getTemplatePath(); // Question template views path
                $loader->setPaths($sQTemplatePath); // Loader path
                $requiredView = $sQTemplatePath.ltrim($sView, '/'); // Complete path of the view
            }
        }

        // We check if the file is a twig file or a php file
        // This allow us to twig the view one by one, from PHP to twig.
        // The check will be removed when 100% of the views will have been twig
        if (file_exists($requiredView.'.twig')) {
            // We're not using the Yii Theming system, so we don't use parent::renderFile
            // current controller properties will be accessible as {{ this.property }}
            $aData['this'] = Yii::app()->getController();
            $aData['question_template_attribute'] = $oQuestionTemplate->getCustomAttributes();
            $template = $this->_twig->loadTemplate($sView.'.twig')->render($aData);
            return $template;
        } else {
            return Yii::app()->getController()->renderPartial($sView, $aData, true);
        }
    }


    /**
     * @inheritdoc
     */
    public function renderOptionPage($oTemplate, $renderArray = array())
    {
        $oRTemplate = $oTemplate;
        $sOptionFile = 'options/options.twig';
        $sOptionJS   = 'options/options.js';
        $sOptionsPath = $oRTemplate->sTemplateurl.'options';
        
        // We get the options twig file from the right template (local or mother template)
        while (!file_exists($oRTemplate->path.$sOptionFile)) {
            
            $oMotherTemplate = $oRTemplate->oMotherTemplate;
            if (!($oMotherTemplate instanceof TemplateConfiguration)) {
                return sprintf(gT('%s not found!', $oRTemplate->path.$sOptionFile));
                break;
            }
            $oRTemplate = $oMotherTemplate;
            $sOptionsPath = $oRTemplate->sTemplateurl.'options';
        }

        if (file_exists($oRTemplate->path.$sOptionJS)) {
            Yii::app()->getClientScript()->registerScriptFile($oRTemplate->sTemplateurl.$sOptionJS, LSYii_ClientScript::POS_BEGIN);
        }

        $this->_twig = $twig = parent::getTwig();
        $this->addRecursiveTemplatesPath($oRTemplate);
        $renderArray['optionsPath'] = $sOptionsPath;
        // Twig rendering
        $line         = file_get_contents($oRTemplate->path.$sOptionFile);
        $oTwigTemplate = $twig->createTemplate($line);
        $sHtml        = $oTwigTemplate->render($renderArray, false);

        return $sHtml;
    }

    /**
     * @inheritdoc
     */
    public function convertTwigToHtml($sString, $aDatas, $oTemplate)
    {
        // Twig init
        $this->_twig = $twig = parent::getTwig();

        // Get the additional infos for the view, such as language, direction, etc
        $aDatas = $this->getAdditionalInfos($aDatas, $oTemplate);

        // Add to the loader the path of the template and its parents.
        $this->addRecursiveTemplatesPath($oTemplate);

        // Plugin for blocks replacement
        list($sString, $aDatas) = $this->getPluginsData($sString, $aDatas);

        // Twig rendering
        $oTwigTemplate = $twig->createTemplate($sString);
        $sHtml         = $oTwigTemplate->render($aDatas, false);

        return $sHtml;
    }


    /**
     * @inheritdoc
     * With adding getPluginTwigPath event
     */
    private function addRecursiveTemplatesPath($oTemplate)
    {
        $oRTemplate   = $oTemplate;
        $loader       = $this->_twig->getLoader();
        $oEvent = new PluginEvent('getPluginTwigPath');
        App()->getPluginManager()->dispatchEvent($oEvent);
        
        $configTwigExtendsOption = (array) $oEvent->get("add");
        $configTwigExtendsForce = (array)$oEvent->get("replace");
        foreach($configTwigExtendsForce as $configTwigExtendForce) {
            if(is_string($configTwigExtendForce) && trim($configTwigExtendForce) != "") {
                $loader->addPath($configTwigExtendForce);
            }
        }
        $loader->addPath($oRTemplate->viewPath);
        while ($oRTemplate->oMotherTemplate instanceof TemplateConfiguration) {
            $oRTemplate = $oRTemplate->oMotherTemplate;
            $loader->addPath($oRTemplate->viewPath);
        }
        foreach($configTwigExtendsOption as $configTwigExtendOption) {
            if(is_string($configTwigExtendOption) && trim($configTwigExtendOption) != "") {
                $loader->addPath($configTwigExtendOption);
            }
        }
    }

    /**
     * @inheritdoc
     */
    private function getPluginsData($sString, $aDatas)
    {
        $event = new PluginEvent('beforeTwigRenderTemplate');

        if (!empty($aDatas['aSurveyInfo']['sid'])) {
            $surveyid = $aDatas['aSurveyInfo']['sid'];
            $event->set('surveyId', $aDatas['aSurveyInfo']['sid']);

            if (isset($_SESSION['survey_'.$surveyid]['srid']) && $aDatas['aSurveyInfo']['active']=='Y') {
                $isCompleted = SurveyDynamic::model($surveyid)->isCompleted($_SESSION['survey_'.$surveyid]['srid']);
            } else {
                $isCompleted = false;
            }

            $aDatas['aSurveyInfo']['bShowClearAll'] = !$isCompleted;
        }

        App()->getPluginManager()->dispatchEvent($event);
        $aPluginContent = $event->getAllContent();
        if (!empty($aPluginContent['sTwigBlocks'])) {
            $sString = $sString.$aPluginContent['sTwigBlocks'];
        }

        return array($sString, $aDatas);
    }

    /**
     * @inheritdoc
     */
    private function getAdditionalInfos($aDatas, $oTemplate)
    {
        // We retreive the definition of the core class and attributes (in the future, should be template dependant done via XML file)
        $aDatas["aSurveyInfo"] = array_merge($aDatas["aSurveyInfo"], $oTemplate->getClassAndAttributes());

        $languagecode = Yii::app()->getConfig('defaultlang');
        if (!empty($aDatas['aSurveyInfo']['sid'])) {
            if (Yii::app()->session['survey_'.$aDatas['aSurveyInfo']['sid']]['s_lang']) {
                $languagecode = Yii::app()->session['survey_'.$aDatas['aSurveyInfo']['sid']]['s_lang'];
            } elseif ($aDatas['aSurveyInfo']['sid'] && Survey::model()->findByPk($aDatas['aSurveyInfo']['sid'])) {
                $languagecode = Survey::model()->findByPk($aDatas['aSurveyInfo']['sid'])->language;
            }
        }

        $aDatas["aSurveyInfo"]['languagecode']     = $languagecode;
        $aDatas["aSurveyInfo"]['dir']              = (getLanguageRTL($languagecode)) ? "rtl" : "ltr";

        if (!empty($aDatas['aSurveyInfo']['sid'])) {
            $showxquestions                            = Yii::app()->getConfig('showxquestions');
            $aDatas["aSurveyInfo"]['bShowxquestions']  = ($showxquestions == 'show' || ($showxquestions == 'choose' && !isset($aDatas['aSurveyInfo']['showxquestions'])) || ($showxquestions == 'choose' && $aDatas['aSurveyInfo']['showxquestions'] == 'Y'));


            // NB: Session is flushed at submit, so sid is not defined here.
            if (isset($_SESSION['survey_'.$aDatas['aSurveyInfo']['sid']]) && isset($_SESSION['survey_'.$aDatas['aSurveyInfo']['sid']]['totalquestions'])) {
                $aDatas["aSurveyInfo"]['iTotalquestions'] = $_SESSION['survey_'.$aDatas['aSurveyInfo']['sid']]['totalquestions'];
            }
        }


        // Add the template options
        if ($oTemplate->oOptions) {
            foreach ($oTemplate->oOptions as $key => $value) {
                $aDatas["aSurveyInfo"]["options"][$key] = (string) $value;
            }
        }
        return $aDatas;
    }

    public function renderPartial($twigView,$aData)
    {
        return $this->_twig->loadTemplate($twigView.'.twig')->render($aData);
    }
}
