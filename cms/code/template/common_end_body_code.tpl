<?
    if (file_exists(TEMPLATE_DIRECTORY.'other/end_body_code.html'))
	{
	    $FileData= file(TEMPLATE_DIRECTORY.'other/end_body_code.html');
            echo implode($FileData, '');
    }

?>