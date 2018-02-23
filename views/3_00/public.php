<?php
    Yii::app()->twigRenderer->renderTemplateFromFile("layout_global.twig", array('oSurvey' => $oSurvey,'aSurveyInfo' => $aSurveyInfo), false);
?>
