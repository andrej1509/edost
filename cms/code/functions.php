<?

/**
 * Class CCMS
 */
    class CCMS
    {
        /** @var CCMSDatabaseLog $Log */
        public static $Log = null;
        /** @var CCMSDatabase $DB */
        public static $DB = null;
        public static $SETS = null;
        public static $VARS = null;
        public static $currentProduct = null;
        public static $currentCategory = null;
        public static $sAllowedCategoryFilter = null;
        public static $sDomainName = "";
        public static $productDisplayedProperties = null;
        public static $arrUsableProductList= null;
        public static $sUsableProductList= null;
        public static $sHttpPrefix = 'http://';
        public static $bDesctopSiteVersion = true;

        // название акции для клиентов
        public static $sClientActionName = null;

        private static $bIsInitialized = false;
        /**
         *
         */
        public static function Initialize()
        {
            if (self::$bIsInitialized) return;
            // если имеем дело с переходом с реферального спама, то запрещаем доступ к сайту.
            if (self::CheckReferalSpam()==false) die;

            global $SETTINGS;

            CLog::Start();

            CUtils::RemoveSpecialCharsInGlobalArrays();
            CShopingCart::Initialize();
            self::$SETS = $SETTINGS;
            self::$VARS = array();
            self::$sDomainName = self::$SETS[SETTINGS_SECTION_GLOBAL][SETTINGS_DOMAIN_NAME];

            if (isset(self::$SETS[SETTINGS_SECTION_GLOBAL][SETTINGS_USE_HTTPS])
                &&(self::$SETS[SETTINGS_SECTION_GLOBAL][SETTINGS_USE_HTTPS]==1))
                self::$sHttpPrefix = 'https://';

            // если присутствует мобильная версия сайта, то переходим на нее
            if (isset(self::$SETS[SETTINGS_SECTION_GLOBAL][SETTINGS_MOBILE_SITE_VERSION_ENABLE])
                &&(self::$SETS[SETTINGS_SECTION_GLOBAL][SETTINGS_MOBILE_SITE_VERSION_ENABLE]==1))
            {
                if (strpos($_SERVER['REQUEST_URI'], 'payment_online')===false) // все платежи пока с основной версии сайта.
                {
                    $useragent=$_SERVER['HTTP_USER_AGENT'];
                    if(preg_match(
                            '/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i',$useragent)
                        ||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',
                            substr($useragent,0,4)))
                    {
                        //$sHost = str_replace('http://', '', $_SERVER['HTTP_REFERER']);
                        //$sHost = str_replace('https://', '', $sHost);
                        header('Location: https://m.'.self::$sDomainName.'/'.$_SERVER['REQUEST_URI']);
                    }
                }
            }

            if (strstr(self::$sDomainName, 'm.')!==false)
                self::$bDesctopSiteVersion = false;

            self::$DB = new CCMSDatabase();
            self::$Log = new CCMSDatabaseLog();
            // CCRMCookies::SaveCookies();

            if (isset($_GET['ProductId']))
            {
                self::$currentProduct = self::$DB->FindProductByID($_GET['ProductId']);
                self::$currentCategory = self::$DB->FindCategoryByID(CCMS::$currentProduct['SECTION_ID']);
            }

            if (!isset($_SESSION[UTM_CLIENT_ACTION]))
            {
                if (isset($_GET[UTM_CLIENT_ACTION])) {
                    $_SESSION[UTM_CLIENT_ACTION] = $_GET[UTM_CLIENT_ACTION];
                    self::$sClientActionName = $_GET[UTM_CLIENT_ACTION];
                }
            }
            else
                self::$sClientActionName = $_SESSION[UTM_CLIENT_ACTION];

            if (isset($_GET['CategoryId'])) CCMS::$currentCategory = self::$DB->FindCategoryByID($_GET['CategoryId']);

            if (isset(self::$SETS[SETTINGS_SECTION_PRODUCT][SETTINGS_PRODUCT_DISPLAYED_PROPERTIES]))
            {
                self::$productDisplayedProperties = explode(SETTINGS_INI_FILE_PARAM_DEVIDER,self::$SETS[SETTINGS_SECTION_PRODUCT][SETTINGS_PRODUCT_DISPLAYED_PROPERTIES]);
            }

            CBaseAddonClass::Initialize();

            self::$bIsInitialized = true;
        }

        public static function CheckReferalSpam()
        {
            /*
                 Для борьбу с реферальным спамом попробуем блокировать через php
                 так как через сервер не помогло.
                 Смотрим есть ли рефер
            */
            if(!empty($_SERVER['HTTP_REFERER']))
            {
                $badSite= array(
                    "traffic2money.com"
                    ,"traffic2money"
                    ,"best-seo-offer.com"
                    ,"buttons-for-website.com"
                    ,"buttons-for-your-website.com"
                    ,"trafficmonetizer.org"
                    ,"videos-for-your-business.com"
                    ,"webmonetizer.net"
                    ,"get-free-traffic-now.com"
                    ,"trafficmonetize.org"
                    ,"4webmasters.org"
                    ,"4webmasters"
                    ,"make-money-online.7makemoneyonline.com"
                    ,"100dollars-seo.com"
                    ,"success-seo.com"
                    ,"trafficmonetizer.org"
                    ,"videos-for-your-business.com"
                    ,"webmonetizer.net"
                    );
                $blockRefer=false;
                $r_s='';
                foreach($badSite as $key)
                {
                    if(!strripos($_SERVER['HTTP_REFERER'],$key)=== false)
                    {$r_s=$key;$blockRefer=true;break;}
                }
                if($blockRefer)
                {
                    echo '
                        <style>
                           body {#fff;font-family: "Segoe UI", "Helvetica Neue", Helvetica, sans-serif;}
                           .link{color:#00A98E;font-size: 1.1em;}
                           .link:hover{text-decoration:none;}
                        </style>
                        <div style="max-width: 500px;margin: 0 auto;">
                            <table>
                                <tr>
                                    <td><p>Похоже Вы зашли через реферальный спам.<BR><BR><BR>
                                    Пожалуйста, перейдите на сайт <a class="link" href="https://'.self::$sDomainName.'" >'.self::$sDomainName.'</a>
                                    </p></td>
                                </tr>
                            </table>
                        </div>';

                    return false;
                }
            }
            return true;
        }

        public static function SetUsableProductList($arrProductList)
        {
            if (is_array($arrProductList))
            {
                self::$arrUsableProductList = $arrProductList;
                self::$sUsableProductList = implode("," ,$arrProductList);
            }

        }
    }

    /**
     * Class CCMSDatabase
     */

    class CCMSDatabase
    {
        /**
         * @var Database_Mysql
         */
        public $bEnable = false;
        public $dbConnection = null;
        public $sDatabaseName = '';
        public $nRootCategoryId = 0;

        const sCreateTableCateroryMoreInfo = "
                                CREATE TABLE ?s.crm_productsection_moreinfo (
                                    ID int(11) NOT NULL AUTO_INCREMENT,
                                    SECTION_ID int(11) DEFAULT NULL COMMENT 'Идентификатор категории - crm_productsection_list.ID',
                                    DESCRIPTION text DEFAULT NULL COMMENT 'Описание',
                                    IMAGE_PATH text DEFAULT NULL COMMENT 'Путь к изображению',
                                    SORT int(11) DEFAULT NULL COMMENT 'Сортировка',
                                    HTML_DESC_PATH text DEFAULT NULL COMMENT 'Путь к HTML-файлу, содержащему полное описание категории товара',
                                    PRIMARY KEY (ID)
                                )
                                ENGINE = INNODB
                                AUTO_INCREMENT = 1
                                CHARACTER SET utf8
                                COLLATE utf8_general_ci;
            ";

        public $sCategoryQuery = "
                    SELECT
                      crm_productsection_list.*,
                      IFNULL(crm_productsection_moreinfo.DESCRIPTION, '') AS 'DESCRIPTION',
                      IFNULL(crm_productsection_moreinfo.IMAGE_PATH, '') AS 'IMAGE_PATH',
                      IFNULL(crm_productsection_moreinfo.SORT, 999999) AS 'SORT',
                      IFNULL(crm_productsection_moreinfo.HTML_DESC_PATH, '') AS 'HTML_DESC_PATH'
                    FROM crm_productsection_list
                    LEFT JOIN crm_productsection_moreinfo
                      ON crm_productsection_moreinfo.SECTION_ID = crm_productsection_list.ID";

        function __construct()
        {
            if (isset(CCMS::$SETS[SETTINGS_SECTION_DATABASE][SETTINGS_DATABASE_MYSQL_SERVER])&&isset(CCMS::$SETS[SETTINGS_SECTION_DATABASE][SETTINGS_DATABASE_MYSQL_DATABASE]))
            {
                $this->bEnable = true;

                $this->sDatabaseName = CCMS::$SETS[SETTINGS_SECTION_DATABASE][SETTINGS_DATABASE_MYSQL_DATABASE];
                // Подключение к СУБД, выбор кодировки и базы данных.
                $this->dbConnection = \Database_Mysql::create(
                    CCMS::$SETS[SETTINGS_SECTION_DATABASE][SETTINGS_DATABASE_MYSQL_SERVER],
                    CCMS::$SETS[SETTINGS_SECTION_DATABASE][SETTINGS_DATABASE_MYSQL_DATABASE_LOGIN],
                    CCMS::$SETS[SETTINGS_SECTION_DATABASE][SETTINGS_DATABASE_MYSQL_DATABASE_PASSWORD])
                    ->setCharset('utf8')
                    ->setDatabaseName(CCMS::$SETS[SETTINGS_SECTION_DATABASE][SETTINGS_DATABASE_MYSQL_DATABASE]);

                $this->nRootCategoryId = isset(CCMS::$SETS[SETTINGS_SECTION_CATALOG][SETTINGS_CATALOG_ROOT_CATEGORY_ID])?
                    CCMS::$SETS[SETTINGS_SECTION_CATALOG][SETTINGS_CATALOG_ROOT_CATEGORY_ID]:0;

                if (!$this->IsTableExistsInDatabase('crm_productsection_moreinfo'))
                {
                    $dbResult = $this->dbConnection->query(CCMSDatabase::sCreateTableCateroryMoreInfo, $this->sDatabaseName);
                }

                if ($this->nRootCategoryId>0) {
                    $pChildCategories = array();
                    $this->GetAllChildCategoriesRecursive($this->nRootCategoryId, $pChildCategories);

                    $sFilter = "";
                    if (count($pChildCategories) > 0) {
                        foreach ($pChildCategories as $pCategory) {
                            $sFilter .= "," . $pCategory['ID'];
                        }
                        $sFilter = substr($sFilter, 1, strlen($sFilter));


                    }
                    else
                    {
                        $sFilter = $this->nRootCategoryId;
                    }

                    CCMS::$sAllowedCategoryFilter = $sFilter;
                }
            }
        }

        /**
         * @param $sTableName
         * @return bool
         */
        public function IsTableExistsInDatabase($sTableName)
        {
            if (isset($this->dbConnection)) {

                $Result = FALSE;
                $pTables = $this->dbConnection->query(
                    'SHOW TABLES FROM ?s WHERE tables_in_?s = "?s"',
                    $this->sDatabaseName,
                    $this->sDatabaseName,
                    $sTableName);

                while ($pData = $pTables->fetch_assoc())
                    $Result = TRUE;

                return $Result;
            }
            return false;
        }

        public function GetProductList()
        {
            if (isset($this->dbConnection)) {
                $sFilter = "";
                if (isset(CCMS::$sAllowedCategoryFilter))
                    $sFilter = " AND SECTION_ID IN (".CCMS::$sAllowedCategoryFilter.")";
                if (isset(CCMS::$sUsableProductList))
                    $sFilter .= " AND ID IN (".CCMS::$sUsableProductList.")";
                return $this->dbConnection->query("SELECT * FROM crm_product_list WHERE SORT>0 ".$sFilter." ORDER BY SORT")->fetch_assoc_array();
            }
            else
                return null;
        }

        /**
         * @param $ProductId
         * @return array|null
         */
        public function FindProductByID($ProductId)
        {
            if (!isset($ProductId)) return null;

            if (isset($this->dbConnection))
                return $this->dbConnection->query("SELECT * FROM crm_product_list where ID=?i", $ProductId)->fetch_assoc();
            else
                return null;
        }

        public function FindProductByIDs($arrProductIds)
        {
            if ((isset($this->dbConnection))&&(count($arrProductIds)>0))
                return $this->dbConnection->query("SELECT * FROM crm_product_list where ID in (?s) ORDER BY FIELD(crm_product_list.ID, ?s)"
                    , implode(",", $arrProductIds), implode(",", $arrProductIds))->fetch_assoc_array();
            else
                return null;
        }

        /** Возвращает строку с данными свойства товара из таблицы crm_product_fields по имени свойства.
         * @param $sPropertyName
         * @return array|null
         */
        public function GetProductPropertyInfo($sPropertyName)
        {
            if (isset($this->dbConnection))
                return $this->dbConnection->query("SELECT * FROM crm_product_fields where ParamName='?s'", $sPropertyName)->fetch_assoc();
            else
                return null;
        }

        /**
         * @param $CategoryId
         * @return array|null
         */
        public function FindProductsForCategoryID($CategoryId)
        {
            if (isset($this->dbConnection))
            {
                if (isset(CCMS::$sAllowedCategoryFilter))
                {
                    $aCategoryIds = explode(',', CCMS::$sAllowedCategoryFilter);
                    if (array_search($CategoryId, $aCategoryIds)!==false)
                        return $this->dbConnection->query("SELECT * FROM crm_product_list WHERE SECTION_ID=?i AND (SORT>0)  ORDER BY SORT", $CategoryId)->fetch_assoc_array();
                    else
                        return null;
                }
                else
                    return $this->dbConnection->query("SELECT * FROM crm_product_list WHERE SECTION_ID=?i AND (SORT>0) ORDER BY SORT", $CategoryId)->fetch_assoc_array();
            }
            else
                return null;
        }

        public function GetCategoryList()
        {
            if (isset($this->dbConnection)) {
                $sFilter = "";
                if (isset(CCMS::$sAllowedCategoryFilter))
                    $sFilter = "WHERE crm_productsection_list.ID IN (".CCMS::$sAllowedCategoryFilter.")";
                return $this->dbConnection->query($this->sCategoryQuery." ".$sFilter)->fetch_assoc_array();
            }
            else
                return null;
        }

        public function FindCategoryByID($CategoryId)
        {
            if (isset($this->dbConnection))
                return $this->dbConnection->query($this->sCategoryQuery." WHERE crm_productsection_list.ID=?i", $CategoryId)->fetch_assoc();
            else
                return null;
        }

        public function FindChildCategoriesForCategoryID($nCategoryId)
        {
            if (isset($this->dbConnection))
                return $this->dbConnection->query($this->sCategoryQuery." WHERE crm_productsection_list.SECTION_ID=?i", $nCategoryId)->fetch_assoc_array();
            else
                return null;
        }

        /**
         * @param $nCategoryId
         * @param $pChildCategories
         * @return bool
         */
        public function GetAllChildCategoriesRecursive($nCategoryId, &$pChildCategories)
        {
            $pCategories = $this->FindChildCategoriesForCategoryID($nCategoryId);
            if (isset($pCategories))
            {
                $pChildCategories = array_merge($pChildCategories, $pCategories);
                foreach ($pCategories as $pCategory)
                {
                    $this->GetAllChildCategoriesRecursive($pCategory['ID'], $pChildCategories);
                }
            }
            return true;
        }

        public function CreateProductsectionMoreInfoTable()
        {
            if (!$this->IsTableExistsInDatabase('crm_productsection_moreinfo'))
            {
                $dbResult = $this->dbConnection->query(CCMSDatabase::sCreateTableCateroryMoreInfo, $this->sDatabaseName);
            }

            $pCategories = $this->dbConnection->query("SELECT * FROM crm_productsection_list");

            while ($pData = $pCategories->fetch_assoc())
            {
                if (count($this->dbConnection->query("SELECT ID FROM crm_productsection_moreinfo WHERE SECTION_ID=?d", $pData['ID'])->fetch_assoc())==0)
                    $this->dbConnection->query(
                        "INSERT INTO crm_productsection_moreinfo VALUES(0,?d,'?s','/img/catalog/?d.jpg',0,'')"
                        ,$pData['ID']
                        ,$pData['NAME']
                        ,$pData['ID']
                    );
            }
        }
    }

    class CShopingCart
    {
        public static function Initialize()
        {
            //$_SESSION['aCart']['Products']= null;
            if (!isset($_SESSION['aCart']['Products']))
                $_SESSION['aCart']['Products'] = array();

            if (!isset($_SESSION['aCart']['fAdditionalCommonDiscount']))
                $_SESSION['aCart']['fAdditionalCommonDiscount'] = 0;
        }

        public static function GetProductTypeCount()
        {
            return count($_SESSION['aCart']['Products']);
        }

        public static function GetAllProductCount()
        {
            $fCount = 0;
            foreach ($_SESSION['aCart']['Products'] as $pProduct)
                $fCount += $pProduct['sCount'];

            return $fCount;
        }

        public static function GetAllProducts()
        {
            return $_SESSION['aCart']['Products'];
        }

        public static function DeleteAllProducts()
        {
            unset($_SESSION['aCart']['Products']);
            $_SESSION['aCart']['Products'] = array();
        }

        // Ищет товарс ID=$nProductId в корзине
        public static function IsProductInCart($nProductId)
        {
            return isset($_SESSION['aCart']['Products'][$nProductId]);
        }

        public static function AddProductFromAjaxQuery()
        {
            CUtils::RemoveSpecialCharsInGlobalArrays();

            $fCount = null;
            if (isset($_POST['count']))
                $fCount = floatval(str_replace(',','.', $_POST['count']));

            $pProductInfo = CCMS::$DB->FindProductByID($_POST['id']);

            if (isset($pProductInfo))
            {
                if (!isset($fCount))
                    $fCount = self::GetProductCount($_POST['id']);

                if (!isset($fCount))
                    return null;

                if ($fCount>0)
                {
                    $arrProductProperties = array();
                    // смотрим устанолены ли переменные для дополнительных свойств товаров
                    if (
                        isset($_POST['product_property_names_array']) &&
                        isset($_POST['product_property_values_array']) &&
                        count($_POST['product_property_names_array']) > 0 &&
                        count($_POST['product_property_values_array']) > 0 &&
                        (count($_POST['product_property_names_array']) == count($_POST['product_property_values_array']))
                    ) {
                        for ($i = 0; $i < count($_POST['product_property_names_array']); $i++) {
                            $arrProductProperties[$_POST['product_property_names_array'][$i]] = $_POST['product_property_values_array'][$i];
                        }
                    }

                    return CShopingCart::AddProduct($pProductInfo, $fCount, $arrProductProperties);
                }
                else {
                    CShopingCart::DeleteProduct($_POST['id']);
                }
            }
            return null;
        }

        public static function AddProductById($nProductId, $fCount, $arrProductProperties)
        {
            $pProductInfo = CCMS::$DB->FindProductByID($nProductId);
            return CShopingCart::AddProduct($pProductInfo, $fCount, $arrProductProperties);
        }

        public static function AddProduct($pProductInfo, $fCount, $arrProductProperties)
        {
            if (isset($pProductInfo))
            {
                $fProductDiscount = 0;
                if (
                    isset(CCMS::$SETS[SETTINGS_SECTION_PRODUCT][SETTINGS_PRODUCT_DISCOUNT_DB_FIELDNAME])&&
                    ($pProductInfo[CCMS::$SETS[SETTINGS_SECTION_PRODUCT][SETTINGS_PRODUCT_DISCOUNT_DB_FIELDNAME]] > 0)&&
                    ($pProductInfo[CCMS::$SETS[SETTINGS_SECTION_PRODUCT][SETTINGS_PRODUCT_DISCOUNT_DB_FIELDNAME]] < 100)
                )
                {
                    $fProductDiscount = $pProductInfo[CCMS::$SETS[SETTINGS_SECTION_PRODUCT][SETTINGS_PRODUCT_DISCOUNT_DB_FIELDNAME]];
                }

                if (isset($_SESSION['aCart']['Products'][$pProductInfo['ID']]))
                    $fProductDiscount = $_SESSION['aCart']['Products'][$pProductInfo['ID']]['nDiscount'];

                CCMS::$Log->OnPutProductInShoppingCart($pProductInfo, $arrProductProperties);

                $pProductInCart = $_SESSION['aCart']['Products'][$pProductInfo['ID']] = array(
                                 'sId' => $pProductInfo['ID']
                                ,'sName' => $pProductInfo['NAME']
                                ,'sDescription' => $pProductInfo['DESCRIPTION']
                                ,'sCount' => $fCount
                                ,'sPrice' => $pProductInfo['PRICE']
                                ,'nDiscount' => $fProductDiscount
                                ,'aProperties' => $arrProductProperties
                                ,'dbProductInfo' => $pProductInfo
                            );

                // сообщаем плагинам о добавлении элемента в корзину.
                CBaseAddonClass::OnEvent(PLG_EVENT_SHOPPING_CART_ADD_PRODUCT, $pProductInCart);

                return $pProductInCart;
            }
            else
                return null;
        }

        public static function DeleteProduct($nProductId)
        {
            unset($_SESSION['aCart']['Products'][$nProductId]);
        }

        // Ищет товарс ID=$nProductId в корзине
        public static function FindProductById($nProductId)
        {
            return $_SESSION['aCart']['Products'][$nProductId];
        }

        public static function GetProductCount($nProductId)
        {
            return isset($_SESSION['aCart']['Products'][$nProductId])?$_SESSION['aCart']['Products'][$nProductId]['sCount']:0;
        }

        public static function SetProductCount($nProductId, $fCount)
        {
            if (isset($_SESSION['aCart']['Products'][$nProductId])) {
                $_SESSION['aCart']['Products'][$nProductId] = $fCount;
                return true;
            }
            else
                return false;
        }

        public static function SetProductDiscount($nProductId, $fDiscount)
        {
            if (isset($_SESSION['aCart']['Products'][$nProductId])) {
                $_SESSION['aCart']['Products'][$nProductId]['nDiscount'] = $fDiscount;
                return true;
            }
            else
                return false;
        }

        /** Возвращает значение установленной общей скидки на все товары в %
         * @return mixed
         */
        public static function GetCommonDiscount()
        {
            return $_SESSION['aCart']['fCommonDiscount'];
        }
        /** Устанавливает единую скидку в % для всех товаров в корзине
         * Так как скидка по акции и скидка на товар не складывается, то происходит замена скидки товара
         * @param $fDiscount
         * @return bool
         */
        public static function SetCommonDiscount($fDiscount)
        {
            $_SESSION['aCart']['fCommonDiscount'] = $fDiscount;

            foreach ($_SESSION['aCart']['Products'] as $pProduct)
                $_SESSION['aCart']['Products'][$pProduct['sId']]['nDiscount'] = $fDiscount;

            return true;
        }

        public static function GetAdditionalCommonDiscount()
        {
            return $_SESSION['aCart']['fAdditionalCommonDiscount'];
        }

        /** Устанавливает дополнительную скидку для всей корзины
         * В последствии она просто добавляется к выводу скидки.
         * Обычно это акционная или иная скидка.
         * @param $fDiscount значение скидки в денежном выражении
         */
        public static function SetAdditionalCommonDiscount($fDiscount)
        {
            $_SESSION['aCart']['fAdditionalCommonDiscount'] = $fDiscount;
        }

        public static function GetProductPrice($nProductId)
        {
            $pProductInfo = CShopingCart::FindProductById($nProductId);
            if (isset($pProductInfo))
                return $pProductInfo['sPrice'];
            else
                return 0;
        }

        public static function GetProductPriceWithDiscount($nProductId)
        {
            $pProductInfo = CShopingCart::FindProductById($nProductId);
            if (isset($pProductInfo))
                return $pProductInfo['sPrice'] * (1-$pProductInfo['nDiscount']/100);
            else
                return 0;
        }

        public static function GetProductTotalCost($nProductId)
        {
            $pProductInfo = CShopingCart::FindProductById($nProductId);
            if (isset($pProductInfo))
                return $pProductInfo['sPrice'] * $pProductInfo['sCount'];
            else
                return 0;
        }

        public static function GetProductTotalCostWithDiscount($nProductId)
        {
            $pProductInfo = CShopingCart::FindProductById($nProductId);
            if (isset($pProductInfo))
                return $pProductInfo['sPrice'] * $pProductInfo['sCount'] * (1-$pProductInfo['nDiscount']/100);
            else
                return 0;
        }

        public static function GetCartScore()
        {
            $fScore = 0;
            foreach ($_SESSION['aCart']['Products'] as $pProduct)
                $fScore += $pProduct['sPrice'] * $pProduct['sCount'];

            return $fScore;
        }

        public static function GetCartScoreWithDiscount()
        {
            $fScore = 0;
            foreach ($_SESSION['aCart']['Products'] as $pProduct)
                $fScore += $pProduct['sPrice'] * $pProduct['sCount'] * (1-$pProduct['nDiscount']/100);

            return $fScore;
        }

        public static function GetCartScoreDiscount()
        {
            $fScore = 0;
            foreach ($_SESSION['aCart']['Products'] as $pProduct)
                $fScore += $pProduct['sPrice'] * $pProduct['sCount'] * ($pProduct['nDiscount']/100);

            $fScore += $_SESSION['aCart']['fAdditionalCommonDiscount'];

            return $fScore;
        }

        public static function GetProductIdArray()
        {
            $sIDs = "";
            foreach ($_SESSION['aCart']['Products'] as $pProduct)
                $sIDs .= $pProduct['sId'].',';
            $sIDs = substr($sIDs, 0, strlen($sIDs)-1);

            return $sIDs;
        }

        public static function GetProductsPropertiesAsText($nProductId)
        {
            $sResult = '';
            $pProduct = CShopingCart::FindProductById($nProductId);
            if (isset($pProduct))
            {
                // добавляем к описанию выбранные свойства товара.
                if (isset($pProduct['aProperties']))
                    foreach ($pProduct['aProperties'] as $PropertyName => $Value)
                    {
                        if (isset(CCMS::$SETS[SETTINGS_SECTION_PRODUCT][SETTINGS_PRODUCT_USE_PROPERTY1_AS_PRODUCT_VIEW])
                            && (CCMS::$SETS[SETTINGS_SECTION_PRODUCT][SETTINGS_PRODUCT_USE_PROPERTY1_AS_PRODUCT_VIEW] == 1)
                            && ($PropertyName == 'product_property_1')
                        )
                            continue;
                        $sResult .= ' ' . $Value;
                    }

            }

            return $sResult;
        }

        public static function GetCartProductsAsText()
        {
            $arrProducts = array();

            if (CShopingCart::GetAllProductCount() > 0)
            {
                foreach ($_SESSION['aCart']['Products'] as $pProduct)
                {
                    $sProduct = self::GetProductsPropertiesAsText($pProduct['sId']);
                    $sProduct .= ' '.$pProduct['sCount'].' ед.';
                    $pRealProduct = CCMS::$DB->FindProductByID(self::GetProductIdForLead($pProduct['sId']));
                    $arrProducts[] = $pRealProduct['NAME'].$sProduct;
                }
            }

            if (count($arrProducts)>0)
                return implode('; ', $arrProducts);
            else
                return "";
        }

        // выдает список товаров в корзине для письма лида
        public static function GetCartProductsAsIdText()
        {
            $arrProducts = array();

            if (CShopingCart::GetAllProductCount() > 0)
            {
                foreach ($_SESSION['aCart']['Products'] as $pProduct)
                {
                    $arrProducts[] = 'product_' . self::GetProductIdForLead($pProduct['sId']) . ':' . $pProduct['sCount'];
                }
            }

            if (count($arrProducts)>0)
                return implode(', ', $arrProducts);
            else
                return "";
        }

        // если есть товары с представлениями, то выдается выбранный идентификатор представления товара
        public static function GetProductIdForLead($nProductId)
        {
            $nRealProductId = 0;
            $oProduct = self::FindProductById($nProductId);
            if (isset($oProduct))
            {
                $nRealProductId = $nProductId;
                if (isset(CCMS::$SETS[SETTINGS_SECTION_PRODUCT][SETTINGS_PRODUCT_USE_PROPERTY1_AS_PRODUCT_VIEW])
                    && CCMS::$SETS[SETTINGS_SECTION_PRODUCT][SETTINGS_PRODUCT_USE_PROPERTY1_AS_PRODUCT_VIEW] == 1
                )
                    if (isset($oProduct['aProperties']['product_property_1']))
                        $nRealProductId = $oProduct['aProperties']['product_property_1'];
                    else
                        $nRealProductId = $nProductId;
            }
            return $nRealProductId;
        }
    }

    // Класс для работы с куками
    class CCRMCookies
    {
        const sCookieLiveTime = 2592000;

        public static function LoadCookies()
        {
            $_SESSION[COOKIE_PARAM_CLIENT_DATA] = array();
            /*
            if (isset($_COOKIE[COOKIE_PARAM_SHOPPING_CART]))
            {
                $aCookieShopingCart = unserialize($_COOKIE[COOKIE_PARAM_SHOPPING_CART]);
                if (isset($aCookieShopingCart))
                    $_SESSION['aCart']['Products'] = $aCookieShopingCart;
            }

            if (isset($_COOKIE[COOKIE_PARAM_CLIENT_DATA]))
            {
                $aCookieClientData = unserialize($_COOKIE[COOKIE_PARAM_CLIENT_DATA]);
                if (isset($aCookieClientData))
                    $_SESSION[COOKIE_PARAM_CLIENT_DATA] = $aCookieClientData;
            }
            */
        }

        public static function SaveCookiesOld()
        {
            if (isset($_SESSION[COOKIE_PARAM_CLIENT_DATA][COOKIE_PARAM_HTTP_REFERER])) return true;

            $_SESSION[COOKIE_PARAM_CLIENT_DATA][COOKIE_PARAM_HTTP_REFERER] = $_SERVER['HTTP_REFERER'];

            if (!isset($_SESSION[COOKIE_PARAM_CLIENT_DATA][COOKIE_PARAM_UTM_REGION]))
                $_SESSION[COOKIE_PARAM_CLIENT_DATA][COOKIE_PARAM_UTM_REGION] = isset($_GET['utm_region_name'])?$_GET['utm_region_name']:'';
            /////////////////////////////////////////////////////////////
            // Сохраняем основные куки
            if (!isset($_COOKIE[COOKIE_PARAM_FIRST_HTTP_REFERER]))
                setcookie(COOKIE_PARAM_FIRST_HTTP_REFERER, $_SERVER['HTTP_REFERER'], time() + CCRMCookies::sCookieLiveTime);

            if (!isset($_COOKIE[COOKIE_PARAM_FIRST_SOURCE_SITE]))
            {
                if (isset($_GET['utm_source']))
                    setcookie(COOKIE_PARAM_FIRST_SOURCE_SITE, $_COOKIE['utm_source'], time() + CCRMCookies::sCookieLiveTime);
                else
                    setcookie(COOKIE_PARAM_FIRST_HTTP_REFERER, '', time() + CCRMCookies::sCookieLiveTime);

                //$arrUTMParams = array();
                //parse_str(substr(strstr($_COOKIE[COOKIE_PARAM_FIRST_HTTP_REFERER],'?'), 1), $arrUTMParams);

                setcookie(
                    COOKIE_PARAM_FIRST_UTM_SOURCE,
                    isset($_GET['utm_source'])?$_GET['utm_source']:'',
                    time() + CCRMCookies::sCookieLiveTime);
                setcookie(
                    COOKIE_PARAM_FIRST_UTM_MEDIUM,
                    isset($_GET['utm_medium'])?$_GET['utm_medium']:'',
                    time() + CCRMCookies::sCookieLiveTime);
                setcookie(
                    COOKIE_PARAM_FIRST_UTM_CAMPAIGN,
                    isset($_GET['utm_campaign'])?$_GET['utm_campaign']:'',
                    time() + CCRMCookies::sCookieLiveTime);
                setcookie(
                    COOKIE_PARAM_FIRST_UTM_CONTENT,
                    isset($_GET['utm_content'])?$_GET['utm_content']:'',
                    time() + CCRMCookies::sCookieLiveTime);
                setcookie(
                    COOKIE_PARAM_FIRST_UTM_TERM,
                    isset($_GET['utm_keyword'])?$_GET['utm_keyword']:'',
                    time() + CCRMCookies::sCookieLiveTime);

                setcookie(
                    COOKIE_PARAM_FIRST_UTM_REGION,
                    isset($_GET['utm_region_name'])?$_GET['utm_region_name']:'',
                    time() + CCRMCookies::sCookieLiveTime);
            }

            setcookie(COOKIE_PARAM_HTTP_REFERER, $_SERVER['HTTP_REFERER'], time() + CCRMCookies::sCookieLiveTime);

            if (isset($_GET['utm_source']))
            {
                setcookie(COOKIE_PARAM_SOURCE_SITE, $_COOKIE['utm_source'], time() + CCRMCookies::sCookieLiveTime);
            }

            //$arrUTMParams = array();
            //parse_str(substr(strstr($_COOKIE[COOKIE_PARAM_HTTP_REFERER],'?'), 1), $arrUTMParams);

            setcookie(
                COOKIE_PARAM_UTM_SOURCE,
                isset($_GET['utm_source'])?$_GET['utm_source']:'',
                time() + CCRMCookies::sCookieLiveTime);
            setcookie(
                COOKIE_PARAM_UTM_MEDIUM,
                isset($_GET['utm_medium'])?$_GET['utm_medium']:'',
                time() + CCRMCookies::sCookieLiveTime);
            setcookie(
                COOKIE_PARAM_UTM_CAMPAIGN,
                isset($_GET['utm_campaign'])?$_GET['utm_campaign']:'',
                time() + CCRMCookies::sCookieLiveTime);
            setcookie(
                COOKIE_PARAM_UTM_CONTENT,
                isset($_GET['utm_content'])?$_GET['utm_content']:'',
                time() + CCRMCookies::sCookieLiveTime);
            setcookie(
                COOKIE_PARAM_UTM_TERM,
                isset($_GET['utm_keyword'])?$_GET['utm_keyword']:'',
                time() + CCRMCookies::sCookieLiveTime);
            setcookie(
                COOKIE_PARAM_UTM_REGION,
                isset($_GET['utm_region_name'])?$_GET['utm_region_name']:'',
                time() + CCRMCookies::sCookieLiveTime);
        }

        public static function SaveCookies()
        {
            /*Текущий refer*/
            if(empty($_COOKIE[COOKIE_PARAM_HTTP_REFERER])||$_COOKIE[COOKIE_PARAM_HTTP_REFERER]!=$_SERVER['HTTP_REFERER'])
            {
                $s1= CCMS::$sHttpPrefix.$_SERVER['HTTP_HOST'];
                $s2=substr($_SERVER['HTTP_REFERER'],0,strlen($s1));
                if((!empty($s1)&&!empty($s2)&&$s1!=$s2))
                {
                    setcookie(COOKIE_PARAM_HTTP_REFERER,$_SERVER['HTTP_REFERER']);
                }
            }
            /*Текущий utm_source*/
            if(isset($_GET['source'])||isset($_GET['utm_source'])){
                $_COOKIE[COOKIE_PARAM_SOURCE_SITE]=$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
                setcookie(COOKIE_PARAM_SOURCE_SITE,$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);

                $_COOKIE[COOKIE_PARAM_UTM_SOURCE] = isset($_GET['utm_source'])?$_GET['utm_source']:'';
                $_COOKIE[COOKIE_PARAM_UTM_MEDIUM] = isset($_GET['utm_medium'])?$_GET['utm_medium']:'';
                $_COOKIE[COOKIE_PARAM_UTM_CAMPAIGN] = isset($_GET['utm_campaign'])?$_GET['utm_campaign']:'';
                $_COOKIE[COOKIE_PARAM_UTM_CONTENT] = isset($_GET['utm_content'])?$_GET['utm_content']:'';
                $_COOKIE[COOKIE_PARAM_UTM_TERM] = isset($_GET['utm_term'])?$_GET['utm_term']:'';
                $_COOKIE[COOKIE_PARAM_UTM_REGION] = isset($_GET['utm_region_name'])?$_GET['utm_region_name']:'';

                setcookie(
                    COOKIE_PARAM_UTM_SOURCE,
                    isset($_GET['utm_source'])?$_GET['utm_source']:'',
                    time() + CCRMCookies::sCookieLiveTime);
                setcookie(
                    COOKIE_PARAM_UTM_MEDIUM,
                    isset($_GET['utm_medium'])?$_GET['utm_medium']:'',
                    time() + CCRMCookies::sCookieLiveTime);
                setcookie(
                    COOKIE_PARAM_UTM_CAMPAIGN,
                    isset($_GET['utm_campaign'])?$_GET['utm_campaign']:'',
                    time() + CCRMCookies::sCookieLiveTime);
                setcookie(
                    COOKIE_PARAM_UTM_CONTENT,
                    isset($_GET['utm_content'])?$_GET['utm_content']:'',
                    time() + CCRMCookies::sCookieLiveTime);
                setcookie(
                    COOKIE_PARAM_UTM_TERM,
                    isset($_GET['utm_term'])?$_GET['utm_term']:'',
                    time() + CCRMCookies::sCookieLiveTime);
                setcookie(
                    COOKIE_PARAM_UTM_REGION,
                    isset($_GET['utm_region_name'])?$_GET['utm_region_name']:'',
                    time() + CCRMCookies::sCookieLiveTime);
            }

            /*Узнаём новый ли посетитель и Refer*/
            if(empty($_COOKIE[COOKIE_PARAM_FIRST_HTTP_REFERER]))
            {
                /*Рефер не с этого же домена*/
                $s1= CCMS::$sHttpPrefix.$_SERVER['HTTP_HOST'];
                $s2=substr($_SERVER['HTTP_REFERER'],0,strlen($s1));
                if( (!empty($s1)&&!empty($s2)&&$s1!=$s2)|| !empty($_GET['utm_source']))
                {
                    /*Есть ли GET[utm_source]*/
                    if(isset($_GET['utm_source']))
                    {
                        $_SESSION['utm'] = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
                        $_COOKIE[COOKIE_PARAM_FIRST_SOURCE_SITE]=$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
                        setcookie(COOKIE_PARAM_FIRST_SOURCE_SITE,$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'],time() + CCRMCookies::sCookieLiveTime);


                        $_COOKIE[COOKIE_PARAM_FIRST_UTM_SOURCE] = isset($_GET['utm_source'])?$_GET['utm_source']:'';
                        $_COOKIE[COOKIE_PARAM_FIRST_UTM_MEDIUM] = isset($_GET['utm_medium'])?$_GET['utm_medium']:'';
                        $_COOKIE[COOKIE_PARAM_FIRST_UTM_CAMPAIGN] = isset($_GET['utm_campaign'])?$_GET['utm_campaign']:'';
                        $_COOKIE[COOKIE_PARAM_FIRST_UTM_CONTENT] = isset($_GET['utm_content'])?$_GET['utm_content']:'';
                        $_COOKIE[COOKIE_PARAM_FIRST_UTM_TERM] = isset($_GET['utm_term'])?$_GET['utm_term']:'';
                        $_COOKIE[COOKIE_PARAM_FIRST_UTM_REGION] = isset($_GET['utm_region_name'])?$_GET['utm_region_name']:'';

                        setcookie(
                            COOKIE_PARAM_FIRST_UTM_SOURCE,
                            isset($_GET['utm_source'])?$_GET['utm_source']:'',
                            time() + CCRMCookies::sCookieLiveTime);
                        setcookie(
                            COOKIE_PARAM_FIRST_UTM_MEDIUM,
                            isset($_GET['utm_medium'])?$_GET['utm_medium']:'',
                            time() + CCRMCookies::sCookieLiveTime);
                        setcookie(
                            COOKIE_PARAM_FIRST_UTM_CAMPAIGN,
                            isset($_GET['utm_campaign'])?$_GET['utm_campaign']:'',
                            time() + CCRMCookies::sCookieLiveTime);
                        setcookie(
                            COOKIE_PARAM_FIRST_UTM_CONTENT,
                            isset($_GET['utm_content'])?$_GET['utm_content']:'',
                            time() + CCRMCookies::sCookieLiveTime);
                        setcookie(
                            COOKIE_PARAM_FIRST_UTM_TERM,
                            isset($_GET['utm_term'])?$_GET['utm_term']:'',
                            time() + CCRMCookies::sCookieLiveTime);

                        setcookie(
                            COOKIE_PARAM_FIRST_UTM_REGION,
                            isset($_GET['utm_region_name'])?$_GET['utm_region_name']:'',
                            time() + CCRMCookies::sCookieLiveTime);
                    }

                    $sRefer=" ".$_SERVER['HTTP_REFERER'];
                    $_SESSION['Refer']=$sRefer;

                    switch($_SERVER['HTTP_HOST'])
                    {
                        case "www.".CCMS::$sDomainName:
                        case CCMS::$sDomainName:
                            if(isset($_GET['utm_source']))
                                setcookie(COOKIE_PARAM_FIRST_SOURCE_SITE,$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'],time() + CCRMCookies::sCookieLiveTime,"/",".".CCMS::$sDomainName);
                            setcookie(COOKIE_PARAM_FIRST_HTTP_REFERER,$sRefer,time() + CCRMCookies::sCookieLiveTime,"/",".".CCMS::$sDomainName);
                            setcookie(COOKIE_PARAM_FIRST_VISIT_DATE,date("Y-m-d H:i:s"),time() + CCRMCookies::sCookieLiveTime,"/",".".CCMS::$sDomainName);
                            break;
                    }
                }
            }

            if (!isset($_SESSION['COOKIES']))
                $_SESSION['COOKIES'] = $_COOKIE;
        }


    }

    class CUtils
    {
        public static function RemoveSpecialCharsInGlobalArrays()
        {
            global $_POST, $_GET, $_COOKIE;

            foreach($_POST as $value) {
                if (!is_array($value))
                    $value = htmlspecialchars($value);

            }
            foreach($_GET as $value)	$value=htmlspecialchars($value);
            foreach($_COOKIE as $value)	$value=htmlspecialchars($value);

            return true;
        }

        public static function NormalizePhoneNumber($sPhone)
        {
            $resPhone = preg_replace("/[^0-9]/", "", $sPhone);

            if (strlen($resPhone) === 11) {
                $resPhone = preg_replace("/^7/", "8", $resPhone);
            }
            return $resPhone;
        }

        /**
         *
         */
        public static function IncreaseAllProductImageNumbers($nCount = 1, $bUseProductWithNamesDirectory = false)
        {
            $ProductList = CCMS::$DB->GetProductList();

            $sPathProductWithNames = $_SERVER['DOCUMENT_ROOT'].PRODUCTS_INFO_WITH_NAMES_PATH;

            foreach ($ProductList as $pProductInfo)
            {
                if ($bUseProductWithNamesDirectory===false)
                    $sProductDir = $_SERVER['DOCUMENT_ROOT'] . GetProductDirectoryByCategoryAndProductId($pProductInfo['SECTION_ID'], $pProductInfo['ID']);
                else
                    $sProductDir = $sPathProductWithNames.GetCHPULinkForProduct($pProductInfo, true);

                $sProductDir .= 'img/';

                if (is_dir($sProductDir))
                {
                    $aImgDirectories = array('big', 'small', 'mid', 'origin');

                    foreach ($aImgDirectories as $sImgDirectory)
                    {
                        $sDir = $sProductDir.$sImgDirectory.'/';

                        $aDirectories = scandir($sDir, SCANDIR_SORT_DESCENDING);
                        if (count($aDirectories)>0) {
                            foreach ($aDirectories as $sPath) {
                                if (($sPath=='..') || ($sPath=='.')|| ($sPath=='desctop.ini')) continue;

                                if (is_file($sDir.$sPath))
                                {
                                    $PathInfo = pathinfo($sDir.$sPath);

                                    $sFileNumber = str_replace('0', '', $PathInfo['filename']);
                                    $sFileNumber = (int) $sFileNumber;
                                    //if (is_numeric($sFileNumber))
                                    rename($sDir.$sPath, $sDir.sprintf("%'.04d", $sFileNumber+$nCount).'.'.$PathInfo['extension']);
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    class CLog
    {
    // use STATIC rendering
        static private $fplog;	// file handler for logging

        // open logging file for writing
        static public function Start($flogname = '') {
            if ($flogname=='')
                $flogname = CMS_FILE_LOCAL_PATH_PHP.'log.txt';
            self::$fplog = fopen($flogname,'ab');
        }

        static public function Stop() {
            fclose(self::$fplog);
        }

        static function GetCallingClass()
        {

            //get the trace
            $trace = debug_backtrace();

            // Get the class that is asking for who awoke it

            $class = $trace[1]['class'];
            // +1 to i cos we have to account for calling this function
            for ( $i=1; $i<count( $trace ); $i++ ) {
                if ( isset( $trace[$i] ) ) // is it set?
                    if ( $class != $trace[$i]['class'] ) // is it a different class
                    {
                        return $trace[$i]['class'];
                    }
            }

            return '';
        }

        static public function WriteData($Data, $sClassName = '', $usedate = true)
        {
            if ($sClassName=='')
                $sClassName = self::GetCallingClass();

            self::Write(implode('', print_r($Data, true)), $sClassName, $usedate);
        }

        static public function Write($sMessage, $sClassName = '', $usedate = true)
        {
            if ($sClassName=='')
                $sClassName = self::GetCallingClass();

            // пишем в лог-файл строку $s,
            // $usedate - вставлять ли в лог дату/время текущие
            $tim = '';
            if($usedate)
                $tim =
                    '['.date('Y-m-d H:i:s').'] SESSION_ID:'.session_id().' MESSAGE '.$sClassName.': ';

            fwrite(self::$fplog,$tim.$sMessage."\r\n");

            self::Stop();
        }

        static public function WriteError($sMessage, $usedate = true)
        {
            $sClassName = self::GetCallingClass();
            if (!isset($sClassName)) $sClassName = 'Undefined Class';

            if (isset($Object))
                self::Write('ERROR '.get_class($Object).': '.$sMessage, $sClassName, $usedate);
            else
                self::Write('ERROR : '.$sMessage, $sClassName, $usedate);
        }
    }

    class MailAgent
    {
        private $_SMTPServer = '';
        private $_SMTPLogin = '';
        private $_SMTPPass = '';
        private $_mail = null;
        private $_mailFrom = '';

        private function initMailAgent()
        {
            if (
                isset(CCMS::$SETS[SETTINGS_SECTION_SMTP][SETTINGS_SMTP_SERVER])&&
                isset(CCMS::$SETS[SETTINGS_SECTION_SMTP][SETTINGS_SMTP_LOGIN])&&
                isset(CCMS::$SETS[SETTINGS_SECTION_SMTP][SETTINGS_SMTP_PASSWORD])
            )
            {

                $this->_SMTPServer = CCMS::$SETS[SETTINGS_SECTION_SMTP][SETTINGS_SMTP_SERVER];
                $this->_SMTPLogin = CCMS::$SETS[SETTINGS_SECTION_SMTP][SETTINGS_SMTP_LOGIN];
                $this->_SMTPPass = CCMS::$SETS[SETTINGS_SECTION_SMTP][SETTINGS_SMTP_PASSWORD];
                $this->_mailFrom = CCMS::$SETS[SETTINGS_SECTION_SMTP][SETTINGS_SMTP_LOGIN];

                $this->_mail  = new PHPMailer();
                // Устанавливаем, что наши сообщения будет идти через
                // SMTP сервер
                $this->_mail->IsSMTP();

                // Можно раскомментировать след. строчку для отладки
                // 1 = Ошибки и сообщения
                // 2 = Только сообщения
                //$mail->SMTPDebug  = 2;

                // Включение SMTP аутентификации
                // Большинство серверов ее требуют
                $this->_mail->SMTPAuth   = true;
                // SMTP Сервер отправки сообщений
                $this->_mail->Host       = $this->_SMTPServer;
                // Порт сервера (чаще всего 25)
                $this->_mail->Port       = 465;
                // SMTP Логин для авториации
                $this->_mail->Username   = $this->_SMTPLogin;
                // SMTP Пароль для авторизации
                $this->_mail->Password   = $this->_SMTPPass;
                // Enable TLS encryption, `ssl` also accepted
                //$this->_mail->SMTPSecure='tls';
                $this->_mail->SMTPSecure='ssl';
                // Кодировка сообщения
                $this->_mail->CharSet    = 'utf-8';

                return true;
            }
            else
                return false;
        }

        public function sendMail( $address, $subject, $body, $from='' )
        {
            if ($this->_mail == null) {
                $this->initMailAgent();
            }

            // Устанавливаем от кого будет уходить почта
            $this->_mail->SetFrom($from=='' ? $this->_mailFrom : $from);
            // Устанавливаем заголовк письма
            $this->_mail->Subject    = $subject;
            // Текст сообщения
            $this->_mail->MsgHTML($body);

            if (is_array($address)) {
                // Отправка сообщений сразу нескольким пользователям
                foreach($address as $value) {
                    $this->_mail->AddAddress($value);
                }
            } else {
                // Адрес получателя. Второй параметр - имя получателя (не обязательно)
                $this->_mail->AddAddress($address);
            }
            // Отправляем сообщение
            return $this->_mail->Send();
        }

    }

	setlocale(LC_ALL, 'ru_RU');

	require_once ($_SERVER['DOCUMENT_ROOT'].'/cms/code/settings.php');
    require_once (CMS_FILE_LOCAL_PATH_PHP.'defines.php');

    include(CMS_FILE_LOCAL_PATH_PHP.'Database/Mysql.php');
    include(CMS_FILE_LOCAL_PATH_PHP.'Database/Mysql/Exception.php');
    include(CMS_FILE_LOCAL_PATH_PHP.'Database/Mysql/Statement.php');

    include(CMS_FILE_LOCAL_PATH_PHP.'plugin_base_class.php');
    include(CMS_FILE_LOCAL_PATH_PHP.'history_data.php');

    @session_start();


    CCMS::Initialize();



    function ToTranslit($text)
    {
        $find=array('А','а','Б','б','В','в','Г','г','Д','д','Е','е','Ё','ё',
            'Ж','ж','З','з','И','и','Й','й','К','к','Л','л','М','м',
            'Н','н','О','о','П','п','Р','р','С','с','Т','т','У','у',
            'Ф','ф','Х','х','Ц','ц','Ч','ч','Ш','ш','Щ','щ','Ъ','ъ',
            'Ы','ы','Ь','ь','Э','э','Ю','ю','Я','я', '№',' - ', ' ', '/','.', ',');

        $replace=array('A','a','B','b','V','v','G','g','D','d','E','e','Yo','yo',
            'Zh','zh','Z','z','I','i','J','j','K','k','L','l','M','m',
            'N','n','O','o','P','p','R','r','S','s','T','t','U','u','F',
            'f','H','h','Ts','ts','Ch','ch','Sh','sh','Sch','sch',
            '','','Y','y','','','E','e','Yu','yu','Ya','ya', '','-', '_', '-', '_', '_');

        $sResult = strtolower(preg_replace('/[^\w\d\s_-]*/','',str_replace($find,$replace,$text)));

        if ($sResult[strlen($sResult)-1]=='_')
            $sResult = substr($sResult, 0, strlen($sResult)-1);

        $sResult = str_replace('__','_',$sResult);

        return $sResult;
    }

    // функция для заполнения массивов с информацией о товаре на сайте
    //Создан дубликат по ID FillProductInfoArrays
    /**
     * @param $LandingTypeName
     * @return bool
     */

    function GetProductCategoryDirectoryById($nCategoryId)
    {
        return PRODUCTS_INFO_PATH."cat".$nCategoryId."/";
    }

    function GetProductDirectoryByCategoryAndProductId($nCategoryId, $nProductId)
    {
        return GetProductCategoryDirectoryById($nCategoryId)."prod".$nProductId.'/';
    }

    function GetProductImagePathByCategoryAndProductId($nCategoryId, $nProductId)
    {
        return GetProductDirectoryByCategoryAndProductId($nCategoryId, $nProductId).'img/';
    }

    function GetProductMainSmallImageByCategoryAndProductId($nCategoryId, $nProductId)
    {
        $sFileName = GetProductImagePathByCategoryAndProductId($nCategoryId, $nProductId).'small/0001.jpg';
        if(file_exists($_SERVER['DOCUMENT_ROOT'].'/'.$sFileName))
            return $sFileName;
        else
            return '/cms/code/img/no_image.jpg';
    }

    function GetProductMainMiddleImageByCategoryAndProductId($nCategoryId, $nProductId)
    {
        $sFileName = GetProductMiddlemagePathByCategoryAndProductId($nCategoryId, $nProductId).'0001.jpg';
        if(file_exists($_SERVER['DOCUMENT_ROOT'].'/'.$sFileName))
            return $sFileName;
        else
            return '/cms/code/img/no_image.jpg';
    }

    function GetProductMainBigImageByCategoryAndProductId($nCategoryId, $nProductId)
    {
        $sFileName = GetProductImagePathByCategoryAndProductId($nCategoryId, $nProductId).'big/0001.jpg';
        if(file_exists($_SERVER['DOCUMENT_ROOT'].'/'.$sFileName))
            return $sFileName;
        else
            return '/cms/code/img/no_image.jpg';
    }

    function GetProductSmallImagePathByCategoryAndProductId($nCategoryId, $nProductId)
    {
        return GetProductImagePathByCategoryAndProductId($nCategoryId, $nProductId).'small/';
    }

    function GetProductMiddlemagePathByCategoryAndProductId($nCategoryId, $nProductId)
    {
        return GetProductImagePathByCategoryAndProductId($nCategoryId, $nProductId).'mid/';
    }

    function GetProductBigImagePathByCategoryAndProductId($nCategoryId, $nProductId)
    {
        return GetProductImagePathByCategoryAndProductId($nCategoryId, $nProductId).'big/';
    }

    function GetProductOriginImagePathByCategoryAndProductId($nCategoryId, $nProductId)
    {
        return GetProductImagePathByCategoryAndProductId($nCategoryId, $nProductId).'origin/';
    }

    function GetProductSmallImageCountByCategoryAndProductId($nCategoryId, $nProductId)
    {
        $sPath = $_SERVER['DOCUMENT_ROOT'].GetProductSmallImagePathByCategoryAndProductId($nCategoryId, $nProductId).'*.jpg';
        $pData = glob($sPath);
        if (isset($pData))
            return count($pData);
        else
            return 0;
    }

	// заменяет указанный тег на необходимый текст
	function ReplaceTagInText($sText, $sTagName, $sReplaceText)
	{
		$sResult = str_replace(TAG_STARTER.$sTagName.TAG_ENDER, $sReplaceText, $sText);
		$sResult = str_replace(TAG_STARTER2.$sTagName.TAG_ENDER2, $sReplaceText, $sResult);
		return $sResult;
	}

	// заменяет указанный тег на необходимый текст и также вставляет указанные в строке $sAttributes атрибуты в текст, в котором находится этот тег
	function ReplaceTagInTextAndPlaceAttributes($sText, $sTagName, $sReplaceText, $sAttributes)
	{
		// !!!!!! пока сделано все примитивно и считается, что тег заключен в html-тег сразу
		$sResult = str_replace(">".TAG_STARTER.$sTagName.TAG_ENDER, " ".$sAttributes.">".$sReplaceText, $sText);
		return $sResult;
	}

	// ищет по имени тега шаблон и если он есть, то возвращает содержимое его файла.
	function GetTemplateForTag($sTagName, $bReplaceInsideTags = false)
	{
		$Tag=str_replace('TAG', '', $sTagName);
		$Tag="TEMPLATE_".$Tag."_FILE";
		if (defined($Tag))
		{
			$sFileName = constant($Tag);
			$sFilePath = PROCESSED_TEMPLATE_DIRECTORY.$sFileName;
			if (!file_exists($sFilePath)) return '';
            $FileData= file($sFilePath);
            if ($bReplaceInsideTags)
                $FileData = FindTagAndReplaceOnValue($FileData);
			return $FileData;
		}
		else return '';
	}


	// возвращает содержимое файла в $FileData
	function GetFileContent($sFilePath, &$FileData, $bReplaceInsideTags = false)
	{
		if (file_exists($sFilePath))
		{
			$FileData= file($sFilePath);
            if ($bReplaceInsideTags)
                $FileData = FindTagAndReplaceOnValue($FileData);
			return true;
		}
		else
		{
			$FileData= '';
			return false;
		}
	}

	// Заменяет одну строку в файле на другую
	function ReplaceStringInFile($sFilePath, $SearchString, $ReplaceString)
	{
		if (file_exists($sFilePath))
		{
			$file = fopen($sFilePath, 'r');
			$text = fread($file, filesize($sFilePath));
			fclose($file);
			$file = fopen($sFilePath, 'w');
			$text = str_replace($SearchString, $ReplaceString, $text);
			fwrite($file, $text);
			fclose($file);

			return true;
		}
		else
		{
			return false;
		}
	}

    // функция заменяет искомый текст на текст их указанного файла в $sFilePathName
    // @param string $sText,
    // @param string $sFilePathName,
    // @param string $sSearch,
    // @param integer $nLeaveSearchParam:
    // -1 оставить искомый текст и вставить текст из файла перед $sSearch
    // 0 - заменить текст, не оставляя искомого.
    // 1 - оставить искомый текст и вставить текст из файла после $sSearch
    function ReplaceTextFromFileData($sText, $sFilePathName, $sSearch, $nLeaveSearchParam)
    {
        if (file_exists($sFilePathName))
        {
            $sResult = '';
            $sFileText=implode(file($sFilePathName),'');
            switch ($nLeaveSearchParam)
            {
                case -1:
                    $sResult = str_replace($sSearch, $sFileText.$sSearch, $sText);
                    break;
                case 0:
                    $sResult = str_replace($sSearch, $sFileText, $sText);
                    break;
                case 1:
                    $sResult = str_replace($sSearch, $sSearch.$sFileText, $sText);
                    break;
				default:
					$sResult=$sText;
            }

            return $sResult;
        }
        else
            return $sText;
    }

	// функция добавляет базовые блоки скриптов и html кода в $PageCode
	function InsertCommonScriptsAndHtmlinCode($PageCode)
	{
        $PageCode = ReplaceTextFromFileData($PageCode, COMMON_CODE_FILEPATH_HEAD, '</head>', -1);
        $PageCode = ReplaceTextFromFileData($PageCode, COMMON_CODE_FILEPATH_BEGIN_BODY, '<body>', 1);
        $PageCode = ReplaceTextFromFileData($PageCode, COMMON_CODE_FILEPATH_END_BODY, '</body>', -1);

		////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		// добавляем информацию от плагинов
		$HeadBlock = array();
		$BeginBodyBlock = array();
		$EndBodyBlock = array();

		if (CBaseAddonClass::GetSummaryCodeForMainHTMLTags($HeadBlock, $BeginBodyBlock, $EndBodyBlock)===true)
		{

			if (count($HeadBlock)>0)
				$PageCode = str_replace('</head>', implode($HeadBlock,'')."\r\n".'</head>', $PageCode);
			if (count($BeginBodyBlock)>0)
				$PageCode = str_replace('<body>', '<body>'."\r\n".implode($BeginBodyBlock,''), $PageCode);
			if (count($EndBodyBlock)>0)
				$PageCode = str_replace('</body>', implode($EndBodyBlock,'')."\r\n".'</body>', $PageCode);
		}
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        return $PageCode;
	}

	// Для шаблонов на базе Adobe Muse
	function UnbindFromAdobeMuse()
	{
		// замена Muse.Utils.initWidget('#wID_формы' на иное имя, чтобы не загружался скрипт обработчик.
	}

	// производит замену основных тегов, которые не зависят от типа файла.
	function ReplaceCommonCMSTags($Code)
	{
		$Code=ReplaceTagInText($Code, TAG_DOMAIN_NAME, CCMS::$SETS[SETTINGS_SECTION_GLOBAL][SETTINGS_DOMAIN_NAME]);
        $Code=ReplaceTagInText($Code, TAG_DOMAIN_NAME_WITH_HTTP, CCMS::$sHttpPrefix.CCMS::$SETS[SETTINGS_SECTION_GLOBAL][SETTINGS_DOMAIN_NAME]);
		$Code=ReplaceTagInText($Code, TAG_COMPANY_NAME, CCMS::$SETS[SETTINGS_SECTION_GLOBAL][SETTINGS_COMPANY_NAME]);

		// категория не задана, попытаемся найти идентификатор категории в GET параметрах
		if (isset(CCMS::$currentCategory))
		{
			$DBCategory = CCMS::$currentCategory;

			$Code = ReplaceTagInText($Code, TAG_CATALOG_CATEGORY_NAME, $DBCategory['NAME']);
			$Code = ReplaceTagInText($Code, TAG_CATALOG_CATEGORY_ID, $DBCategory['ID']);
			$Code = ReplaceTagInText($Code, TAG_CATALOG_CATEGORY_CHPU_LINK, GetFullCategoryCPHULink($DBCategory['ID']));
		}

		return $Code;
	}


// Для корректной работы файлов из AdobeMuse проводим их дополнительную обработку

	function CopyAdobeMuseProjectToTemplate($MuseExportPath, $DestinationFolderPath, $DestinationHTMLFilePath)
	{
		$aDirectories = scandir($MuseExportPath);

		if (count($aDirectories)>0) {
			foreach ($aDirectories as $sPath) {
				if (($sPath=='..') || ($sPath=='.')) continue;

				if (is_dir($MuseExportPath.$sPath)) {

					system("rm -rf ".$DestinationFolderPath.$sPath);

					CopyDirectoryWithSubFolders(
						$MuseExportPath.$sPath,
						$DestinationFolderPath.$sPath);
				} else {
					$PathInfo = pathinfo($sPath);
					if ($PathInfo['extension']=='html')
						copy($MuseExportPath.$sPath, $DestinationHTMLFilePath.$sPath);
				}
			}
		}
		return true;
	}

	function ProcessAdobeMuseFile($HTMLCode)
	{
		$ResultText = $HTMLCode;
		// отменяем работу скриптов Muse
		$ResultText = str_replace("ajaxSubmit:true", "ajaxSubmit:false", $ResultText);
		$ResultText = str_replace(CCMS::$sHttpPrefix."#", "#", $ResultText);
		$ResultText = str_replace('href="css', 'href="/css', $ResultText);
		
		$ResultText = str_replace('src="', 'src="/', $ResultText);
		$ResultText = str_replace('src="//', 'src="/', $ResultText);
		$ResultText = str_replace('src="/{{', 'src="{{', $ResultText);
		
		$ResultText = str_replace('x3Cscript src="/', 'x3Cscript src="', $ResultText);
		$ResultText = str_replace('src="/http', 'src="http', $ResultText);

		$ResultText = str_replace('data-main="scripts', 'data-main="/scripts', $ResultText);

        $ResultText = str_replace('poster="assets', 'poster="/assets', $ResultText);

        $ResultText = str_replace(TAG_MUSE_HTTP_REMOVER, '', $ResultText);

		return $ResultText;
	}

	// Для корректной работы файлов из AdobeMuse проводим их дополнительную обработку
	function ProcessAdobeMuseScriptFiles()
	{
		$FileList = ScanDirectory($_SERVER['DOCUMENT_ROOT'].'/scripts');
		foreach ($FileList as $sFilePath) {
			ReplaceStringInFile($sFilePath, '"scripts/', '"/scripts/');
		}

		return true;
	}

    function SetCommonAttributesToHTMLNodes($HTMLCode)
    {
		$ResultText=$HTMLCode;

		// !!! здесь желательно переписать и применить регулярные выражения.
		/*
        $html=str_get_html(implode($HTMLCode,''));
		if (!isset($html)||$html==false)
			return $HTMLCode;
        // ищем включения форм и меняем параметры на нужные нам
        $Forms = $html->find('form');
        foreach ($Forms as $Form)
        {
            $Form->action = CMS_FILE_INTERNET_PATH.'send_lead_to_mail.php';
            $Form->method = 'POST';
			// добавляем события Яндекс.Метрики
			if (isset(CCMS::$SETS[SETTINGS_SECTION_STATISTICS][SETTINGS_STATISTICS_CREATE_YANDEX_METRIKA_EVENTS]))
			$Form->onsubmit="yaCounter".CCMS::$SETS[SETTINGS_SECTION_STATISTICS][SETTINGS_STATISTICS_YANDEX_METRIKA_COUNTER_NUMBER].".reachGoal('".$Form->id."'); return true;";
        }


        $ResultText = $html->save();

		$html->clear();
        unset($html);
		*/


		$ResultText = preg_replace("!action=\"scripts/form-(.*?)\"!si",'action="/'.CMS_FILE_INTERNET_PATH.'send_lead_to_mail.php"',$ResultText);

		$ResultText = ProcessAdobeMuseFile($ResultText);

		// заменяем параметры имен форм из настроек.
		if (isset(CCMS::$SETS[SETTINGS_SECTION_FORM][SETTINGS_FORM_NAME_INPUT_ALIAS]))
			$ResultText = str_replace(explode(SETTINGS_INI_FILE_PARAM_DEVIDER, CCMS::$SETS[SETTINGS_SECTION_FORM][SETTINGS_FORM_NAME_INPUT_ALIAS]), SETTINGS_COMMON_FORM_NAME_INPUT_NAME, $ResultText);
		if (isset(CCMS::$SETS[SETTINGS_SECTION_FORM][SETTINGS_FORM_PHONE_INPUT_ALIAS]))
			$ResultText = str_replace(explode(SETTINGS_INI_FILE_PARAM_DEVIDER, CCMS::$SETS[SETTINGS_SECTION_FORM][SETTINGS_FORM_PHONE_INPUT_ALIAS]), SETTINGS_COMMON_FORM_PHONE_INPUT_NAME, $ResultText);
		if (isset(CCMS::$SETS[SETTINGS_SECTION_FORM][SETTINGS_FORM_EMAIL_INPUT_ALIAS]))
			$ResultText = str_replace(explode(SETTINGS_INI_FILE_PARAM_DEVIDER, CCMS::$SETS[SETTINGS_SECTION_FORM][SETTINGS_FORM_EMAIL_INPUT_ALIAS]), SETTINGS_COMMON_FORM_EMAIL_INPUT_NAME, $ResultText);
		if (isset(CCMS::$SETS[SETTINGS_SECTION_FORM][SETTINGS_FORM_COMMENT_INPUT_ALIAS]))
			$ResultText = str_replace(explode(SETTINGS_INI_FILE_PARAM_DEVIDER, CCMS::$SETS[SETTINGS_SECTION_FORM][SETTINGS_FORM_COMMENT_INPUT_ALIAS]), SETTINGS_COMMON_FORM_COMMENT_INPUT_NAME, $ResultText);
		if (isset(CCMS::$SETS[SETTINGS_SECTION_FORM][SETTINGS_FORM_ADDRESS_INPUT_ALIAS]))
			$ResultText = str_replace(explode(SETTINGS_INI_FILE_PARAM_DEVIDER, CCMS::$SETS[SETTINGS_SECTION_FORM][SETTINGS_FORM_ADDRESS_INPUT_ALIAS]), SETTINGS_COMMON_FORM_ADDRESS_INPUT_NAME, $ResultText);

		$ResultText=ReplaceTagInText($ResultText, TAG_ITEM_ID_PRODUCT_COUNT, 		PREFIX_ITEM_ID_PRODUCT_COUNT.TAG_STARTER.TAG_PRODUCT_ID.TAG_ENDER);
		$ResultText=ReplaceTagInText($ResultText, TAG_ITEM_ID_PRODUCT_PRICE, 		PREFIX_ITEM_ID_PRODUCT_PRICE.TAG_STARTER.TAG_PRODUCT_ID.TAG_ENDER);
		$ResultText=ReplaceTagInText($ResultText, TAG_ITEM_ID_PRODUCT_TOTAL_PRICE, 	PREFIX_ITEM_ID_PRODUCT_TOTAL_PRICE.TAG_STARTER.TAG_PRODUCT_ID.TAG_ENDER);
		$ResultText=ReplaceTagInText($ResultText, TAG_ITEM_ID_PRODUCT_SMALL_IMG, 	PREFIX_ITEM_ID_PRODUCT_SMALL_IMG.TAG_STARTER.TAG_PRODUCT_ID.TAG_ENDER);
		$ResultText=ReplaceTagInText($ResultText, TAG_ITEM_ID_PRODUCT_RAW, 			PREFIX_ITEM_ID_PRODUCT_RAW.TAG_STARTER.TAG_PRODUCT_ID.TAG_ENDER);
		$ResultText=ReplaceTagInText($ResultText, TAG_ITEM_ID_PRODUCT_BUY_BTN, 		PREFIX_ITEM_ID_PRODUCT_BUY_BTN.TAG_STARTER.TAG_PRODUCT_ID.TAG_ENDER);

		$ResultText=ReplaceTagInText($ResultText, TAG_ITEM_ID_SHOPPING_CART_TOTAL_PRODUCT_COUNT, 		ITEM_ID_SHOPPING_CART_TOTAL_PRODUCT_COUNT);
		$ResultText=ReplaceTagInText($ResultText, TAG_ITEM_ID_SHOPPING_CART_TOTAL_VALUE,			 	ITEM_ID_SHOPPING_CART_TOTAL_VALUE);
		$ResultText=ReplaceTagInText($ResultText, TAG_ITEM_ID_SHOPPING_CART_TOTAL_DISCOUNT_VALUE, 		ITEM_ID_SHOPPING_CART_TOTAL_DISCOUNT_VALUE);
		$ResultText=ReplaceTagInText($ResultText, TAG_ITEM_ID_SHOPPING_CART_TOTAL_DISCOUNT, 			ITEM_ID_SHOPPING_CART_TOTAL_DISCOUNT);
		$ResultText=ReplaceTagInText($ResultText, TAG_ITEM_ID_SHOPPING_CART_TOTAL_VALUE_WITH_DISCOUNT, 	ITEM_ID_SHOPPING_CART_TOTAL_VALUE_WITH_DISCOUNT);

		return $ResultText;
    }

	function ReplaceCommonTagsAndParameters($PageText, $bNotItemTemplateFolder)
	{
		$PageText = AddAndReplaceUserTextFromSettings($PageText);
		$PageText = SetCommonAttributesToHTMLNodes($PageText);
		$PageText = InsertCommonScriptsAndHtmlinCode($PageText);
		$PageText = ReplaceTextFromFileData($PageText, TEMPLATE_DIRECTORY.TEMPLATE_COUNTERS_FILE, '</body>', -1);
		$PageText = ReplaceInTemplateSpecialTagsForPhpFunctionCall($PageText, $bNotItemTemplateFolder);

		$PageText = ReplaceCommonCMSTags($PageText);

		return $PageText;
	}

	// Функция вносит настройки из файла settings.txt указанные в блоках [ADD_TEXT_AFTER_TEXT] и [REPLACE_USER_TAGS]
	// Для блока ADD_TEXT_AFTER_TEXT добавляем к указанному тексту до @@ на текст после
	// Для блока REPLACE_USER_TAGS заменяет указанный текст до @@ на текст после
	function AddAndReplaceUserTextFromSettings($PageCode)
	{
		// обрабатываем блок добавления текста [ADD_TEXT_AFTER_TEXT]
		if (isset(CCMS::$SETS[SETTINGS_ADD_TEXT_AFTER_TEXT]))
		{

			$i = 1;
			//echo CCMS::$SETS[SETTINGS_ADD_TEXT_AFTER_TEXT][$i++];
			while (isset(CCMS::$SETS[SETTINGS_ADD_TEXT_AFTER_TEXT][$i]))
			{
				$Values=explode(SETTINGS_INI_FILE_PARAM_DEVIDER_TEXT, CCMS::$SETS[SETTINGS_ADD_TEXT_AFTER_TEXT][$i]);

				if (count($Values)>1)
				{
					$PageCode = str_replace(trim($Values[0]), trim($Values[0]). " " . trim($Values[1]), $PageCode);
				}

				$i++;
			}
		}

		// обрабатывем блок замены текста [REPLACE_USER_TAGS]
		if (isset(CCMS::$SETS[SETTINGS_REPLACE_USER_TAGS]))
		{

			$i = 1;
			while (isset(CCMS::$SETS[SETTINGS_REPLACE_USER_TAGS][$i]))
			{

				$Values = explode(SETTINGS_INI_FILE_PARAM_DEVIDER_TEXT, CCMS::$SETS[SETTINGS_REPLACE_USER_TAGS][$i]);

				if (count($Values) > 1)
				{
/*
					$PageCode = str_replace(TAG_STARTER.trim($Values[0]).TAG_ENDER, trim($Values[1]), $PageCode);
                    $PageCode = str_replace(TAG_STARTER2.trim($Values[0]).TAG_ENDER2, trim($Values[1]), $PageCode);
*/
                    $PageCode = str_replace(TAG_STARTER.trim($Values[0]).TAG_ENDER, trim($Values[1]), $PageCode);
                    $PageCode = str_replace(TAG_STARTER2.trim($Values[0]).TAG_ENDER2, trim($Values[1]), $PageCode);
				}

				$i++;
			}
		}

		return $PageCode;
	}

	function SetSettingsToCommonJavaScriptFiles()
	{

		// применяем настройки к файлу common.js
		$sJSFilePath = $_SERVER['DOCUMENT_ROOT'].'/cms/code/js/common.js';
		if (!file_exists($sJSFilePath)) return false;
		$sJSFileText=file($sJSFilePath);
		$sJSFileText=ReplaceTagInText($sJSFileText, 'SETTINGS_CATALOG_BUY_BTN_IN_CART_CLASS_NAME', CCMS::$SETS[SETTINGS_SECTION_CATALOG][SETTINGS_CATALOG_BUY_BTN_IN_CART_CLASS_NAME]);
		$sJSFileText=ReplaceTagInText($sJSFileText, 'SETTINGS_CATALOG_BUY_BTN_NOT_IN_CART_CLASS_NAME', CCMS::$SETS[SETTINGS_SECTION_CATALOG][SETTINGS_CATALOG_BUY_BTN_NOT_IN_CART_CLASS_NAME]);
		//$sJSFileText=ReplaceTagInText($sJSFileText, 'SETTINGS_CATALOG_HEADER_PRODUCT_COUNT_CLASS_NAME', CCMS::$SETS[SETTINGS_SECTION_CATALOG][SETTINGS_CATALOG_HEADER_PRODUCT_COUNT_CLASS_NAME]);
		$sJSFileText=ReplaceTagInText($sJSFileText, 'SETTINGS_CATALOG_HEADER_PRODUCT_COUNT_TAG_ID', CCMS::$SETS[SETTINGS_SECTION_CATALOG][SETTINGS_CATALOG_HEADER_PRODUCT_COUNT_TAG_ID]);
		$sJSFileText=ReplaceTagInText($sJSFileText, 'SETTINGS_CATALOG_HEADER_CART_ICON_TAG_ID', CCMS::$SETS[SETTINGS_SECTION_CATALOG][SETTINGS_CATALOG_HEADER_CART_ICON_TAG_ID]);

		$sJSFileText=ReplaceTagInText($sJSFileText, 'SETTINGS_CATALOG_BUY_BTN_IN_CART_TEXT', CCMS::$SETS[SETTINGS_SECTION_CATALOG][SETTINGS_CATALOG_BUY_BTN_IN_CART_TEXT]);
		$sJSFileText=ReplaceTagInText($sJSFileText, 'SETTINGS_CATALOG_BUY_BTN_NOT_IN_CART_TEXT', CCMS::$SETS[SETTINGS_SECTION_CATALOG][SETTINGS_CATALOG_BUY_BTN_NOT_IN_CART_TEXT]);
		$sJSFileText=ReplaceTagInText($sJSFileText, 'SETTINGS_PRODUCT_BUY_BTN_ITEM_ID', CCMS::$SETS[SETTINGS_SECTION_PRODUCT][SETTINGS_PRODUCT_BUY_BTN_ITEM_ID]);

		$sJSFileText=ReplaceTagInText($sJSFileText, 'SETTINGS_STATISTICS_YANDEX_METRIKA_COUNTER_NUMBER', CCMS::$SETS[SETTINGS_SECTION_STATISTICS][SETTINGS_STATISTICS_YANDEX_METRIKA_COUNTER_NUMBER]);



		$sJSFileText=ReplaceTagInText($sJSFileText, "PREFIX_ITEM_ID_PRODUCT_COUNT", PREFIX_ITEM_ID_PRODUCT_COUNT);
		$sJSFileText=ReplaceTagInText($sJSFileText, "PREFIX_ITEM_ID_PRODUCT_PRICE", PREFIX_ITEM_ID_PRODUCT_PRICE);
		$sJSFileText=ReplaceTagInText($sJSFileText, "PREFIX_ITEM_ID_PRODUCT_TOTAL_PRICE", PREFIX_ITEM_ID_PRODUCT_TOTAL_PRICE);
		$sJSFileText=ReplaceTagInText($sJSFileText, "PREFIX_ITEM_ID_PRODUCT_SMALL_IMG", PREFIX_ITEM_ID_PRODUCT_SMALL_IMG);
		$sJSFileText=ReplaceTagInText($sJSFileText, "PREFIX_ITEM_ID_PRODUCT_RAW", PREFIX_ITEM_ID_PRODUCT_RAW);
		$sJSFileText=ReplaceTagInText($sJSFileText, "PREFIX_ITEM_ID_PRODUCT_BUY_BTN", PREFIX_ITEM_ID_PRODUCT_BUY_BTN);

		$sJSFileText=ReplaceTagInText($sJSFileText, "ITEM_ID_SHOPPING_CART_TOTAL_PRODUCT_COUNT", ITEM_ID_SHOPPING_CART_TOTAL_PRODUCT_COUNT);
		$sJSFileText=ReplaceTagInText($sJSFileText, "ITEM_ID_SHOPPING_CART_TOTAL_VALUE", ITEM_ID_SHOPPING_CART_TOTAL_VALUE);
		$sJSFileText=ReplaceTagInText($sJSFileText, "ITEM_ID_SHOPPING_CART_TOTAL_DISCOUNT_VALUE", ITEM_ID_SHOPPING_CART_TOTAL_DISCOUNT_VALUE);
		$sJSFileText=ReplaceTagInText($sJSFileText, "ITEM_ID_SHOPPING_CART_TOTAL_DISCOUNT", ITEM_ID_SHOPPING_CART_TOTAL_DISCOUNT);
		$sJSFileText=ReplaceTagInText($sJSFileText, "ITEM_ID_SHOPPING_CART_TOTAL_VALUE_WITH_DISCOUNT", ITEM_ID_SHOPPING_CART_TOTAL_VALUE_WITH_DISCOUNT);

		$sJSFileText=ReplaceTagInText($sJSFileText, TAG_DOMAIN_NAME, CCMS::$SETS[SETTINGS_SECTION_GLOBAL][SETTINGS_DOMAIN_NAME]);
        $sJSFileText=ReplaceTagInText($sJSFileText, TAG_DOMAIN_NAME_WITH_HTTP, CCMS::$sHttpPrefix.CCMS::$SETS[SETTINGS_SECTION_GLOBAL][SETTINGS_DOMAIN_NAME]);

		file_put_contents(TEMPLATE_DIRECTORY.'js/common.js', $sJSFileText);
	}

	// производит замену основных тегов и параметров в указанном файле шаблона. Возвращает обработанные данные в виде массива строк.
	function ReplaceCommonTagsAndParametersInTemplateFile($sFilePath, &$Result, $bNotItemTemplateFolder)
	{
		if (!file_exists($sFilePath)) return '';
		$FileText=file($sFilePath);

		$Result = ReplaceCommonTagsAndParameters($FileText, $bNotItemTemplateFolder);

		return true;
	}

	function PrepareTemplateFileAndSaveInProcessed($sFilePath, $bNotItemTemplateFolder)
	{
		$FileText = '';
		if (ReplaceCommonTagsAndParametersInTemplateFile($sFilePath, $FileText, $bNotItemTemplateFolder))
		{
			$sFilePath = str_replace(TEMPLATE_DIRECTORY, PROCESSED_TEMPLATE_DIRECTORY, $sFilePath);
			$path_parts = pathinfo($sFilePath);

			if (!is_dir($path_parts['dirname'])) mkdir($path_parts['dirname'], 0777, true);
			file_put_contents($path_parts['dirname'].'/'.$path_parts['filename'].".php", $FileText);
			return true;
		}
		else
			return false;
	}


	function ScanDirectory($Directory) {
		$arrfiles = array();
		if (is_dir($Directory)) {
			if ($handle = opendir($Directory)) {
				chdir($Directory);
				while (false !== ($file = readdir($handle))) {
					if ($file != "." && $file != "..") {
						if (is_dir($file)) {
							$arr = ScanDirectory($file);
							foreach ($arr as $value) {
								$arrfiles[] = $Directory."/".$value;
							}
						} else {
							$arrfiles[] = $Directory."/".$file;
						}
					}
				}
				chdir("../");
			}
			closedir($handle);
		}
		return $arrfiles;
	}

	
	function CopyDirectoryWithSubFolders($source, $dest, $over=false)
	{
		if(!is_dir($dest))
			mkdir($dest);
		if($handle = opendir($source))
		{
			while(false !== ($file = readdir($handle)))
			{
				if($file != '.' && $file != '..')
				{
					$path = $source . '/' . $file;
					if(is_file($path))
					{
						if(!is_file($dest . '/' . $file || $over))
							if(!@copy($path, $dest . '/' . $file))
							{
								echo "('.$path.') Ошибка!!! ";
							}
					}
					elseif(is_dir($path))
					{
						if(!is_dir($dest . '/' . $file))
							mkdir($dest . '/' . $file);
						CopyDirectoryWithSubFolders($path, $dest . '/' . $file, $over);
					}
				}
			}
			closedir($handle);
		}
	}

	function CreateProductFolders()
	{
		$ProductList = CCMS::$DB->GetProductList();

		foreach ($ProductList as $pProductInfo)
		{
			//сохраняем
			if (!is_dir($_SERVER['DOCUMENT_ROOT'] . GetProductDirectoryByCategoryAndProductId($pProductInfo['SECTION_ID'], $pProductInfo['ID'])))
				mkdir($_SERVER['DOCUMENT_ROOT'] . GetProductDirectoryByCategoryAndProductId($pProductInfo['SECTION_ID'], $pProductInfo['ID']), 0777, true);

			if (!is_dir($_SERVER['DOCUMENT_ROOT'] . GetProductImagePathByCategoryAndProductId($pProductInfo['SECTION_ID'], $pProductInfo['ID'])))
				mkdir($_SERVER['DOCUMENT_ROOT'] . GetProductImagePathByCategoryAndProductId($pProductInfo['SECTION_ID'], $pProductInfo['ID']), 0777, true);

			if (!is_dir($_SERVER['DOCUMENT_ROOT'] . GetProductImagePathByCategoryAndProductId($pProductInfo['SECTION_ID'], $pProductInfo['ID']) . 'small/'))
				mkdir($_SERVER['DOCUMENT_ROOT'] . GetProductImagePathByCategoryAndProductId($pProductInfo['SECTION_ID'], $pProductInfo['ID']) . 'small/', 0777, true);

			if (!is_dir($_SERVER['DOCUMENT_ROOT'] . GetProductImagePathByCategoryAndProductId($pProductInfo['SECTION_ID'], $pProductInfo['ID']) . 'mid/'))
				mkdir($_SERVER['DOCUMENT_ROOT'] . GetProductImagePathByCategoryAndProductId($pProductInfo['SECTION_ID'], $pProductInfo['ID']) . 'mid/', 0777, true);

			if (!is_dir($_SERVER['DOCUMENT_ROOT'] . GetProductImagePathByCategoryAndProductId($pProductInfo['SECTION_ID'], $pProductInfo['ID']) . 'big/'))
				mkdir($_SERVER['DOCUMENT_ROOT'] . GetProductImagePathByCategoryAndProductId($pProductInfo['SECTION_ID'], $pProductInfo['ID']) . 'big/', 0777, true);

			if (!is_dir($_SERVER['DOCUMENT_ROOT'] . GetProductImagePathByCategoryAndProductId($pProductInfo['SECTION_ID'], $pProductInfo['ID']) . 'origin/'))
				mkdir($_SERVER['DOCUMENT_ROOT'] . GetProductImagePathByCategoryAndProductId($pProductInfo['SECTION_ID'], $pProductInfo['ID']) . 'origin/', 0777, true);
		}
	}

	function CreateProductFoldersWithNames()
	{
		$ProductList = CCMS::$DB->GetProductList();

		$sPath = $_SERVER['DOCUMENT_ROOT'].PRODUCTS_INFO_WITH_NAMES_PATH;

		foreach ($ProductList as $pProductInfo)
		{
			$sProductDir = $sPath.GetCHPULinkForProduct($pProductInfo, true);

			//сохраняем
			if (!is_dir($sProductDir))
				mkdir($sProductDir, 0777, true);

			if (!is_dir($sProductDir."img/"))
				mkdir($sProductDir."img/", 0777, true);

			if (!is_dir($sProductDir."img/origin/"))
				mkdir($sProductDir."img/origin/", 0777, true);

			if (!is_dir($sProductDir."img/big/"))
				mkdir($sProductDir."img/big/", 0777, true);

			if (!is_dir($sProductDir."img/mid/"))
				mkdir($sProductDir."img/mid/", 0777, true);

			if (!is_dir($sProductDir."img/small/"))
				mkdir($sProductDir."img/small/", 0777, true);
		}
	}

	function CopyProductsFilesFromFolder($bFromFolderWithNamesToProductsFolder = true)
	{
		$ProductList = CCMS::$DB->GetProductList();

		$sPathProductWithNames = $_SERVER['DOCUMENT_ROOT'].PRODUCTS_INFO_WITH_NAMES_PATH;

		foreach ($ProductList as $pProductInfo)
		{
			$sProductDir = $_SERVER['DOCUMENT_ROOT'] . GetProductDirectoryByCategoryAndProductId($pProductInfo['SECTION_ID'], $pProductInfo['ID']);
			$sProductDirWithName = $sPathProductWithNames.GetCHPULinkForProduct($pProductInfo, true);

			// копируем из $sProductDirWithName в $sProductDir
			if (is_dir($sProductDir)&&is_dir($sProductDirWithName))
			{
				if ($bFromFolderWithNamesToProductsFolder)
					CopyDirectoryWithSubFolders($sProductDirWithName, $sProductDir);
				else
					CopyDirectoryWithSubFolders($sProductDir, $sProductDirWithName);
			}
		}
	}

    function RenameImagesInProductWithImageFolder()
    {
        $ProductList = CCMS::$DB->GetProductList();

        $sPath = $_SERVER['DOCUMENT_ROOT'].PRODUCTS_INFO_WITH_NAMES_PATH;

        foreach ($ProductList as $pProductInfo)
        {
            $sProductDir = $sPath.GetCHPULinkForProduct($pProductInfo, true);
            $sProductDir .= "img/origin/";

            // переименовываем
            if (is_dir($sProductDir))
            {
                $arrFiles = array_values(array_diff(scandir($sProductDir), array('..', '.')));

                for ($i=0;$i<count($arrFiles);$i++)
                {
                    if (is_file($sProductDir.$arrFiles[$i]))
                    {
                        $sFileName = $sProductDir . sprintf("%'.04d", $i + 1) . '.jpg';
                        rename($sProductDir . $arrFiles[$i], $sFileName);
                    }
                }
            }
        }
    }

	function CopyProductOriginImageFilesToOtherImageFolders($bUseProductsFolder = true)
	{
		$ProductList = CCMS::$DB->GetProductList();

		$sPath = $_SERVER['DOCUMENT_ROOT'].PRODUCTS_INFO_WITH_NAMES_PATH;

		foreach ($ProductList as $pProductInfo)
		{
			$sProductDir = "";
			if ($bUseProductsFolder)
				$sProductDir = $_SERVER['DOCUMENT_ROOT'].GetProductDirectoryByCategoryAndProductId($pProductInfo['SECTION_ID'], $pProductInfo['ID']);
			else
				$sProductDir = $sPath.GetCHPULinkForProduct($pProductInfo, true);

			$sProductDir .= "img/";

			// копируем
			if (is_dir($sProductDir))
			{
				CopyDirectoryWithSubFolders($sProductDir."origin/", $sProductDir."big/");
				CopyDirectoryWithSubFolders($sProductDir."origin/", $sProductDir."mid/");
				CopyDirectoryWithSubFolders($sProductDir."origin/", $sProductDir."small/");
			}
		}
	}

	function PrepareAllTemplateFileFromFolderAndSaveInProcessed(
		$Path,
		$bCreateProductFolders = true,
		$bCreateHtaccessFile = true,
		$bCreateSiteMapFile = true,
		$bCreateYMLFile = true
	)
	{
		$FileList = ScanDirectory($Path);
		foreach ($FileList as $sFilePath)
		{
			if (strpos($sFilePath, '.htaccess')!==false) continue;

			$bNotItemTemplateFolder = false;

			if (
				(strpos($sFilePath, '/catalog/')===false)&&
				(strpos($sFilePath, '/catalog_tree/')===false)&&
				(strpos($sFilePath, '/css/')===false)&&
				(strpos($sFilePath, '/js/')===false)&&
				(strpos($sFilePath, '/other/')===false)&&
				(strpos($sFilePath, '/plugins/')===false)&&
				(strpos($sFilePath, '/order/')===false)
			)
			{
				$bNotItemTemplateFolder = true;
			}

			PrepareTemplateFileAndSaveInProcessed($sFilePath, $bNotItemTemplateFolder);
			// если директория файла не принадлежит к стандартным папкам шаблонов, то создаем все это в основной директории.
			if ($bNotItemTemplateFolder)
			{

				$path_parts = pathinfo($sFilePath);

				// вычитаем /cms/template/ из пути.
				$sNewFilePath = str_replace('/cms/template', '', $path_parts['dirname']);

				// подставляем в специализированный шаблон
				// сохраняем полученный результат в файл и выводим его через $sTemplatePhpFileName
				if (file_exists(SIMPLE_TEMPLATE_PHP_PAGE_CODE_FILEPATH))
				{
					if (!is_dir($sNewFilePath)) mkdir($sNewFilePath, 0777, true);

					$FilePhp=file(SIMPLE_TEMPLATE_PHP_PAGE_CODE_FILEPATH);
					$FilePhp=ReplaceTagInText($FilePhp, TAG_TEMPLATE_FILE_NAME, $path_parts['filename'].".php");
					$FilePhp = ReplaceCommonCMSTags($FilePhp);
					//сохраняем
					file_put_contents($sNewFilePath.'/'.$path_parts['filename'].".php", $FilePhp);
				}

			}
		}

		SetSettingsToCommonJavaScriptFiles();

		$ProductList = CCMS::$DB->GetProductList();

		if ($bCreateHtaccessFile) {
			// записи с путями ЧПУ для товаров для файда htaccess
			$saCHPU = array();

			// доавляем php страницы

			$aFiles = ScanDirectory($_SERVER['DOCUMENT_ROOT']);

			for ($i = 0; $i < count($aFiles); $i++) {
				if (
					(strpos($aFiles[$i], ".php") > 0)
					&& (strpos($aFiles[$i], "cms/") == false)
					&& (strpos($aFiles[$i], "index.php") == false)
                    && (strpos($aFiles[$i], "scripts/") == false)
				) {
					$Link = $aFiles[$i];
					$Link = str_replace($_SERVER['DOCUMENT_ROOT'] . '/', '', $Link);
					$Link = str_replace('.php', '', $Link);

					if (strlen($Link)>0)
                    {
                        // формируем путь ЧПУ для файла .htaccess
                        array_push(
                            $saCHPU,
                            "RewriteRule ^" . $Link . "/?$	" . $Link . ".php [QSA]\r\n");
                        //"RewriteRule ^".$Link."/?$	http://".TAG_STARTER.TAG_DOMAIN_NAME.TAG_ENDER."/".$Link.".php [QSA]\r\n");
                    }
				}
			}

			if (isset($ProductList))
                foreach ($ProductList as $pProductInfo) {

                    // формируем путь ЧПУ для файла .htaccess
                    array_push(
                        $saCHPU,
                        "RewriteRule ^" . GetCHPULinkForProduct($pProductInfo) . "?$	product.php?ProductId=" . $pProductInfo['ID'] . " [QSA]\r\n");
                }

			// проверяем нет ли запрета на создание ссылок ЧПУ для категорий каталога и если нет, то создаем их автоматически
			if (
				(isset(CCMS::$SETS[SETTINGS_SECTION_SEF_URL][SETTINGS_SEF_URL_DONT_CREATE_CATALOG_CHPU])&&
				CCMS::$SETS[SETTINGS_SECTION_SEF_URL][SETTINGS_SEF_URL_DONT_CREATE_CATALOG_CHPU]==0)
				||(!isset(CCMS::$SETS[SETTINGS_SECTION_SEF_URL][SETTINGS_SEF_URL_DONT_CREATE_CATALOG_CHPU]))
			)
			{
				$pCategoryListCHPU = GetFullCategoryLinkLinkListForCHPU();

				if (isset($pCategoryListCHPU))
                    foreach ($pCategoryListCHPU as $nCategoryId => $sCategoryCHPU) {
                        array_push(
                            $saCHPU,
                            "RewriteRule ^" . $sCategoryCHPU . "?$	catalog.php?CategoryId=" . $nCategoryId . " [QSA]\r\n");
                    }

				$CategoryList = CCMS::$DB->GetCategoryList();

                if (isset($CategoryList))
                    foreach ($CategoryList as $pCategoryInfo) {
                        // формируем путь ЧПУ для файла .htaccess
                        array_push(
                            $saCHPU,
                            "RewriteRule ^catalog_" . $pCategoryInfo['ID'] . "/?$	/catalog/?CategoryId=" . $pCategoryInfo['ID'] . " [QSA]\r\n");
                    }
			}

			$saPluginsCHPU = CBaseAddonClass::GetHtaccessRowsForAllPlugins();
			if (isset($saPluginsCHPU)&&(count($saPluginsCHPU)>0))
				$saCHPU = array_merge($saCHPU, $saPluginsCHPU);


			// берем дополнительные строки для файла htaccess из папки с шаблонами, если он там есть.
			if (file_exists(TEMPLATE_DIRECTORY.".htaccess"))
				$saCHPU = array_merge($saCHPU, file(TEMPLATE_DIRECTORY.".htaccess"));

			$sHtaccessFile = array();
			if (GetFileContent(TEMPLATE_PHP_DIRECTORY . ".htaccess", $sHtaccessFile)) {
				$sHtaccessFile = array_merge($sHtaccessFile, $saCHPU);
				$sHtaccessFile = ReplaceTagInText($sHtaccessFile, TAG_DOMAIN_NAME, CCMS::$SETS[SETTINGS_SECTION_GLOBAL][SETTINGS_DOMAIN_NAME]);
                $sHtaccessFile = ReplaceTagInText($sHtaccessFile, TAG_DOMAIN_NAME_WITH_HTTP, CCMS::$sHttpPrefix.CCMS::$SETS[SETTINGS_SECTION_GLOBAL][SETTINGS_DOMAIN_NAME]);
				file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/.htaccess", $sHtaccessFile);
			}
		}

		ProcessAdobeMuseScriptFiles();

		if ($bCreateSiteMapFile) CreateSiteMapFile();
		if ($bCreateYMLFile)CreateYMLFile();

		return true;
	}

	// печатает страницу шаблона. Есть возможность задать шаблон php кода на основе которого происходит эта печать.
	function PrintTemplatePageByFileName($sTemplateFileName, $sTemplatePhpFileName = TEMPLATE_PHP_PAGE_CODE_FILENAME)
	{
		// убираем расширение
		$sTemplateFileName = str_replace(strrchr($sTemplateFileName, "."), ".php", $sTemplateFileName);

		$sFilePath = PROCESSED_TEMPLATE_DIRECTORY.$sTemplateFileName;
		if (!file_exists($sFilePath)) return false;

		///////

		$sOnlyFileName = str_replace(".php", "", $sTemplateFileName);
		if ($sOnlyFileName != 'index')
		{
            CCMS::$VARS[TAG_CURRENT_PAGE_FULL_CHPU_HREF] = CCMS::$sHttpPrefix . CCMS::$SETS[SETTINGS_SECTION_GLOBAL][SETTINGS_DOMAIN_NAME] . "/" . $sOnlyFileName . "/";
            CCMS::$VARS[TAG_CURRENT_ALTERNATE_MOBILE_PAGE_FULL_CHPU_HREF] = CCMS::$sHttpPrefix .'m.'. CCMS::$SETS[SETTINGS_SECTION_GLOBAL][SETTINGS_DOMAIN_NAME] . "/" . $sOnlyFileName . "/";
        }
		else
        {
            CCMS::$VARS[TAG_CURRENT_PAGE_FULL_CHPU_HREF] = CCMS::$sHttpPrefix . CCMS::$SETS[SETTINGS_SECTION_GLOBAL][SETTINGS_DOMAIN_NAME] . "/";
            CCMS::$VARS[TAG_CURRENT_ALTERNATE_MOBILE_PAGE_FULL_CHPU_HREF] = CCMS::$sHttpPrefix .'m.'. CCMS::$SETS[SETTINGS_SECTION_GLOBAL][SETTINGS_DOMAIN_NAME] . "/";
        }

		///////////


		include($sFilePath);

		return true;
	}

	// Выдает код странице на базе шаблона $sTemplatePhpFileName.
	function GetTemplatePageByTemplateText($sFileText, $sTemplatePhpFileName = TEMPLATE_PHP_PAGE_CODE_FILENAME)
	{
		// сохраняем полученный результат в файл и выводим его через $sTemplatePhpFileName
		if (!file_exists(TEMPLATE_PHP_DIRECTORY.$sTemplatePhpFileName)) return '';
		$FilePhp=file(TEMPLATE_PHP_DIRECTORY.$sTemplatePhpFileName);
		$sFileText=ReplaceTagInText($FilePhp, TAG_TEMPLATE_FILE,  implode($sFileText,''));

		return $sFileText;
	}

	function PrintTemplateFileAsPhp($sFileName, $bReplaceCommonTags)
	{
		$sFilePath = TEMPLATE_DIRECTORY.$sFileName;
		if (!file_exists($sFilePath)) return false;
		$FileText=file($sFilePath);

		$ResultText=$FileText;

		/*
		if ($bReplaceCommonTags==true)
		{
			$ResultText=ReplaceTagInText($ResultText, TAG_HEADER, implode(GetTemplateForTag(TAG_HEADER),''));
			$ResultText=ReplaceTagInText($ResultText, TAG_FOOTER, implode(GetTemplateForTag(TAG_FOOTER),''));
		}
        */
		//$ResultText = ReplaceCommonTagsAndParameters($ResultText, true);

		SetSettingsToCommonJavaScriptFiles();

		$ResultText=trim($ResultText);
		// сохраняем полученный результат в файл и выводим его через template_page_code.txt
		if (!file_exists(TEMPLATE_PHP_PAGE_CODE_FILE)) return '';
		$FilePhp=file(TEMPLATE_PHP_PAGE_CODE_FILE);
		$FilePhp=ReplaceTagInText($FilePhp, TAG_TEMPLATE_FILE,  $ResultText);

		//сохраняем и исполняем
		$sTempFilePathName= TEMP_DIRECTORY.'tmp_'.$sFileName;
		file_put_contents($sTempFilePathName, $FilePhp);

        // исполняем
		include($sTempFilePathName);

		return true;
	}

	// Заменяет в шаблоне стандартный набор функций на вызов php функций данной cms.
	function ReplaceInTemplateSpecialTagsForPhpFunctionCall($sTemplate, $bNotItemTemplateFolder)
	{

		$sTemplate = preg_replace("!".TAG_STARTER.TAG_CATALOG_CATEGORIES_WITH_CHILDS."(.*?)".TAG_ENDER."!si","<? echo implode(GetCatalogCategoryTreePageCode\\1, ''); ?>",$sTemplate);
		$sTemplate = preg_replace("!".TAG_STARTER.TAG_CATALOG_CATEGORY."(.*?)".TAG_ENDER."!si","<? echo implode(GetCatalogCategoryPageCode\\1, ''); ?>",$sTemplate);

		$sTemplate = preg_replace("!".TAG_STARTER.TAG_CATEGORY_TREE_LIST."(.*?)".TAG_ENDER."!si","<? echo implode(GetCatalogCategoryTree\\1, ''); ?>",$sTemplate);

		$sTemplate = preg_replace("!".TAG_STARTER.TAG_PRODUCT_IMAGE_BLOCK."(.*?)".TAG_ENDER."!si","<? echo GetProductImageBlockPathById({{PRODUCT_ID}}, \\1; ?>", $sTemplate);

		$sTemplate = str_replace("({{PRODUCT_ID}}, (", "({{PRODUCT_ID}}, ", $sTemplate);

		$sTemplate=ReplaceTagInText($sTemplate, TAG_PRODUCT_ID,  "-PRODUCT_ID-");

		$sTemplate = preg_replace("!".TAG_STARTER.TAG_PRODUCT_PARAMETER."(.*?)".TAG_ENDER."!si","<? echo (GetProductParameterByName\\1); ?>",$sTemplate);

		$sTemplate = str_replace("-PRODUCT_ID-",  TAG_STARTER2.TAG_PRODUCT_ID.TAG_ENDER2, $sTemplate);

		$sTemplate = ReplaceTagInText($sTemplate, TAG_PRODUCT_COUNT_INPUT,
			'<input type="text" value="'.TAG_STARTER.TAG_PRODUCT_COUNT_IN_CART.TAG_ENDER.
			'" id="'.PREFIX_ITEM_ID_PRODUCT_COUNT.TAG_STARTER.TAG_PRODUCT_ID.TAG_ENDER.'" class="product_input" oninput="ChangeProductCountInShoppingCart({{PRODUCT_ID}}); return true;">');

        $sTemplate = preg_replace("!".TAG_STARTER.TAG_SHOPPING_CART_PRODUCT_LIST."\((.*?)\)".TAG_ENDER."!si","<? echo  implode(GetCatalogShoppingCartProductListCode(\\1), ''); ?>",$sTemplate);
		$sTemplate = str_replace(TAG_STARTER.TAG_SHOPPING_CART_PRODUCT_LIST.TAG_ENDER, "<? echo implode(GetCatalogShoppingCartProductListCode(), ''); ?>", $sTemplate);

		$sTemplate = preg_replace_callback("!".TAG_STARTER.TAG_PRINT_TEMPLATE."\((.*?)\)".TAG_ENDER."!si","GetTemplateTextByFileNameCallback", $sTemplate);

		// замена для изменяемых в процессе работы сайта величин
        // для изменяемых эдементов нужно, чтобы был спан содержащий id

        $sTemplate = preg_replace("!".TAG_STARTER."U_(.*?)".TAG_ENDER."!si",'<span id="\\1">{{\\1}}</span>',$sTemplate);
        $sTemplate = preg_replace("!".TAG_STARTER."UP_(.*?)".TAG_ENDER."!si",'<span id="\\1_{{PRODUCT_ID}}">{{\\1}}</span>',$sTemplate);
        $sTemplate = preg_replace("!".TAG_STARTER."UC_(.*?)".TAG_ENDER."!si",'<span id="\\1_{{CATEGORY_ID}}">{{\\1}}</span>',$sTemplate);

		if ($bNotItemTemplateFolder)
		{
			// пока только для категорий
			$sTemplate = preg_replace("!".TAG_STARTER."CATEGORY_(.*?)".TAG_ENDER."!si","<? echo GetTagValue('CATEGORY_\\1'); ?>",$sTemplate);
			$sTemplate = preg_replace("!".TAG_STARTER2."CATEGORY_(.*?)".TAG_ENDER2."!si","<? echo GetTagValue('CATEGORY_\\1'); ?>",$sTemplate);

            $sTemplate = preg_replace("!".TAG_STARTER."CATEGORY_(.*?)".TAG_ENDER."!si","<? echo GetTagValue('CATEGORY_\\1'); ?>",$sTemplate);
            $sTemplate = preg_replace("!".TAG_STARTER2."CATEGORY_(.*?)".TAG_ENDER2."!si","<? echo GetTagValue('CATEGORY_\\1'); ?>",$sTemplate);

			$sTemplate = ReplaceTagInText($sTemplate, TAG_CURRENT_PAGE_FULL_CHPU_HREF, "<? echo GetTagValue('".TAG_CURRENT_PAGE_FULL_CHPU_HREF."'); ?>");
            $sTemplate = ReplaceTagInText($sTemplate, TAG_CURRENT_ALTERNATE_MOBILE_PAGE_FULL_CHPU_HREF, "<? echo GetTagValue('".TAG_CURRENT_ALTERNATE_MOBILE_PAGE_FULL_CHPU_HREF."'); ?>");

			$sTemplate = preg_replace("!".TAG_STARTER.TAG_PRODUCT_PROPERTY."\((.*?)\)".TAG_ENDER."!si","<? echo GetProductPropertyValue({{PRODUCT_ID}}, '\\1'); ?>", $sTemplate);


            $sTemplate = preg_replace("!".TAG_STARTER."SETTINGS(.*?)\((.*?)\)".TAG_ENDER."!si","<? echo GetTagValue('SETTINGS',\\2); ?>",$sTemplate);

            // меняем тольько теги вида PRODUCT_*(Параметр 1, ...)
            $sTemplate = preg_replace("!".TAG_STARTER."PRODUCT_(.*?)\((.*?)\)".TAG_ENDER."!si","<? echo GetTagValue('PRODUCT_\\1',\\2); ?>",$sTemplate);
			//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			// теги плагинов
			/*$sTemplate = preg_replace("!".TAG_STARTER."PL_(.*?)\((.*?)\)".TAG_ENDER."!si","<? echo GetTagValue('PL_\\1',\"\\2\"); ?>",$sTemplate);*/
            $sTemplate = preg_replace("!".TAG_STARTER."PL_(.*?)\((.*?)\)".TAG_ENDER."!si","<? echo GetTagValue('PL_\\1',\\2); ?>",$sTemplate);
			$sTemplate = preg_replace("!".TAG_STARTER."PL_(.*?)".TAG_ENDER."!si","<? echo GetTagValue('PL_\\1'); ?>",$sTemplate);

            $sTemplate = preg_replace("!".TAG_STARTER2."PL_(.*?)\((.*?)\)".TAG_ENDER2."!si","<? echo GetTagValue('PL_\\1',\\2); ?>",$sTemplate);
            $sTemplate = preg_replace("!".TAG_STARTER2."PL_(.*?)".TAG_ENDER2."!si","<? echo GetTagValue('PL_\\1'); ?>",$sTemplate);
			//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

            $sTemplate = preg_replace("!".TAG_STARTER."SYS_(.*?)\((.*?)\)".TAG_ENDER."!si","<? echo GetTagValue('SYS_\\1',\\2); ?>",$sTemplate);
            $sTemplate = preg_replace("!".TAG_STARTER."SYS_(.*?)".TAG_ENDER."!si","<? echo GetTagValue('SYS_\\1'); ?>",$sTemplate);

            $sTemplate = preg_replace("!".TAG_STARTER2."SYS_(.*?)\((.*?)\)".TAG_ENDER2."!si","<? echo GetTagValue('SYS_\\1',\\2); ?>",$sTemplate);
            $sTemplate = preg_replace("!".TAG_STARTER2."SYS_(.*?)".TAG_ENDER2."!si","<? echo GetTagValue('SYS_\\1'); ?>",$sTemplate);

            $sTemplate = preg_replace("!".TAG_STARTER_PARAM."PL_(.*?)\((.*?)\)".TAG_ENDER_PARAM."!si","GetTagValue('PL_\\1',\\2)",$sTemplate);
            $sTemplate = preg_replace("!".TAG_STARTER_PARAM."PL_(.*?)".TAG_ENDER_PARAM."!si","GetTagValue('PL_\\1')",$sTemplate);

		}
		else
		{

		}

		return $sTemplate;
	}

	function FindTagAndReplaceOnValue($sText)
	{
		$arrTags1 = array();
		$arrTags2 = array();

		// обрабатываем теги вида {{TAG_NAME(PARAMETERS)}}
		preg_match_all("!".TAG_STARTER."(\w+)\((.*?)\)".TAG_ENDER."!si", implode($sText, ''), $arrTags1, PREG_SET_ORDER);

		foreach ($arrTags1 as $pTag)
		{
			if (count($pTag)>2)
			{
                if (isset($pTag[2]))
                    eval("\$sValue = GetTagValue(\"$pTag[1]\", $pTag[2]);");
                else
				    $sValue = GetTagValue($pTag[1], $pTag[2]);
				$sText = ReplaceTagInText($sText, $pTag[1]."(".$pTag[2].")", $sValue);
			}
		}

		// обрабатываем теги вида {{TAG_NAME}}
		preg_match_all("!".TAG_STARTER."(.*?)".TAG_ENDER."!si", implode($sText, ''),$arrTags2);
		if (count($arrTags2)>1)
			// одинаковых тегов может быть много, и нужно для них только один раз получить значение,
			// поэтому удаляем из массива дубликаты
			$arrTags2 = array_unique($arrTags2[1]);

		foreach ($arrTags2 as $pTag)
		{
			if ($pTag != "")
			{
				$sValue = GetTagValue($pTag);
				$sText = ReplaceTagInText($sText, $pTag, $sValue);
			}
		}

		return $sText;
	}

	function GetTagValue($sTagName)
	{
	    // параметры нужны в виде массива.
        $arrParameters = func_get_args();
        if (count($arrParameters)>1)
            array_shift($arrParameters);
        else
            $arrParameters = null;

		$pCategoryInfo = null;
		$pProductInfo = null;
		$pProductInCart = null;
		$Result = "";


        $pCategoryInfo = CCMS::$currentCategory;
        if (!isset($pCategoryInfo))
            $pCategoryInfo = CCMS::$DB->FindCategoryByID($pProductInfo['SECTION_ID']);

        if (strstr($sTagName, 'PRODUCT_')!=false)
        {
            // если имеем теги вида PRODUCT_*(Параметр 1, ...)
            if (isset($arrParameters)&&isset($arrParameters[0]))
                $pProductInfo = CCMS::$DB->FindProductByID($arrParameters[0]);
        }

        if (!isset($pProductInfo))
            $pProductInfo = CCMS::$currentProduct;

        $pProductInCart = CShopingCart::FindProductById($pProductInfo['ID']);
        if (isset($pProductInCart)) $bProductInCart=true;

		if (isset($pCategoryInfo))
		{
            CCMS::$VARS[TAG_CURRENT_PAGE_FULL_CHPU_HREF] = CCMS::$sHttpPrefix.CCMS::$SETS[SETTINGS_SECTION_GLOBAL][SETTINGS_DOMAIN_NAME]."/".GetFullCategoryCPHULink($pCategoryInfo['ID']);
            CCMS::$VARS[TAG_CURRENT_ALTERNATE_MOBILE_PAGE_FULL_CHPU_HREF] = CCMS::$sHttpPrefix.'m.'.CCMS::$SETS[SETTINGS_SECTION_GLOBAL][SETTINGS_DOMAIN_NAME]."/".GetFullCategoryCPHULink($pCategoryInfo['ID']);

			switch ($sTagName)
			{
				case TAG_CATALOG_CATEGORY_ID: $Result = $pCategoryInfo['ID']; break;
				case TAG_CATALOG_CATEGORY_NAME: $Result = $pCategoryInfo['NAME']; break;
                case TAG_CATALOG_CATEGORY_DESCRIPTION: $Result = $pCategoryInfo['DESCRIPTION']; break;
				case TAG_CATALOG_CATEGORY_CHPU_LINK: $Result = GetFullCategoryCPHULink($pCategoryInfo['ID']); break;
				case TAG_CATALOG_CATEGORY_FULL_CHPU_LINK: $Result = CCMS::$sHttpPrefix.CCMS::$SETS[SETTINGS_SECTION_GLOBAL][SETTINGS_DOMAIN_NAME]."/".GetFullCategoryCPHULink($pCategoryInfo['ID']); break;
			}
		}

		if (isset($pProductInfo))
		{
            CCMS::$VARS[TAG_CURRENT_PAGE_FULL_CHPU_HREF] = CCMS::$sHttpPrefix.CCMS::$SETS[SETTINGS_SECTION_GLOBAL][SETTINGS_DOMAIN_NAME]."/".GetCHPULinkForProduct($pProductInfo);
            CCMS::$VARS[TAG_CURRENT_ALTERNATE_MOBILE_PAGE_FULL_CHPU_HREF] = CCMS::$sHttpPrefix.'m.'.CCMS::$SETS[SETTINGS_SECTION_GLOBAL][SETTINGS_DOMAIN_NAME]."/".GetCHPULinkForProduct($pProductInfo);

			switch ($sTagName) {
				case TAG_PRODUCT_ID:
					$Result = $pProductInfo['ID'];
					break;
				case TAG_PRODUCT_NAME:
					$Result = htmlspecialchars($pProductInfo['NAME']);
					break;
				case TAG_PRODUCT_DESCRIPTION:
					$Result = htmlspecialchars($pProductInfo['DESCRIPTION']);
					break;

				case TAG_PRODUCT_ID_WSD:
					$Result = $pProductInfo['ID'];
					break;
				case TAG_PRODUCT_NAME_WSD:
					$Result = $pProductInfo['NAME'];
					break;
				case TAG_PRODUCT_DESCRIPTION_WSD:
					$Result = $pProductInfo['DESCRIPTION'];
					break;

				case TAG_PRODUCT_MAIN_BIG_IMAGE_PATH:
					$Result = GetProductMainBigImageByCategoryAndProductId($pProductInfo['SECTION_ID'], $pProductInfo['ID']);
					break;
				case TAG_PRODUCT_MAIN_SMALL_IMAGE_PATH:
					$Result = GetProductMainSmallImageByCategoryAndProductId($pProductInfo['SECTION_ID'], $pProductInfo['ID']);
					break;
				case TAG_PRODUCT_BIG_IMAGES_PATH:
					$Result = GetProductBigImagePathByCategoryAndProductId($pProductInfo['SECTION_ID'], $pProductInfo['ID']);
					break;
				case TAG_PRODUCT_SMALL_IMAGES_PATH:
					$Result = GetProductSmallImagePathByCategoryAndProductId($pProductInfo['SECTION_ID'], $pProductInfo['ID']);
					break;
				case TAG_PRODUCT_IMAGE_COUNT:
					$Result = GetProductSmallImageCountByCategoryAndProductId($pProductInfo['SECTION_ID'], $pProductInfo['ID']);
					break;

				case TAG_PRODUCT_PRICE:
					$Result = GetPriceStringInFormat($pProductInfo['PRICE']);
					break;
				case TAG_PRODUCT_PRICE_DISCOUNT:
					$Result = 0;
					break;
				case TAG_PRODUCT_PRICE_WITH_DISCOUNT:
					$Result = GetPriceStringInFormat($pProductInfo['PRICE']);
					break;

                case TAG_PRODUCT_HTML_FULL_HREF:
                    $Result = '/'.GetCHPULinkForProduct($pProductInfo);
                    break;

                case TAG_PRODUCT_INFO_PARAMS:
                    // формируем скрытую информацию для работы скриптов корзины.
                    $Result  = 'product-id="'.$pProductInfo['ID'].'" ';
                    $Result .= 'product-section-id="'.$pProductInfo['SECTION_ID'].'" ';
                    $Result .= 'product-count="'.CShopingCart::GetProductCount($pProductInfo['ID']).'" ';
                    break;

                case TAG_PRODUCT_TO_CART_BTN_TEXT:
                    $Result = $bProductInCart?CCMS::$SETS[SETTINGS_SECTION_CATALOG][SETTINGS_CATALOG_BUY_BTN_IN_CART_TEXT]:CCMS::$SETS[SETTINGS_SECTION_CATALOG][SETTINGS_CATALOG_BUY_BTN_NOT_IN_CART_TEXT];
                    break;

                case TAG_PRODUCT_CATALOG_ITEM_BUY_BTN_STATE_CLASS:
                    // в зависимости, есть товар в корзине или нет, подставляем нужный класс, который влияет на статус кнопки.
                    if (
                        isset(CCMS::$SETS[SETTINGS_SECTION_CATALOG][SETTINGS_CATALOG_BUY_BTN_NOT_IN_CART_CLASS_NAME])&&
                        isset(CCMS::$SETS[SETTINGS_SECTION_CATALOG][SETTINGS_CATALOG_BUY_BTN_IN_CART_CLASS_NAME])
                    )
                    {
                        $Result =
                            ($bProductInCart==true)?
                                CCMS::$SETS[SETTINGS_SECTION_CATALOG][SETTINGS_CATALOG_BUY_BTN_IN_CART_CLASS_NAME]:
                                CCMS::$SETS[SETTINGS_SECTION_CATALOG][SETTINGS_CATALOG_BUY_BTN_NOT_IN_CART_CLASS_NAME];
                    }
                    break;

				case TAG_PRODUCT_COUNT_IN_CART:
				case TAG_PRODUCT_TOTAL_PRICE:
					$Result = "0";
					break;
                case TAG_SETTINGS:
                    $sSettingsSection = $arrParameters[0];
                    $sSettingsParam = $arrParameters[1];
                    //	пытаемся найти параметр в настройках.
                    if (isset($sSettingsSection)&&isset($sSettingsParam))
                        $Result =  CCMS::$SETS[$sSettingsSection][$sSettingsParam];
                    break;
			}
		}

		if (isset($ProductInCart))
		{
			switch ($sTagName) {
				case TAG_PRODUCT_COUNT_IN_CART:
					$Result = GetCountStringInFormat($pProductInCart['sCount']);
					break;
				case TAG_PRODUCT_TOTAL_PRICE:
					$Result = GetPriceStringInFormat($pProductInfo['PRICE']*$pProductInCart['sCount']);
					break;
			}
		}

		//	пытаемся найти тег в плагинах.
		if (strstr($sTagName, "PL_")!==FALSE)
		{
			$Result = CBaseAddonClass::GetTagValue($sTagName, $arrParameters);
		}

		switch ($sTagName) {

		    // работаем с тегами
            case TAG_HEADER:

                $sTemplateFile  = $arrParameters[0];
                $sTitle         = $arrParameters[1];
                $sKeywords      = $arrParameters[2];
                $Description    = $arrParameters[3];
                $AdditionalHtml = $arrParameters[4];

                CCMS::$VARS[TAG_HEADER_TITLE]               = $arrParameters[1];
                CCMS::$VARS[TAG_HEADER_META_KEYWORDS]       = $arrParameters[2];
                CCMS::$VARS[TAG_HEADER_META_DESCRIPTION]    = $arrParameters[3];
                CCMS::$VARS[TAG_HEADER_ADDITIONAL_HTML]     = $arrParameters[4];

                if ($sTemplateFile != '')
                {
                    GetFileContent(PROCESSED_TEMPLATE_DIRECTORY.$sTemplateFile.'.php', $Result, true);
                    $Result = implode($Result, '');
                }
                else
                    $Result = implode(GetTemplateForTag(TAG_HEADER, true),'');

                $Result = ReplaceTagInText($Result, TAG_HEADER_TITLE, $sTitle);
                $Result = ReplaceTagInText($Result, TAG_HEADER_META_KEYWORDS, $sKeywords);
                $Result = ReplaceTagInText($Result, TAG_HEADER_META_DESCRIPTION, $Description);
                $Result = ReplaceTagInText($Result, TAG_HEADER_ADDITIONAL_HTML, $AdditionalHtml);
                break;

            case TAG_FOOTER:

                $sTemplateFile = '';
                if (isset($arrParameters))
                    $sTemplateFile = $arrParameters[0];

                if ($sTemplateFile != '')
                {
                    GetFileContent(PROCESSED_TEMPLATE_DIRECTORY.$sTemplateFile.'.php', $Result, true);
                    $Result = implode($Result, '');
                }
                else
                    $Result = implode(GetTemplateForTag(TAG_FOOTER, true),'');

                break;

			case TAG_CURRENT_PAGE_FULL_CHPU_HREF:

				// пытаемся вначале опросить плагины, так как какие-то из них могут отвечать и за генерацию ЧПУ своих страниц
				$Result = CBaseAddonClass::GetCurrentCHPULinkForPage();
				if ($Result=="") {
					if (isset(CCMS::$VARS[$sTagName]))
						$Result = CCMS::$VARS[$sTagName];
				}
				else
				{
					$Result =  CCMS::$sHttpPrefix.CCMS::$SETS[SETTINGS_SECTION_GLOBAL][SETTINGS_DOMAIN_NAME]."/".$Result;
				}
				break;
            case TAG_CURRENT_ALTERNATE_MOBILE_PAGE_FULL_CHPU_HREF:

                // пытаемся вначале опросить плагины, так как какие-то из них могут отвечать и за генерацию ЧПУ своих страниц
                $Result = CBaseAddonClass::GetCurrentCHPULinkForPage();
                if ($Result=="") {
                    if (isset(CCMS::$VARS[TAG_CURRENT_ALTERNATE_MOBILE_PAGE_FULL_CHPU_HREF]))
                        $Result = CCMS::$VARS[TAG_CURRENT_ALTERNATE_MOBILE_PAGE_FULL_CHPU_HREF];
                }
                else
                {
                    $Result =  CCMS::$sHttpPrefix.'m.'.CCMS::$SETS[SETTINGS_SECTION_GLOBAL][SETTINGS_DOMAIN_NAME]."/".$Result;
                }
                break;

            default:
                if (isset(CCMS::$VARS[$sTagName]))
                    $Result = CCMS::$VARS[$sTagName];
                break;
		}

		return $Result;
	}

	function GetPriceStringInFormat($fPrice, $nPrecision = null)
	{
		if (!isset($nPrecision)) $nPrecision = CCMS::$SETS[SETTINGS_SECTION_PRODUCT][SETTINGS_PRODUCT_PRICE_PRECISION];
		return number_format($fPrice, $nPrecision,  '.', ' ');
	}

	function GetCountStringInFormat($fCount)
	{
		
		if ($fCount!=floor($fCount))
			return rtrim(rtrim(number_format($fCount, CCMS::$SETS[SETTINGS_SECTION_PRODUCT][SETTINGS_PRODUCT_COUNT_PRECISION], '.', ''), '0'), '.');
		else
            return $fCount;
	}

	function GetProductPropertyValue($nProductId, $sPropertyName)
	{
		$aProductInfo = CCMS::$DB->FindProductByID($nProductId);
		if (isset($aProductInfo))
			return trim($aProductInfo[$sPropertyName]);
		else
			return "";
	}

	// выводит на печать указанный параметр продукта, если он существует.
	function GetProductParameterByName($nProductId, $sParameterName)
	{
		$aProductInfo = CCMS::$DB->FindProductByID($nProductId);
		if (isset($aProductInfo))
			if (isset($aProductInfo[$sParameterName]))
			{
				return $aProductInfo[$sParameterName];
			}

		return " ";
	}

	function GetProductUnitById($nUnitId)
	{
		$Unit = null;

		switch ($nUnitId)
		{
			case 1:
				$Unit['ID'] = '1';
				$Unit['SYMBOL_RUS'] = 'м';
				$Unit['MEASURE_TITLE'] = 'Метр';
				break;
			case 9:
				$Unit['ID'] = '9';
				$Unit['SYMBOL_RUS'] = 'шт';
				$Unit['MEASURE_TITLE'] = 'Штука';
				break;
		}

		return $Unit;
	}

	function GetProductUnitSymbolById($nUnitId)
	{
		$Unit = GetProductUnitById($nUnitId);

		if (isset($Unit)) return $Unit['SYMBOL_RUS'];
		else return "";
	}

	$BONUS_PERCENT = 10;
?>