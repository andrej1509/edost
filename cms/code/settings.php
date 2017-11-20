<?php


define ("CMS_FILE_LOCAL_PATH_PHP", $_SERVER['DOCUMENT_ROOT']."/cms/code/");

define ('SETTINGS_INI_FILE_NAME', $_SERVER['DOCUMENT_ROOT']."/cms/settings/settings.ini");

define ('SETTINGS_INI_FILE_PARAM_DEVIDER', ',');
define ('SETTINGS_INI_FILE_PARAM_DEVIDER_TEXT', '@@');

define ('SETTINGS_SECTION_COMMON', 'COMMON');

define ('SETTINGS_SECTION_PROJECT', 'PROJECT');
define ('SETTINGS_PROJECT_USE_ADOBE_MUSE_PROJECT', 'use_adobe_muse_project');
define ('SETTINGS_PROJECT_MUSE_EXPORT_PATH', 'adobe_muse_export_path');

define ('SETTINGS_SECTION_GLOBAL', 'GLOBAL');
define ('SETTINGS_DOMAIN_NAME', 'domain_url');
define ('SETTINGS_COMPANY_NAME', 'company_name');
define ('SETTINGS_MOBILE_SITE_VERSION_ENABLE', 'mobile_site_version_enable');
define ('SETTINGS_USE_HTTPS', 'use_https');

define ('SETTINGS_CATALOG_HEADER_PRODUCT_COUNT_TAG_ID', 'product_count_in_cart_tag_id');

define ('SETTINGS_SECTION_DATABASE', 'DATABASE');
define ('SETTINGS_DATABASE_MYSQL_SERVER', 'mysql_server');
define ('SETTINGS_DATABASE_MYSQL_DATABASE', 'mysql_database');
define ('SETTINGS_DATABASE_MYSQL_DATABASE_LOGIN', 'mysql_database_login');
define ('SETTINGS_DATABASE_MYSQL_DATABASE_PASSWORD', 'mysql_database_password');

define ('SETTINGS_SECTION_SEO', 'SEO');
define ('SETTINGS_USE_PRODUCT_CATEGORY_IN_NAME', 'use_product_category_in_name');

define ('SETTINGS_SECTION_FORM', 'FORM_SETTINGS');
define ('SETTINGS_COMMON_FORM_NAME_INPUT_NAME', 'Name');
define ('SETTINGS_COMMON_FORM_PHONE_INPUT_NAME', 'Phone');
define ('SETTINGS_COMMON_FORM_EMAIL_INPUT_NAME', 'Email');
define ('SETTINGS_COMMON_FORM_COMMENT_INPUT_NAME', 'Comments');
define ('SETTINGS_COMMON_FORM_ADDRESS_INPUT_NAME', 'Address');

define ('SETTINGS_FORM_NAME_INPUT_ALIAS', 'name_input_alias');
define ('SETTINGS_FORM_PHONE_INPUT_ALIAS', 'phone_input_alias');
define ('SETTINGS_FORM_EMAIL_INPUT_ALIAS', 'email_input_alias');
define ('SETTINGS_FORM_COMMENT_INPUT_ALIAS', 'comment_input_alias');
define ('SETTINGS_FORM_ADDRESS_INPUT_ALIAS', 'address_input_alias');

define ('SETTINGS_SECTION_STATISTICS', 'STATISTICS');
define ('SETTINGS_STATISTICS_CREATE_YANDEX_METRIKA_EVENTS', 'create_yandex_metrika_events');
define ('SETTINGS_STATISTICS_YANDEX_METRIKA_COUNTER_NUMBER', 'yandex_metrika_counter_number');


define ('SETTINGS_SECTION_LEADS', 'LEADS');
define ('SETTINGS_SEND_LEAD_TO_MAIL_ENABLE', 'send_lead_to_email_enable');
define ('SETTINGS_LEADS_MAILTO', 'lead_email');

define ('SETTINGS_SECTION_CATALOG', 'CATALOG_SETTINGS');
define ('SETTINGS_CATALOG_ROOT_CATEGORY_ID', 'root_category_id');
define ('SETTINGS_CATALOG_BUY_BTN_NOT_IN_CART_CLASS_NAME', 'buy_btn_not_in_cart_class_name');
define ('SETTINGS_CATALOG_BUY_BTN_IN_CART_CLASS_NAME', 'buy_btn_in_cart_class_name');
define ('SETTINGS_CATALOG_HEADER_PRODUCT_COUNT_CLASS_NAME', 'header_product_count_class_name');
define ('SETTINGS_CATALOG_HEADER_CART_ICON_TAG_ID', 'header_cart_icon_tag_id');
define ('SETTINGS_CATALOG_BUY_BTN_NOT_IN_CART_TEXT', 'buy_btn_not_in_cart_text');
define ('SETTINGS_CATALOG_BUY_BTN_IN_CART_TEXT', 'buy_btn_in_cart_text');

define ('SETTINGS_SECTION_PRODUCT', 'PRODUCT_SETTINGS');
define ('SETTINGS_PRODUCT_BUY_BTN_ITEM_ID', 'product_buy_btn_item_id');
define ('SETTINGS_PRODUCT_DISCOUNT_DB_FIELDNAME', 'product_discount_db_fieldname');
define ('SETTINGS_PRODUCT_COUNT_PRECISION', 'product_count_precision');
define ('SETTINGS_PRODUCT_PRICE_PRECISION', 'product_price_precision');
define ('SETTINGS_PRODUCT_DISPLAYED_PROPERTIES', 'product_displayed_properties');
define ('SETTINGS_PRODUCT_USE_PROPERTY1_AS_PRODUCT_VIEW', 'product_use_property1_as_product_view'); // первое свойство товара в корзине будет использовано в качестве идентификатора выбранного представления товара

define ('SETTINGS_SECTION_SMTP', 'SMTP');
define ('SETTINGS_SMTP_SERVER', 'smtp_server');
define ('SETTINGS_SMTP_LOGIN', 'smtp_login');
define ('SETTINGS_SMTP_PASSWORD', 'smtp_password');

define ('SETTINGS_SECTION_SEF_URL', 'SEF_URL_SETTINGS');
define ('SETTINGS_SEF_URL_ADD_CATALOG_PREFIX', 'add_catalog_prefix');
define ('SETTINGS_SEF_URL_DONT_CREATE_CATALOG_CHPU', 'dont_create_catalog_chpu');
define ('SETTINGS_SEF_URL_USE_ONLY_PRODUCT_NAME', 'use_only_product_name');

define ('SETTINGS_SECTION_BITRIX24', 'BITRIX24');
define ('SETTINGS_BITRIX24_ENABLE_CREATE_LEADS', 'enable_create_leads');
define ('SETTINGS_BITRIX24_CRM_HOST', 'crm_host');
define ('SETTINGS_BITRIX24_CRM_PORT', 'crm_port');
define ('SETTINGS_BITRIX24_CRM_PATH', 'crm_path');
define ('SETTINGS_BITRIX24_CRM_LOGIN', 'crm_login');
define ('SETTINGS_BITRIX24_CRM_PASSWORD', 'crm_password');
define ('SETTINGS_BITRIX24_ASSIGNED_BY_ID', 'assigned_by_id');
define ('SETTINGS_BITRIX24_SOURCE_ID', 'source_id');
define ('SETTINGS_BITRIX24_SOURCE_DESCRIPTION', 'source_description');
define ('SETTINGS_BITRIX24_CLIENT_ID', 'client_id');
define ('SETTINGS_BITRIX24_CLIENT_SECRET', 'client_secret');
define ('SETTINGS_BITRIX24_REFRESH_TOKEN', 'refresh_token');
define ('SETTINGS_BITRIX24_SCOPE', 'scope');
define ('SETTINGS_BITRIX24_WEBHOOK', 'webhook');
define ('SETTINGS_BITRIX24_LENGTH', 'ln');
define ('SETTINGS_BITRIX24_WIDTH', 'wd');
define ('SETTINGS_BITRIX24_HEIGHT', 'hg');
define ('SETTINGS_BITRIX24_WEIGHT', 'weight');
define ('SETTINGS_BITRIX24_STORE_ID', 'store_id');
define ('SETTINGS_BITRIX24_ID_PASSWORD', 'store_password');
define ('SETTINGS_BITRIX24_LEAD_PREFIX', 'lead_prefix'); // XXXX-XX-XX [Префикс] [Имя лида]
define ('SETTINGS_BITRIX24_LEAD_FIELD_TRAFFIC_SOURCE_FIRST', 'lead_field_traffic_source_first');
define ('SETTINGS_BITRIX24_LEAD_FIELD_TRAFFIC_SOURCE', 'lead_field_traffic_source');
define ('SETTINGS_BITRIX24_LEAD_FIELD_TRAFFIC_HREF_FIRST', 'lead_field_traffic_href_first');
define ('SETTINGS_BITRIX24_LEAD_FIELD_TRAFFIC_HREF', 'lead_field_traffic_href');
define ('SETTINGS_BITRIX24_LEAD_FIELD_CRM_CONTACT', 'lead_field_crm_contact');

define ('SETTINGS_ADD_TEXT_AFTER_TEXT', 'ADD_TEXT_AFTER_TEXT');
define ('SETTINGS_REPLACE_USER_TAGS', 'REPLACE_USER_TAGS');

class CCRMSettings
{
    public $sFileName = "";
    public $arrSettings = array();

    function __construct($sFileName)
    {
        $this->sFileName = $sFileName;
    }

    function Load()
    {
        if (file_exists($this->sFileName))
        {
            $this->arrSettings = parse_ini_file($this->sFileName, true, INI_SCANNER_RAW);
            array_walk($this->arrSettings, array($this, 'ReplaceDisallowTagsInSettingsFile'));
            return $this->arrSettings;
        }
        return null;
    }

    function Save()
    {
        $res = array();
        foreach($this->arrSettings as $key => $val)
        {
            if(is_array($val))
            {
                $res[] = "[$key]";
                foreach($val as $skey => $sval) $res[] = "$skey = ".(is_numeric($sval) ? $sval : '"'.$sval.'"');
            }
            else $res[] = "$key = ".(is_numeric($val) ? $val : '"'.$val.'"');
        }
        $this->SafeFilereWrite($this->sFileName, implode("\r\n", $res));
    }
    //////
    private function SafeFilereWrite($fileName, $dataToSave)
    {
        if ($fp = fopen($fileName, 'w'))
        {
            $startTime = microtime();
            do
            {            $canWrite = flock($fp, LOCK_EX);
                // If lock not obtained sleep for 0 - 100 milliseconds, to avoid collision and CPU load
                if(!$canWrite) usleep(round(rand(0, 100)*1000));
            } while ((!$canWrite)and((microtime()-$startTime) < 1000));

            //file was locked so now we can store information
            if ($canWrite)
            {            fwrite($fp, $dataToSave);
                flock($fp, LOCK_UN);
            }
            fclose($fp);
        }
    }

    private function ReplaceDisallowTagsInSettingsFile(&$item, $key)
    {
        $item = str_replace("@sc@", ";", $item);
    }
}

if (!isset($SETTINGS))
{
    $Obj = new CCRMSettings(SETTINGS_INI_FILE_NAME);
    $SETTINGS = $Obj->Load();
}


?>