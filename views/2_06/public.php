<?php
    if($useCompletedTemplate)
    {
        echo templatereplace(file_get_contents("{$templateDir}/completed.pstpl"),array('COMPLETED'=>$message,'URL'=>''));
    }
    else
    {
        echo CHtml::tag("div",array('id'=>'wrapper','class'=>'message'),CHtml::tag("p",array("id"=>"tokenmessage"),templatereplace($message)));
    }
?>
