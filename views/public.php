<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $language; ?>" lang="<?php echo $language; ?>"<?php echo $dir; ?>>
<head>
<?php
    echo templatereplace(file_get_contents("{$templateDir}/startpage.pstpl"));
    if($useCompletedTemplate)
    {
      echo templatereplace(file_get_contents("{$templateDir}/completed.pstpl"),array('COMPLETED'=>$message,'URL'=>''));
    }
    else
    {
      echo CHtml::tag("div",array('id'=>'wrapper','class'=>'message'),templatereplace($message));
    }
    echo templatereplace(file_get_contents("{$templateDir}/endpage.pstpl"));
?>
</body></html>
