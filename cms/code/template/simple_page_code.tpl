<?
	require_once ($_SERVER['DOCUMENT_ROOT'].'/cms/code/settings.php');
	include(CMS_FILE_LOCAL_PATH_PHP.'functions.php');

	CUtils::RemoveSpecialCharsInGlobalArrays();

	if (('{{TEMPLATE_FILE_NAME}}' == 'product.php' )||('{{TEMPLATE_FILE_NAME}}' == 'product_opt.php' ))
	{
		$nProductId = $_GET['ProductId'];

		$FilePhp = GetProductPageById($nProductId, '{{TEMPLATE_FILE_NAME}}');

        if ($FilePhp)
        {
            //сохраняем и исполняем
            $sTempFilePathName= TEMP_DIRECTORY.session_id()."_product_$nProductId.php";
            file_put_contents($sTempFilePathName, $FilePhp);

            // исполняем
            include($sTempFilePathName);
            // удаляем временный файл
            unlink($sTempFilePathName);
        }
        else
        {
            // товар не найден, перенаправляем на главную страницу.
            header("Location: ".CCMS::$sHttpPrefix."{{DOMAIN_NAME}}/");
        }
	}
	else
	{
		PrintTemplatePageByFileName('{{TEMPLATE_FILE_NAME}}');
	}
?>