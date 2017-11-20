<?php
$lead_id = $_POST['idlead'];
include_once($_SERVER['DOCUMENT_ROOT'] . '/cms/code/settings.php');
include_once(CMS_FILE_LOCAL_PATH_PHP . 'functions.php');
include_once(PLUGINS_CODE_DIRECTORY . '/delivery_service/main.php');

CUtils::RemoveSpecialCharsInGlobalArrays();
$pObj= new CDeliveryService();
$sItems = $pObj->OnButtonLoadItems($lead_id);
$sWeight = 0;
foreach ($sItems as $sItem){
    $sProps = $pObj->OnButtonLoadProps($sItem["PRODUCT_ID"]);
    $sWeight =  $sProps[CCMS::$SETS[SETTINGS_SECTION_BITRIX24][SETTINGS_BITRIX24_WEIGHT]]["value"]+$sWeight;
};
echo $sWeight;
?>