<!DOCTYPE html>
<html lang="<?php echo $language; ?>"<?php echo $dir; ?>>
<head>
<?php
    echo templatereplace(file_get_contents("{$templateDir}/startpage.pstpl"));
    if($useCompletedTemplate)
    {
      echo templatereplace(file_get_contents("{$templateDir}/completed.pstpl"),array('COMPLETED'=>$message,'URL'=>''));
    }
    else
    {
      echo CHtml::tag("div",array('id'=>'wrapper','class'=>'message'),CHtml::tag("p",array("id"=>"tokenmessage"),templatereplace($message)));
    }
    echo templatereplace(file_get_contents("{$templateDir}/endpage.pstpl"));
?>
</body></html>
