<?php

define("TAG_TYPE_VALUE", "TAG_TYPE_VALUE");
define("TAG_TYPE_FUNCTION", "TAG_TYPE_FUNCTION");

define("TAG_CLASS_OBJ", 'CLASS_OBJ');
define("TAG_TYPE", 'TYPE');
define("TAG_VALUE", 'VALUE');
define("TAG_HTML_ID", 'HTML_ID');
define("TAG_FUNCTION", 'FUNCTION');
define("TAG_DESCRIPTION", 'DESC');

define("TAG_PLUGIN_TAG_PREFIX", 'PL_');

define("TAG_ERROR_ALREADY_REGISTERED", 'TAG_ERROR_ALREADY_REGISTERED');
define("TAG_ERROR_NOT_REGISTERED", 'TAG_ERROR_NOT_REGISTERED');

define("PLUGINS_PROCESSED_TEMPLATE_DIRECTORY", PROCESSED_TEMPLATE_DIRECTORY.'plugins/');
define("PLUGINS_TEMPLATE_DIRECTORY", $_SERVER['DOCUMENT_ROOT'].'/cms/template/plugins/');
define("PLUGINS_TEMPLATE_URL_PATH", '/cms/template/plugins/');

define("PLUGINS_COMMON_DIRECORY", '/template/common/');
define("PLUGINS_TEMPLATE_HEAD_BLOCK", PLUGINS_COMMON_DIRECORY.'head.php');
define("PLUGINS_TEMPLATE_BEGIN_BODY_BLOCK", PLUGINS_COMMON_DIRECORY.'begin_body.php');
define("PLUGINS_TEMPLATE_END_BODY_BLOCK", PLUGINS_COMMON_DIRECORY.'end_body.php');

define("PLUGINS_CODE_DIRECTORY", $_SERVER['DOCUMENT_ROOT'].'/cms/plugins/');

define("PLG_EVENT_SHOPPING_CART_ADD_PRODUCT", "PLG_EVENT_SHOPPING_CART_ADD_PRODUCT");

abstract class CBasePluginClass
{
    protected $bIsActive = FALSE; // включен ли плагин, должен ли он работать. Это делается либо исходя их настроек или сам плагин может отключить себя из-за ошибок в работе.
    /**
     * @var Database_Mysql
     */
    protected $dbConn;
    protected $sDirectoryName = "";

    // Уникальный префикс тега. Если тег в тексте сайта имеет этот префикс, то будет вызвана функция GetTagValue
    // При установке значения функцией будет вызвана функция SetTagValue у этого плагина и сама переменная создана в
    // в списке тегов. В основном предназначена для планинов выводящих, например, поля таблицы, или cookies
    protected $sPluginTagPreFix = "";

    protected $arrSettings = array();

    public function __set($property, $value) {

        if (property_exists($this, $property)) {
            $this->$property = $value;
        }
    }

    public function __get($property) {

        if (property_exists($this, $property)) {
            return $this->$property;
        }
        else
            return null;
    }

    function __construct($sDirectoryName, $bIsActive = TRUE, $sPluginTagPreFix = "")
    {
        $this->dbConn = CCMS::$DB->dbConnection;
        $this->sDirectoryName = $sDirectoryName;
        $this->bIsActive=$bIsActive;
        $this->sPluginTagPreFix = TAG_PLUGIN_TAG_PREFIX.$sPluginTagPreFix;
        $this->LoadSettings();
    }

    /**
     * @return bool
     */
    final function LoadSettings()
    {
        $Obj = new CCRMSettings(PLUGINS_CODE_DIRECTORY.$this->sDirectoryName.'/settings.ini');
        $this->arrSettings = $Obj->Load();

        return isset($this->arrSettings);
    }

    final public function IsTableExistsInDatabase($sTableName, $sDatabase = "")
    {
        if (!isset(CCMS::$DB->dbConnection)) return false;

        return CCMS::$DB->IsTableExistsInDatabase($sTableName);
    }

    final public function GetTagValueWithArgs()
    {
        $arrParameters = func_get_args();

        if (count($arrParameters)>1) {
            array_shift($arrParameters);
            return $this->GetTagValue(func_get_arg(0), $arrParameters);
        }
        else
            return "";
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // набор функций для переопределения
    // возвращает массив строк для файла .httaccess
    public function GetHtaccessRows() { return null; }
    // возвращает массив строк для файла sitemap.xml
    public function GetPagesForSitemap() { return null; }
    // функция должна выдавать усеченную ЧПУ ссылку без имени домена
    public function GetCurrentCHPULink() { return null; }
    // принимает на вход данные об изменении параметра на сайте и выдвет в качестве результата изменение связанных с ним параметров
    public function GetConnectedPropertyValue(
        $sParamName,
        $sParamValue = null,
        $sHTMLTagId = '',
        $sTableName = '',
        $pTableRow = null) { return null; }
    // проверяет допустима ли установка свойства в данное значение другой частью кода, если нет,
    // то корректирует до допустимого помещая его в $ValidValue
    public function ValidateTagValue($sTagName, $objTagValue, &$objValidValue) { return true; }
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // обязательная функция для реализации
    public abstract function GetTagValue($sTagName, $arrParameters = null);

    // позволяет обрабатывать стандартные или произвольные события плагинам
    public function OnEvent($sEventType, $pEventData = null) { return; }
}

class CBaseAddonClass
{
    static $Tags = array();
    static $AddonClasses = array();

    public static function Initialize()
    {
        // ищцм плагины в папке с плагинами и инициализируем их
        $aDirectories = scandir(PLUGINS_CODE_DIRECTORY);

        if (count($aDirectories)>0) {
            foreach ($aDirectories as $sPath) {
                if (($sPath=='..') || ($sPath=='.')) continue;

                if (is_dir(PLUGINS_CODE_DIRECTORY.$sPath)) {
                    self::GetAndInitPhpClassesFromFile(PLUGINS_CODE_DIRECTORY.$sPath.'/main.php');
                }
            }
        }
    }

    private static function GetAndInitPhpClassesFromFile($sFilePath)
    {
        if (!is_file($sFilePath)) return false;

        $php_code = file_get_contents($sFilePath);

        $Result = array();
        $tokens = token_get_all($php_code);
        $count = count($tokens);
        for ($i = 2; $i < $count; $i++) {
            if (   $tokens[$i - 2][0] == T_CLASS
                && $tokens[$i - 1][0] == T_WHITESPACE
                && $tokens[$i][0] == T_STRING) {

                $class_name = $tokens[$i][1];

                include_once ($sFilePath);

                if (($class_name != 'CBasePluginClass') && is_subclass_of(new $class_name(), 'CBasePluginClass'))
                    $Result[] = $class_name;
            }
        }

        return $Result;
    }

    public static function GetTags()
    {
        return self::$Tags;
    }

    public static function GetTag($TagName)
    {
        if (isset(self::$Tags[$TagName]))
            return self::$Tags[$TagName];
        else
            return false;
    }

    public static function GetPluginClassByTagPrefix($TagName)
    {
        foreach (self::$AddonClasses as $Obj)
        {
            if (stripos ($TagName, $Obj->sPluginTagPreFix."_")==0)
                return $Obj;
        }

        return null;
    }

    public static function GetTagValue($TagName, $arrParameters = null)
    {
        $ResultValue = "";
        if (isset(self::$Tags[$TagName])) {
            if (self::$Tags[$TagName][TAG_TYPE] == TAG_TYPE_VALUE)
            {
                $ResultValue = self::$Tags[$TagName][TAG_VALUE];
            }
            else {
                try {

                    $Obj = self::$Tags[$TagName][TAG_CLASS_OBJ];

                    $Function = self::$Tags[$TagName][TAG_FUNCTION];
                    if (isset($Function)&&($Function!=''))
                    {
                        if (isset($arrParameters))
                            eval("\$ResultValue = \$Obj->\$Function($arrParameters);");
                        //$ResultValue = $Obj->$Function($aParameters);
                        else
                            $ResultValue = $Obj->$Function();
                    }
                    else
                    {
/*
                        if (isset($arrParameters))
                            eval("\$ResultValue = \$Obj->GetTagValue(\"$TagName\", $arrParameters);");
                            //$ResultValue = $Obj->GetTagValueWithArgs($TagName, $aParameters);
                        else
                            $ResultValue = $Obj->GetTagValue($TagName);*/
                        //!!!!!!! 22.07.17*/
                        $ResultValue = $Obj->GetTagValue($TagName, $arrParameters);
                    }
                } catch (Exception $e) {
                    CLog::Write('Выброшено исключение: '.$e->getMessage());
                }
            }
            return $ResultValue;
        }
        else
        {
            // попытаемся найти класс по префиксу для не зарегистрированных, но обрабатываемых плагинами тегов
            $Obj = self::GetPluginClassByTagPrefix($TagName);
            if (isset($Obj))
            {
                if (isset($aParameters))
                    eval("\$ResultValue = \$Obj->GetTagValueWithArgs(\"$TagName\", $aParameters);");
                else
                    $ResultValue = $Obj->GetTagValue($TagName);

                return $ResultValue;
            }
        }

        return TAG_ERROR_NOT_REGISTERED;
    }

    public static function SetTagValue($TagName, $objValue)
    {
        $bResult = false;
        if (isset(self::$Tags[$TagName]))
        {
            $Obj = self::$Tags[$TagName][TAG_CLASS_OBJ];

            if (isset($Obj))
            {
                $objValidValue = null;
                if ($Obj->ValidateTagValue($TagName, $objValue, $objValidValue)) {
                    self::$Tags[$TagName][TAG_VALUE] = $objValue;
                    $bResult = true;
                } else
                    self::$Tags[$TagName][TAG_VALUE] = $objValidValue;
                return $bResult;
            }
        }

        return TAG_ERROR_NOT_REGISTERED;
    }



    public static function IsTag($TagName)
    {
        if (isset(self::$Tags[$TagName])) return true;
        else return false;
    }

    public static function SetTagWithFunction($TagClass, $TagName, $TagFunctionName = null, $sTagDescription = "", $TagValue = null)
    {
        if (isset(self::$Tags[$TagName]))
            return TAG_ERROR_ALREADY_REGISTERED;
        // проверяем есть ли класс в $AddonClasses
        if (!isset(self::$AddonClasses[get_class($TagClass)]))
        {
            self::$AddonClasses[get_class($TagClass)] = clone $TagClass;
        }

        self::$Tags[$TagName] =
            array(
                TAG_CLASS_OBJ => self::$AddonClasses[get_class($TagClass)],
                TAG_TYPE => TAG_TYPE_FUNCTION,
                TAG_VALUE => $TagValue,
                TAG_FUNCTION => $TagFunctionName,
                TAG_DESCRIPTION => $sTagDescription);

        return true;
    }

    public static function SetTag($TagClassName, $TagName, $TagValue, $sTagDescription = "")
    {
        return self::SetTagWithFunction($TagClassName, $TagName, null, $sTagDescription, $TagValue);
    }

    public static function GetHtaccessRowsForAllPlugins()
    {
        $saCHPU = array();
        foreach (self::$AddonClasses as $Obj)
        {
            $saPluginCHPU = $Obj->GetHtaccessRows();
            if (isset($saPluginCHPU)&&(count($saPluginCHPU)>0))
                $saCHPU = array_merge($saCHPU, $saPluginCHPU);
        }

        return $saCHPU;
    }

    public static function GetPagesForSitemapForAllPlugins()
    {
        $Result = array();
        foreach (self::$AddonClasses as $Obj)
        {
            $saPages = $Obj->GetPagesForSitemap();
            if (isset($saPages)&&(count($saPages)>0))
                $Result = array_merge($Result, $saPages);
        }

        return $Result;
    }

    public static function GetCurrentCHPULinkForPage()
    {
        $Result = array();
        foreach (self::$AddonClasses as $Obj)
        {
            $sLink = $Obj->GetCurrentCHPULink();
            if (isset($sLink)&&$sLink!="") $Result[] = $sLink;
        }

        if (count($Result)==1)
            return $Result[0];
        else if (count($Result)>1)
        {
            throw new Exception('Ошибка. Не может быть несколько разных ссылок ЧПУ у страницы');
            // Ошибка. Не может быть несколько разных ссылок ЧПУ у страницы
            return "";
        }
        else return "";
    }

    // отдает сразу все массивы с данными о том какие строки кода нужно включить
    // в блок head, в начале и конце блока body суммарно от всех плагинов
    public static function GetSummaryCodeForMainHTMLTags(&$HeadBlock, &$BeginBodyBlock, &$EndBodyBlock)
    {
        foreach (self::$AddonClasses as $Obj)
        {
            $Head = null;
            $BeginBody = null;
            $EndBody = null;
            if (file_exists(PLUGINS_CODE_DIRECORY.$Obj->sDirectoryName.PLUGINS_TEMPLATE_HEAD_BLOCK))
                $Head       = file(PLUGINS_CODE_DIRECORY.$Obj->sDirectoryName.PLUGINS_TEMPLATE_HEAD_BLOCK);

            if (file_exists(PLUGINS_CODE_DIRECORY.$Obj->sDirectoryName.PLUGINS_TEMPLATE_BEGIN_BODY_BLOCK))
                $BeginBody  = file(PLUGINS_CODE_DIRECORY.$Obj->sDirectoryName.PLUGINS_TEMPLATE_BEGIN_BODY_BLOCK);

            if (file_exists(PLUGINS_CODE_DIRECORY.$Obj->sDirectoryName.PLUGINS_TEMPLATE_END_BODY_BLOCK))
                $EndBody    = file(PLUGINS_CODE_DIRECORY.$Obj->sDirectoryName.PLUGINS_TEMPLATE_END_BODY_BLOCK);

            if (isset($Head)&&(count($Head)>0)) $HeadBlock = array_merge($HeadBlock, $Head);
            if (isset($BeginBody)&&(count($BeginBody)>0)) $BeginBodyBlock = array_merge($BeginBodyBlock, $BeginBody);
            if (isset($EndBody)&&(count($EndBody)>0)) $EndBodyBlock = array_merge($EndBodyBlock, $EndBody);
        }

        return true;
    }

    public static function GetConnectedPropertyValue(
        $sParamName,
        $sParamValue = null,
        $sHTMLTagId = '',
        $sTableName = '',
        $pTableRow = null)
    {
        $Result = array();

        foreach (self::$AddonClasses as $Obj)
        {
            $pData = $Obj->GetConnectedPropertyValue($sParamName, $sParamValue, $sHTMLTagId, $sTableName, $pTableRow);
            if (isset($pData)&&is_array($pData))
                $Result = array_merge($Result, $pData);
        }

        return $Result;
    }

    public static function OnEvent($sEventType, $pEventData = null)
    {
        foreach (self::$AddonClasses as $Obj)
            $Obj->OnEvent($sEventType, $pEventData);

        return true;
    }
}

?>