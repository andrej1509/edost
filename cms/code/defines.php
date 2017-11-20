<?
// ������� ����� ��� ������

require_once ($_SERVER['DOCUMENT_ROOT'].'/cms/code/settings.php');

define ("CMS_FILE_INTERNET_PATH", "cms/code/");

define ("CMS_VARS_TAGS", "TagValues");
define ("CMS_VARS_DB", "DatabaseSettings");
define ("CMS_DB_ALLOWED_CATEGORIES_FILTER", "AllowedCategoriesFilter");


define ("TAG_HEAD_COMMON_JAVA_SCRIPTS", "HEAD_COMMON_JAVA_SCRIPTS");
define ("TAG_HEAD_JAVA_SCRIPTS_SHOPPING_CART", "HEAD_JAVA_SCRIPTS_SHOPPING_CART");

define ("TAG_STARTER", "{{");
define ("TAG_ENDER", "}}");
define ("TAG_STARTER2", "@@");
define ("TAG_ENDER2", "@@");
define ("TAG_STARTER_PARAM", "##");
define ("TAG_ENDER_PARAM", "##");


define ("TAG_MUSE_HTTP_REMOVER", "http://-");

define ("TAG_DOMAIN_NAME", "DOMAIN_NAME");
define ("TAG_DOMAIN_NAME_WITH_HTTP", "DOMAIN_NAME_WITH_HTTP");
define ("TAG_COMPANY_NAME", "COMPANY_NAME");
define ("TAG_CURRENT_PAGE", "CURRENT_PAGE");
define ("TAG_CURRENT_PAGE_FULL_CHPU_HREF", "CURRENT_PAGE_FULL_CHPU_HREF");
define ("TAG_CURRENT_ALTERNATE_MOBILE_PAGE_FULL_CHPU_HREF", "CURRENT_ALTERNATE_MOBILE_PAGE_FULL_CHPU_HREF"); // ссылка на аналогичную страницу мобильного сайта

define ("TAG_SETTINGS", "SETTINGS");

define ("TAG_PRINT_TEMPLATE", "PRINT_TEMPLATE"); // формат {{PRINT_TEMPLATE(Имя файла шаблона)}}


define ("TAG_HEADER", "SYS_HEADER"); // формат {{HEADER(Title, MetaKeywords, MetaDescription, AdditionalHtml)}}
define ("TAG_HEADER_TITLE", "HEADER_TITLE");
define ("TAG_HEADER_META_KEYWORDS", "HEADER_META_KEYWORDS");
define ("TAG_HEADER_META_DESCRIPTION", "HEADER_META_DESCRIPTION");
define ("TAG_HEADER_ADDITIONAL_HTML", "HEADER_ADDITIONAL_HTML");

define ("TAG_FOOTER", "SYS_FOOTER");


define ("TAG_COUNTERS", "COUNTERS");
define ("TAG_COUNT", "COUNT");
define ("TAG_CATALOG_LIST", "CATALOG_LIST");
define ("TAG_CATALOG_CATEGORY_ID", "CATEGORY_ID");
define ("TAG_CATALOG_CATEGORY_NAME", "CATEGORY_NAME");
define ("TAG_CATALOG_CATEGORY", "CATALOG_CATEGORY"); // формат {{CATALOG_CATEGORY(Идентификатор категории ID, Лимит по выводу до кнопки "Показать еще")}}
define ("TAG_CATALOG_CATEGORIES_WITH_CHILDS", "CATALOG_CATEGORY_WITH_CHILDS"); // формат {{CATALOG_CATEGORY_WITH_CHILDS(Идентификатор родительской категории ID, Лимит по выводу до кнопки "Показать еще")}}
define ("TAG_CATALOG_ITEMS", "CATALOG_ITEMS");
define ("TAG_CATALOG_PRODUCT_LIST", "PRODUCT_LIST");
define ("TAG_CATALOG_HIDDEN_PRODUCT_LIST", "HIDDEN_PRODUCT_LIST");
define ("TAG_CATALOG_ITEM_BUY_BTN_STATE_CLASS", "CATALOG_ITEM_BUY_BTN_STATE_CLASS"); // текущее состояние кнопки "в корзину" или уже "в корзине".
define ("TAG_PRODUCT_CATALOG_ITEM_BUY_BTN_STATE_CLASS", "PRODUCT_CATALOG_ITEM_BUY_BTN_STATE_CLASS"); // текущее состояние кнопки "в корзину" или уже "в корзине".
define ("TAG_CATALOG_ITEM_BUY_BTN_TEXT", "TO_CART_BTN_TEXT");
define ("TAG_PRODUCT_TO_CART_BTN_TEXT", "PRODUCT_TO_CART_BTN_TEXT");

define ("TAG_CATALOG_CATEGORY_CHPU_LINK", "CATEGORY_CHPU_LINK");
define ("TAG_CATALOG_CATEGORY_FULL_CHPU_LINK", "CATEGORY_FULL_CHPU_LINK");

define ("TAG_CATALOG_CATEGORY_DESCRIPTION", "CATEGORY_DESCRIPTION");
define ("TAG_CATALOG_CATEGORY_IMAGE_PATH", "CATEGORY_IMAGE_PATH");
define ("TAG_CATALOG_CATEGORY_SORT", "CATEGORY_SORT");
define ("TAG_CATALOG_CATEGORY_HTML_DESC_PATH", "CATEGORY_HTML_DESC_PATH");


define ("TAG_CATEGORY_TREE_LIST", "CATEGORY_TREE_LIST"); // формат {{CATEGORY_TREE(Идентификатор ID родительской категории или 0))
define ("TAG_CATEGORY_TREE_LEVEL1", "CATEGORY_TREE_LEVEL1");
define ("TAG_CATEGORY_TREE_LEVEL2", "CATEGORY_TREE_LEVEL2");
define ("TAG_CATEGORY_TREE_LEVEL3", "CATEGORY_TREE_LEVEL3");


define ("TAG_PRODUCT_ID", "PRODUCT_ID");
define ("TAG_PRODUCT_NAME", "PRODUCT_NAME");
define ("TAG_PRODUCT_DESCRIPTION", "PRODUCT_DESCRIPTION");
define ("TAG_PRODUCT_PRICE", "PRODUCT_PRICE");
define ("TAG_PRODUCT_UNIT", "PRODUCT_UNIT");

// with structure data from Shcema.org
define ("TAG_PRODUCT_ID_WSD", "PRODUCT_ID_WSD");
define ("TAG_PRODUCT_NAME_WSD", "PRODUCT_NAME_WSD");
define ("TAG_PRODUCT_DESCRIPTION_WSD", "PRODUCT_DESCRIPTION_WSD");
define ("TAG_PRODUCT_PRICE_WSD", "PRODUCT_PRICE_WSD");

define ("TAG_PRODUCT_TOTAL_PRICE", "PRODUCT_TOTAL_PRICE"); // количество * цена
define ("TAG_PRODUCT_TOTAL_PRICE_WITH_DISCOUNT", "PRODUCT_TOTAL_PRICE_WITH_DISCOUNT"); // количество * цена

define ("TAG_PRODUCT_PRICE_DISCOUNT", "PRODUCT_PRICE_DISCOUNT");
define ("TAG_PRODUCT_PRICE_WITH_DISCOUNT", "PRODUCT_PRICE_WITH_DISCOUNT");
define ("TAG_PRODUCT_MAIN_BIG_IMAGE_PATH", "PRODUCT_MAIN_BIG_IMAGE_PATH");
define ("TAG_PRODUCT_MAIN_SMALL_IMAGE_PATH", "PRODUCT_MAIN_SMALL_IMAGE_PATH");
define ("TAG_PRODUCT_MAIN_MIDDLE_IMAGE_PATH", "PRODUCT_MAIN_MIDDLE_IMAGE_PATH");
define ("TAG_PRODUCT_OTHER_BIG_IMAGE_PATH", "PRODUCT_OTHER_BIG_IMAGE_PATH");

define ("TAG_PRODUCT_IMAGE_COUNT", "PRODUCT_IMAGE_COUNT");

define ("TAG_PRODUCT_BIG_IMAGES_PATH", "PRODUCT_BIG_IMAGES_PATH");
define ("TAG_PRODUCT_MIDDLE_IMAGES_PATH", "PRODUCT_MIDDLE_IMAGES_PATH");
define ("TAG_PRODUCT_SMALL_IMAGES_PATH", "PRODUCT_SMALL_IMAGES_PATH");

define ("TAG_PRODUCT_HTML_FULL_HREF", "PRODUCT_HTML_FULL_HREF");
define ("TAG_PRODUCT_IMAGE", "PRODUCT_IMAGE"); // спец тег. формат {{PRODUCT_IMAGE(Номер изображения = имя файла. Например 1 = файл 1.jpg)}}
define ("TAG_PRODUCT_IMAGE_BLOCK", "PRODUCT_IMAGE_BLOCK"); // спец тег. формат {{PRODUCT_IMAGE_BLOCK(Номер изображения = имя файла. Например 1 = файл 1.jpg)}} Вставляет вместо данного блока html-тег img по размеру ограничивающего блока.
define ("TAG_PRODUCT_COUNT_INPUT", "PRODUCT_COUNT_INPUT");
define ("TAG_PRODUCT_PARAMETER", "PRODUCT_PARAMETER"); // формат {{PRODUCT_PARAMETER({{PRODUCT_ID}}, Имя параметра)}}
define ("TAG_PRODUCT_HIDDEN_INPUT_WITH_INFO", "HIDDEN_PRODUCT_INFO");
define ("TAG_PRODUCT_INFO_PARAMS", "PRODUCT_INFO");
define ("TAG_PRODUCT_OTHER_SMALL_IMAGE_PATH", "PRODUCT_OTHER_SMALL_IMAGE_PATH");
define ("TAG_PRODUCT_COUNT_IN_CART", "PRODUCT_COUNT_IN_CART"); // текущее количества товара в корзине
define ("TAG_CART_PRODUCT_PROPERTY", "CART_PRODUCT_PROPERTY"); // формат {{CART_PRODUCT_PROPERTY(Номер свойства из файла настроек)}}
define ("TAG_PRODUCT_PROPERTY", "PRODUCT_PROPERTY"); // формат {{PRODUCT_PROPERTY_AS_TEMPLATE(Имя колонки в таблице продуктов, в которой содержаться данныеа)}}
define ("TAG_PRODUCT_PROPERTY_AS_TEMPLATE", "PRODUCT_PROPERTY_AS_TEMPLATE"); // формат {{PRODUCT_PROPERTY_AS_TEMPLATE(Имя колонки в таблице продуктов, в которой содержиться путь к файлу шаблона)}}
define ("TAG_PRODUCT_CHPU_LINK", "PRODUCT_CHPU_LINK");
define ("TAG_PRODUCT_FULL_CHPU_LINK", "PRODUCT_FULL_CHPU_LINK");

define ("TAG_CURRENT_CITY", "CURRENT_CITY");

define ("TAG_FORM_HIDDEN_VARIABLES", "FORM_HIDDEN_VARIABLES");

define ("TAG_TEMPLATE_FILE", "TEMPLATE_FILE");
define ("TAG_TEMPLATE_FILE_NAME", "TEMPLATE_FILE_NAME");

define ("TEMPLATE_SYS_HEADER_FILE", "other/header.php");
define ("TEMPLATE_SYS_FOOTER_FILE", "other/footer.php");
define ("TEMPLATE_SHORT_HEADER_FILE", "short_header.php");
define ("TEMPLATE_SHORT_FOOTER_FILE", "short_footer.php");
//define ("TEMPLATE_COUNTERS_FILE", "counters.html");
define ("TEMPLATE_PRODUCT_PAGE_FILE", "product.php");

define ("TEMPLATE_CATALOG", "catalog/");
define ("TEMPLATE_CATALOG_ITEM", "product_item.php");
define ("TEMPLATE_CATALOG_CATEGORY_IN_LANDING", "product_list.php");
define ("TEMPLATE_CATALOG_CATEGORY_IN_LANDING_HIDDEN_PART", "product_list_hidden_part.php");

define ("TEMPLATE_ORDER_PRODUCT_LIST", "order/product_list.php");
define ("TEMPLATE_ORDER_PRODUCT_LIST_EMPTY", "order/product_list_empty.php");
define ("TEMPLATE_ORDER_PRODUCT_ITEM", "order/product_item.php");

define ("TEMPLATE_FILENAME_PRODUCT_LIST", "product_list.php");
define ("TEMPLATE_FILENAME_PRODUCT_LIST_EMPTY", "product_list_empty.php");
define ("TEMPLATE_FILENAME_PRODUCT_ITEM", "product_item.php");

// так как cms примитивная, то максимум делаем вложенность до 3 уровней;
define ("TEMPLATE_CATEGORY_TREE", "catalog_tree/");
define ("TEMPLATE_CATEGORY_TREE_ITEM", "catalog_category_tree.php");
define ("TEMPLATE_CATEGORY_TREE_NAME_ITEM_LEVEL1", "catalog_tree_category_name_item_level1.php");
define ("TEMPLATE_CATEGORY_TREE_NAME_ITEM_LEVEL2", "catalog_tree_category_name_item_level2.php");
define ("TEMPLATE_CATEGORY_TREE_NAME_ITEM_LEVEL3", "catalog_tree_category_name_item_level3.php");

define ("TEMPLATE_DIRECTORY", $_SERVER['DOCUMENT_ROOT']."/cms/template/");
define ("PROCESSED_TEMPLATE_DIRECTORY", $_SERVER['DOCUMENT_ROOT']."/cms/processed_html_template/");
define ("TEMPLATE_PHP_DIRECTORY", CMS_FILE_LOCAL_PATH_PHP."template/");
define ("TEMPLATE_PHP_PAGE_CODE_FILENAME", "template_page_code.tpl");
define ("TEMPLATE_PHP_PAGE_CODE_FILEPATH", TEMPLATE_PHP_DIRECTORY.TEMPLATE_PHP_PAGE_CODE_FILENAME);
define ("SIMPLE_TEMPLATE_PHP_PAGE_CODE_FILENAME", "simple_page_code.tpl");
define ("SIMPLE_TEMPLATE_PHP_PAGE_CODE_FILEPATH", TEMPLATE_PHP_DIRECTORY.SIMPLE_TEMPLATE_PHP_PAGE_CODE_FILENAME);

define ("TEMPLATE_COUNTERS_FILE", TEMPLATE_DIRECTORY."other/counters.html");

define ("PRODUCTS_INFO_PATH", "/cms/products/");
define ("PRODUCTS_INFO_WITH_NAMES_PATH", "/cms/products_with_names/");
define ("PRODUCTS_BIG_IMAGE_PATH", PRODUCTS_INFO_PATH."img/big/");
define ("PRODUCTS_SMALL_IMAGE_PATH", PRODUCTS_INFO_PATH."img/small/");

define ("TEMP_DIRECTORY", CMS_FILE_LOCAL_PATH_PHP."temp/");

// системные куски кода для добавления в файлы всех страниц
define ("COMMON_CODE_FILE_HEAD", "common_head_code.tpl");
define ("COMMON_CODE_FILE_BEGIN_BODY", "common_begin_body_code.tpl");
define ("COMMON_CODE_FILE_END_BODY", "common_end_body_code.tpl");

define ("COMMON_CODE_FILEPATH_HEAD", TEMPLATE_PHP_DIRECTORY.COMMON_CODE_FILE_HEAD);
define ("COMMON_CODE_FILEPATH_BEGIN_BODY", TEMPLATE_PHP_DIRECTORY.COMMON_CODE_FILE_BEGIN_BODY);
define ("COMMON_CODE_FILEPATH_END_BODY", TEMPLATE_PHP_DIRECTORY.COMMON_CODE_FILE_END_BODY);

// куски кода для реализации работы интернет-магазина. Нужен не на всех страницах.

// пользовательские куски кода для добавления в файлы всех страниц
define ("USERCODEFILE_HEAD", "");
define ("USERCODEFIL_BEGIN_BODY", "");
define ("USERCODEFIL_END_BODY", "");



define ("TAG_ITEM_ID_PRODUCT_COUNT", "ITEM_ID_PRODUCT_COUNT");
define ("TAG_ITEM_ID_PRODUCT_PRICE", "ITEM_ID_PRODUCT_PRICE");
define ("TAG_ITEM_ID_PRODUCT_TOTAL_PRICE", "ITEM_ID_PRODUCT_TOTAL_PRICE"); // количество * цена
define ("TAG_ITEM_ID_PRODUCT_SMALL_IMG", "ITEM_ID_PRODUCT_SMALL_IMG");
define ("TAG_ITEM_ID_PRODUCT_RAW", "ITEM_ID_PRODUCT_RAW"); // id объекта с информацией о конкретном товаре. Это поле необъодимо для удаления товара.
define ("TAG_ITEM_ID_PRODUCT_BUY_BTN", "ITEM_ID_PRODUCT_BUY_BTN");
// после префикса должен стоять PRODUCT_ID
define ("PREFIX_ITEM_ID_PRODUCT_COUNT", "product_count_");
define ("PREFIX_ITEM_ID_PRODUCT_PRICE", "product_price_");
//define ("PREFIX_ITEM_ID_PRODUCT_TOTAL_PRICE", "product_total_price_"); // количество * цена
define ("PREFIX_ITEM_ID_PRODUCT_TOTAL_PRICE",  TAG_PRODUCT_TOTAL_PRICE."_"); // количество * цена
define ("PREFIX_ITEM_ID_PRODUCT_SMALL_IMG", "product_small_img_");
define ("PREFIX_ITEM_ID_PRODUCT_RAW", "product_raw_"); // id объекта с информацией о конкретном товаре. Это поле необъодимо для удаления товара.
define ("PREFIX_ITEM_ID_PRODUCT_BUY_BTN", "product_buy_btn_");

// теги и идентификаторы для корзины.
define ("TAG_SHOPPING_CART_PRODUCT_LIST", "SHOPPING_CART_PRODUCT_LIST");
define ("TAG_SHOPPING_CART_TOTAL_PRODUCT_COUNT", "SHOPPING_CART_TOTAL_PRODUCT_COUNT");
define ("TAG_SHOPPING_CART_PRODUCT_COUNT", "SHOPPING_CART_PRODUCT_COUNT");
define ("TAG_SHOPPING_CART_TOTAL_VALUE", "SHOPPING_CART_TOTAL_VALUE");
define ("TAG_SHOPPING_CART_TOTAL_DISCOUNT_VALUE", "SHOPPING_CART_TOTAL_DISCOUNT_VALUE");
define ("TAG_SHOPPING_CART_TOTAL_DISCOUNT", "SHOPPING_CART_TOTAL_DISCOUNT");
define ("TAG_SHOPPING_CART_TOTAL_VALUE_WITH_DISCOUNT", "SHOPPING_CART_TOTAL_VALUE_WITH_DISCOUNT");

define ("TAG_ITEM_ID_SHOPPING_CART_TOTAL_PRODUCT_COUNT", "ITEM_ID_SHOPPING_CART_TOTAL_PRODUCT_COUNT");
define ("TAG_ITEM_ID_SHOPPING_CART_TOTAL_VALUE", "ITEM_ID_SHOPPING_CART_TOTAL_VALUE");
define ("TAG_ITEM_ID_SHOPPING_CART_TOTAL_DISCOUNT_VALUE", "ITEM_ID_SHOPPING_CART_TOTAL_DISCOUNT_VALUE");
define ("TAG_ITEM_ID_SHOPPING_CART_TOTAL_DISCOUNT", "ITEM_ID_SHOPPING_CART_TOTAL_DISCOUNT");
define ("TAG_ITEM_ID_SHOPPING_CART_TOTAL_VALUE_WITH_DISCOUNT", "ITEM_ID_SHOPPING_CART_TOTAL_VALUE_WITH_DISCOUNT");

define ("ITEM_ID_SHOPPING_CART_TOTAL_PRODUCT_COUNT", "cart_total_product_count");
define ("ITEM_ID_SHOPPING_CART_TOTAL_VALUE", "cart_total_value");
define ("ITEM_ID_SHOPPING_CART_TOTAL_DISCOUNT_VALUE", "cart_total_discount_value");
define ("ITEM_ID_SHOPPING_CART_TOTAL_DISCOUNT", "cart_total_discount");
define ("ITEM_ID_SHOPPING_CART_TOTAL_VALUE_WITH_DISCOUNT", "cart_total_value_with_discount");

/// События для плагинов
define ("TAG_EVENT_PREPARE_SHOPPING_CART", "EVENT_PREPARE_SHOPPING_CART");

define ("PAGE_ORDER", "/order/");
define ("PAGE_THANK_YOU", "/thank_you/");

define ("COOKIE_PARAM_FIRST_VISIT_DATE", "FIRST_VISIT_DATE");
define ("COOKIE_PARAM_FIRST_HTTP_REFERER", "FIRST_HTTP_REFERER");
define ("COOKIE_PARAM_HTTP_REFERER", "HTTP_REFERER");
define ("COOKIE_PARAM_FIRST_SOURCE_SITE", "FIRST_SOURCE_SITE");
define ("COOKIE_PARAM_SOURCE_SITE", "SOURCE_SITE");

define ("COOKIE_PARAM_FIRST_UTM_SOURCE", "FIRST_UTM_SOURCE");
define ("COOKIE_PARAM_FIRST_UTM_MEDIUM", "FIRST_UTM_MEDIUM");
define ("COOKIE_PARAM_FIRST_UTM_CAMPAIGN", "FIRST_UTM_CAMPAIGN");
define ("COOKIE_PARAM_FIRST_UTM_CONTENT", "FIRST_UTM_CONTENT");
define ("COOKIE_PARAM_FIRST_UTM_TERM", "FIRST_UTM_TERM");
define ("COOKIE_PARAM_FIRST_UTM_REGION", "FIRST_UTM_REGION");

define ("COOKIE_PARAM_UTM_SOURCE", "UTM_SOURCE");
define ("COOKIE_PARAM_UTM_MEDIUM", "UTM_MEDIUM");
define ("COOKIE_PARAM_UTM_CAMPAIGN", "UTM_CAMPAIGN");
define ("COOKIE_PARAM_UTM_CONTENT", "UTM_CONTENT");
define ("COOKIE_PARAM_UTM_TERM", "UTM_TERM");
define ("COOKIE_PARAM_UTM_REGION", "UTM_REGION");

define('COOKIE_PARAM_CLIENT_DATA','CLIENT_DATA');
define('COOKIE_PARAM_SHOPPING_CART','SHOPPING_CART');

define('COOKIE_HIDE_CURRENT_ACTION','HIDE_CURRENT_ACTION');

define('UTM_CLIENT_ACTION','client_action');

?>