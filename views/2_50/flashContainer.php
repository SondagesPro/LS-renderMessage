<?php
  App()->clientScript->registerCssFile($assetUrl.'/2.50/renderMessage.css');
?><div class="rm-flash-container">
  <?php foreach($messages as $message){
    echo Yii::app()->controller->renderPartial("renderMessage.views.2_50.flashMessage",$message);
  }?>
</div>
