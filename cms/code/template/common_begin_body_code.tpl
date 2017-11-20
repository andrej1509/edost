<?

    if (file_exists(TEMPLATE_COUNTERS_FILE))
	{
			$FileData= file(TEMPLATE_COUNTERS_FILE);
            echo implode($FileData, '');
    }

?>