<?php

class CCMSDatabaseLog
{
    /** @var CCMSDatabase $DB */
    private $dbConnection = null;
    const sTableNameShoppingCartHistory = 'cms_shoppingcart_history';
    const sTemplateTableShoppingCartHistory = "
                CREATE TABLE ?s.cms_shoppingcart_history (
                      ID int(11) NOT NULL AUTO_INCREMENT,
                      PRODUCT_ID int(11) DEFAULT NULL,
                      PRODUCT_NAME text DEFAULT NULL,
                      PRODUCT_PROPERTIES text DEFAULT NULL,
                      DATE_CREATE datetime DEFAULT NULL,
                      DATE_UPDATE datetime DEFAULT NULL,
                      FIRST_HTTP_REFERER text DEFAULT NULL,
                      FIRST_SOURCE_SITE text DEFAULT NULL,
                      FIRST_UTM_SOURCE text DEFAULT NULL,
                      FIRST_UTM_MEDIUM text DEFAULT NULL,
                      FIRST_UTM_CAMPAIGN text DEFAULT NULL,
                      FIRST_UTM_CONTENT text DEFAULT NULL,
                      FIRST_UTM_TERM text DEFAULT NULL,
                      HTTP_REFERER text DEFAULT NULL,
                      SOURCE_SITE text DEFAULT NULL,
                      UTM_SOURCE text DEFAULT NULL,
                      UTM_MEDIUM text DEFAULT NULL,
                      UTM_CONTENT text DEFAULT NULL,
                      UTM_TERM text DEFAULT NULL,
                      SESSION_ID varchar(255) DEFAULT NULL,
                      PRIMARY KEY (ID)
                    )
                    ENGINE = INNODB
                    AUTO_INCREMENT = 1
                    CHARACTER SET utf8
                    COLLATE utf8_general_ci;
    ";

    const sTemplateIShoppingCartHistoryInsertQuery = "
        INSERT INTO ?s VALUES(0,?d,'?s','?s',NOW(),NOW(),'?s','?s','?s','?s','?s','?s','?s','?s','?s','?s','?s','?s','?s','?s')
        ";
    const sTemplateIShoppingCartHistoryUpdateProductPropertyQuery = "
        UPDATE ?s SET PRODUCT_PROPERTIES = '?s', DATE_UPDATE = NOW()  WHERE PRODUCT_ID = ?d
    ";

    function __construct()
    {
        $this->dbConnection = CCMS::$DB->dbConnection;

        if (isset($this->dbConnection))
            if (!CCMS::$DB->IsTableExistsInDatabase(CCMSDatabaseLog::sTableNameShoppingCartHistory))
            {
                $dbResult = $this->dbConnection->query(
                    self::sTemplateTableShoppingCartHistory,
                    $this->dbConnection->sDatabaseName);
            }
    }

    public function OnPutProductInShoppingCart($pProductInfo, $arrProductProperties = null)
    {
        if (!isset($this->dbConnection)) return false;

        if (isset($pProductInfo))
        {
            // если такая запись для данной сессии уже есть, то ничего не делаем
            $dbResult = $this->dbConnection->query(
                "SELECT ID FROM ?s WHERE SESSION_ID = '?s' AND PRODUCT_ID = ?d"
                ,self::sTableNameShoppingCartHistory
                ,session_id()
                ,$pProductInfo['ID'])->fetch_assoc();

            $sProductProperties = "";
            if (isset($arrProductProperties))
            {
                $sProductProperties = implode(', ', $arrProductProperties);
            }

            if (!isset($dbResult))
            {
                $dbResult = $this->dbConnection->query(
                     self::sTemplateIShoppingCartHistoryInsertQuery
                    ,self::sTableNameShoppingCartHistory
                    ,$pProductInfo['ID']
                    ,$pProductInfo['NAME']
                    ,$sProductProperties
                    ,$_COOKIE[COOKIE_PARAM_FIRST_HTTP_REFERER]
                    ,$_COOKIE[COOKIE_PARAM_FIRST_SOURCE_SITE]
                    ,$_COOKIE[COOKIE_PARAM_FIRST_UTM_SOURCE]
                    ,$_COOKIE[COOKIE_PARAM_FIRST_UTM_MEDIUM]
                    ,$_COOKIE[COOKIE_PARAM_FIRST_UTM_CAMPAIGN]
                    ,$_COOKIE[COOKIE_PARAM_FIRST_UTM_CONTENT]
                    ,$_COOKIE[COOKIE_PARAM_FIRST_UTM_TERM]
                    ,$_COOKIE[COOKIE_PARAM_HTTP_REFERER]
                    ,$_COOKIE[COOKIE_PARAM_SOURCE_SITE]
                    ,$_COOKIE[COOKIE_PARAM_UTM_SOURCE]
                    ,$_COOKIE[COOKIE_PARAM_UTM_MEDIUM]
                    ,$_COOKIE[COOKIE_PARAM_UTM_CONTENT]
                    ,$_COOKIE[COOKIE_PARAM_UTM_TERM]
                    ,session_id()
                );
            }
            else
            {
                // обновляем свойства товара

                $this->dbConnection->query(
                     self::sTemplateIShoppingCartHistoryUpdateProductPropertyQuery
                    ,self::sTableNameShoppingCartHistory
                    ,$sProductProperties
                    ,$pProductInfo['ID']);
            }
        }
    }
}

require_once ($_SERVER['DOCUMENT_ROOT'].'/cms/code/settings.php');
require_once (CMS_FILE_LOCAL_PATH_PHP.'defines.php');