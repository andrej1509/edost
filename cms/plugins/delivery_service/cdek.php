<?

include_once ($_SERVER['DOCUMENT_ROOT'].'/cms/code/settings.php');
include_once (CMS_FILE_LOCAL_PATH_PHP.'plugin_base_class.php');
require_once (CMS_FILE_LOCAL_PATH_PHP.'bitrix24.php');

require(PLUGINS_CODE_DIRECTORY."delivery_service/cdek_sdk/CdekSdk.php");


class CCDEK
{
    /** @var CdekSdk $pCdekSdk */
    private $pCdekSdk = null;

    const CDEK_ACCOUNT = '';
    const CDEK_PASSWORD = '';
    const CDEK_AGENCY_FEE = 3; // сумма агентского вознаграждения за наложку

    //const CDEK_USABLE_DEAL_STAGE = [ 'NEW', '16', '6', '17' ]; // используемые стадии сделок
//    const CDEK_USABLE_DEAL_STAGE = [ '16' ]; // используемые стадии сделок. Пока используется "Адрес проверен"

    const SEND_CITY_CODE = 419;  /*Чебоксары*/
    const DEFAULT_WEIGHT = 500;  /* Вес в граммах по умолчанию. У нас нет тяжелых изделий, поэтому можно так. */
    const DEFAULT_PRODUCT_NAME =  "Карнавальная продукция";
    const TABLENAME_PICKUP_POINTS = 'cdek_pickup_points';
    const ORDER_SELLER_NAME = 'Егорова';
//    const ORDER_SELLER_ADDRESS = array(
//                                    'Street' => 'ул.',
//                                    'House' => '1',
//                                    'Flat' => '2');

    public static $ERROR_DESCRIPTION = array(
        'Отсутствие обязательного атрибута: PVZCODE' => 'Не задано или пустое поле "Идентификатор пункта самовывоза"',
        'Код города получателя отсутствует в базе СДЭК: RecCityCode=0' => 'Не задано или пустое поле "Идентификатор пункта самовывоза"',
        'Невалидное значение габаритов: SizeC=' => 'Не задано или пустое поле "Размеры упаковки, см"',
    );

    private $dbConn = null;

    /** @var CBitrix24 $pBitrix24 */
    private $pBitrix24 = null;
    private $arrBitrixPackageDimensions = array();

    function __construct($dbConnection)
    {
        $this->pBitrix24 = new CBitrix24();
        //$this->pCdekSdk = new CdekSdk(self::CDEK_ACCOUNT, self::CDEK_PASSWORD, true);
/*
        $this->dbConn = $dbConnection;

        //$res =  $this->pCdekSdk->statusReport(CdekSdk::URL_STATUS_REPORT_MAIN, 1, null,[['DispatchNumber'=>1060258453]]);
        //$r=$this->pCdekSdk->pvzList();

        $arrDealFields = $this->pBitrix24->RestCommand('crm.deal.fields');
        if (isset($arrDealFields[CBitrix24Config::FIELD_NAME_PACKAGE_DIMENSIONS]))
        {
            $arrDealFields[CBitrix24Config::FIELD_NAME_PACKAGE_DIMENSIONS]['items'];
            foreach ( $arrDealFields[CBitrix24Config::FIELD_NAME_PACKAGE_DIMENSIONS]['items'] as $arrPackage)
            {
                $this->arrBitrixPackageDimensions[$arrPackage['ID']] = $arrPackage['VALUE'];
            }
        }
        */
    }

    /**
     * Для всех сделок в стадии "  " создается список заказов в личном кабинете
     * Возвращает true или массив с ошибками, который вернул СДЭК
     * @return array
     */
    public function CreateOrders($bUpdateTrekCodeInBitrix24 = false)
    {
        // ищем сделки в нужном нам статусе
        $arrDealIDs = $this->pBitrix24->GetList(
            'crm.deal.list',
            ['STAGE_ID'=> self::CDEK_USABLE_DEAL_STAGE, CBitrix24Config::FIELD_NAME_DELIVERY_TYPE => CBitrix24Config::DELIVERY_TYPE_CDEK],
            ['ID']);

        $sRequestDate = date('c',time());

        $arrOrders = array();
        $arrInfoRequestParams = array();

        if (isset($arrDealIDs))
        {
            foreach ($arrDealIDs as $ID)
            {
                // получаем все параметры сделки
                $arrDealInfo = $this->pBitrix24->RestCommand('crm.deal.get', ['id'=>$ID['ID']]);
                if (isset($arrDealInfo))
                {
                    $arrContactInfo = $this->pBitrix24->RestCommand('crm.contact.get', ['ID'=>$arrDealInfo['CONTACT_ID']]);

                    $sPvzCode = null;
                    $nCityCode = 0;
                    $nTariffTypeCode = 0;
                    $arrAddressInfo = array();

                    if (isset($arrDealInfo[CBitrix24Config::FIELD_NAME_COURIER_DELIVERY])&&($arrDealInfo[CBitrix24Config::FIELD_NAME_COURIER_DELIVERY]==1))
                    {
                        $nTariffTypeCode = 137; // Тариф склад-дверь для интернет-магазинов
                        // Доставка курьером
                        // разбираем строку адреса
                        // должен быть формат Улица, Дом Корпус Строение.
                        $arrAddressParts = explode(',', $arrContactInfo['ADDRESS']);

                        if (count($arrAddressParts)>=2)
                        {
                            $pPVZInfo = $this->dbConn->query("SELECT * FROM ?s.?s WHERE City=\"?s\"", CCMS::$DB->sDatabaseName, self::TABLENAME_PICKUP_POINTS, $arrContactInfo['ADDRESS_CITY'])->fetch_assoc();
                            if (isset($pPVZInfo)&&is_array($pPVZInfo))
                            {
                                $nCityCode = $pPVZInfo['CityCode'];
                                // доставка курьером, нужно указать адрес
                                $arrAddressInfo['Street'] = $arrAddressParts[0];
                                $arrAddressInfo['House'] = $arrAddressParts[1];
                                if (isset($arrContactInfo['ADDRESS_2']))
                                    $arrAddressInfo['Flat'] = $arrContactInfo['ADDRESS_2']; // Да, это так они квартиру обозначили.
                            }
                        }
                    }
                    else if (isset($arrDealInfo[CBitrix24Config::FIELD_NAME_PVZ_CODE])&&($arrDealInfo[CBitrix24Config::FIELD_NAME_PVZ_CODE]!=''))
                    {
                        $nTariffTypeCode = 136; // Тариф склад-склад для интернет-магазинов

                        $sPvzCode = $arrDealInfo[CBitrix24Config::FIELD_NAME_PVZ_CODE];
                        $pPVZInfo = $this->dbConn->query("SELECT * FROM ?s.?s WHERE Code=\"?s\"", CCMS::$DB->sDatabaseName, self::TABLENAME_PICKUP_POINTS, $sPvzCode)->fetch_assoc();


                        if (isset($pPVZInfo)&&isset($pPVZInfo['CityCode']))
                        {
                            $nCityCode = $pPVZInfo['CityCode'];
                            $arrAddressInfo = ['PvzCode' => $sPvzCode ];
                        }
                    }

                    // размеры упаковки
                    $arrPackageDimensions = array();
                    if (isset($arrDealInfo[CBitrix24Config::FIELD_NAME_PACKAGE_DIMENSIONS]))
                    {


                        if (isset($this->arrBitrixPackageDimensions[$arrDealInfo[CBitrix24Config::FIELD_NAME_PACKAGE_DIMENSIONS]]))
                        {
                            $sDimensions = explode('-', $this->arrBitrixPackageDimensions[$arrDealInfo[CBitrix24Config::FIELD_NAME_PACKAGE_DIMENSIONS]]);
                            $arrPackageDimensions = explode('х', $sDimensions[0]);
                        }

                        if (count($arrPackageDimensions)!=3)
                        {
                            // все размеры должны быть, значит где-то ошибка в заполнеии списка в CRM
                            $arrPackageDimensions = [0,0,0];// это будет вызывать ошибку и заставит обратить внимание на этот параметр;
                        }
                    }

                    // заполняем массив заказа для сделки
                    $arrOrder =  [
                        'Number' => $arrDealInfo['ID'],
                        'SellerName' => self::ORDER_SELLER_NAME,
                        'SendAddress' => self::ORDER_SELLER_ADDRESS,
                        'SendCityCode' => self::SEND_CITY_CODE,
                        'DeliveryRecipientCost' => 0,
                        'RecCityCode' => $nCityCode,
                        'Phone' => $arrContactInfo['PHONE'][0]['VALUE'],
                        'RecipientName' => $arrContactInfo['LAST_NAME'].' '.$arrContactInfo['NAME'].' '.$arrContactInfo['SECOND_NAME'],
                        'Comment' => "",
                        'TariffTypeCode'=> $nTariffTypeCode,
                        'Address' => $arrAddressInfo,
                        'Packages' => [
                            [
                                'Number' => 1,
                                'BarCode' => 1,
                                'Weight' => self::DEFAULT_WEIGHT,
                                'SizeA' => $arrPackageDimensions[0],
                                'SizeB' => $arrPackageDimensions[1],
                                'SizeC' => $arrPackageDimensions[2],
                                'Items' => [
                                    [
                                        'WareKey' => "1",
                                        'Cost' => (float)$arrDealInfo['OPPORTUNITY'],
                                        'Payment' =>
                                            ($arrDealInfo[CBitrix24Config::FIELD_NAME_PAYMENT_TYPE]==CBitrix24Config::PAYMENT_TYPE_CASH_ON_DELIVERY)?
                                                ((float)$arrDealInfo['OPPORTUNITY']
                                                - (float)(isset($arrDealInfo[CBitrix24Config::FIELD_NAME_PARTIAL_PAYMENT])?(float)$arrDealInfo[CBitrix24Config::FIELD_NAME_PARTIAL_PAYMENT]:0))
                                                :0,
                                        'Weight' => self::DEFAULT_WEIGHT,
                                        'Amount' => 1,
                                        'Comment' => self::DEFAULT_PRODUCT_NAME,
                                    ],
                                ],
                            ],
                        ],
                        /*
                        'AddServices' => [
                            [
                                'ServiceCode' => 3,
                            ],
                        ],
                        */
                    ];

                    array_push($arrOrders, $arrOrder);
                    // формируем массив для вызова infoRequest
                    array_push($arrInfoRequestParams, [ 'Number' => $arrDealInfo['ID'], 'Date' => $sRequestDate ]);
                }
            }
        }


        if (count($arrOrders)>0)
        {
            $arrResult = $this->pCdekSdk->deliveryRequest(1, $sRequestDate, $arrOrders);

            // форматируем результат по заказам, смотрим, есть ли ошибки
            $arrOrderErrors = array();
            $arrCurrentOrderErrors = null;
            $sLastNumber = '';
            foreach ($arrResult['result']['response']['Order'] as $pErrorInfo)
            {
                $sFiledName = $pErrorInfo['@attributes']['Number'];
                if (!isset($arrOrderErrors[$sFiledName]))
                {
                    $arrOrderErrors[$sFiledName]['Ошибки'] = array();

                    if (isset($pErrorInfo['@attributes']['DispatchNumber']))
                    {
                        // Этот заказ создался, поэтому обновляем информацию в битрикс24
                        // Если заказ уже создан, то DispatchNumber не передается

                        if ($bUpdateTrekCodeInBitrix24)
                        {
                            // заполняем поле "Трек-код в Битрикс24"
                            $arrInfoRequestResult = $this->pCdekSdk->infoRequest([['DispatchNumber' => $pErrorInfo['@attributes']['DispatchNumber']]]);
                            if (isset($arrInfoRequestResult['result']['InfoReport']['Order']))
                            {
                                if ($this->UpdateBitrix24DealFromInfoRequest($arrInfoRequestResult['result']['InfoReport']['Order']) !== true)
                                    array_push(
                                        $arrOrderErrors[$arrInfoRequestResult['result']['InfoReport']['Order']['@attributes']['Number']]['Ошибки'],
                                        'Ошибка обновления поля "Трек-код" и "Сумма доставки в Битрикс24"');
                            }
                        }
                    }
                }

                array_push($arrOrderErrors[$sFiledName]['Ошибки'], $pErrorInfo['@attributes']['Msg']);
            }
/*
            if ($bUpdateTrekCodeInBitrix24)
            {
                // заполняем поле "Трек-код в Битрикс24"
                $arrInfoRequestResult = $this->pCdekSdk->infoRequest($arrInfoRequestParams);
                if (isset($arrInfoRequestResult['result']['InfoReport']['Order']))
                {
                    if (count($arrOrders) > 1)
                    {
                        foreach ($arrInfoRequestResult['result']['InfoReport']['Order'] as $InfoRequestResult)
                            if ($this->UpdateBitrix24DealFromInfoRequest($InfoRequestResult) !== true)
                                array_push($arrOrderErrors[$InfoRequestResult['@attributes']['Number']]['Ошибки'], 'Ошибка обновления поля "Трек-код" и "Сумма доставки в Битрикс24"');

                    }
                    else
                    {
                        if ($this->UpdateBitrix24DealFromInfoRequest($arrInfoRequestResult['result']['InfoReport']['Order']) !== true)
                            array_push($arrOrderErrors[$arrInfoRequestResult['result']['InfoReport']['Order']['@attributes']['Number']]['Ошибки'], 'Ошибка обновления поля "Трек-код" и "Сумма доставки в Битрикс24"');
                    }
                }
            }
*/
        }
        else
        {
            $arrOrderErrors['']['Ошибки'][0] = 'Сделок для создания не найдено.';
        }

        return $arrOrderErrors;
    }

    private function UpdateBitrix24DealFromInfoRequest($arrInfoRequestResult)
    {
        if ($arrInfoRequestResult['@attributes']['ErrorCode'] != 'ERR_INVALID_NUMBER')
        {
            // заполняем поле "Трек-код" и "Стоимость доставки"
            $arrDealData = array();
            $arrDealData['id'] = $arrInfoRequestResult['@attributes']['Number'];
            $arrDealData['fields'][CBitrix24Config::FIELD_NAME_TREK_NUMBER] = $arrInfoRequestResult['@attributes']['DispatchNumber'];

            // Рассчитываем стоимость доставки
            $arrDealData['fields'][CBitrix24Config::FIELD_NAME_DELIVERY_PAYMENT] =
                $arrInfoRequestResult['@attributes']['DeliverySum'];

            // если наложенный платеж, то прибавляем агентское вознаграждение
            if ($arrInfoRequestResult['@attributes']['CashOnDeliv']>0)
            {
                $arrDealData['fields'][CBitrix24Config::FIELD_NAME_DELIVERY_PAYMENT] +=
                    $arrInfoRequestResult['@attributes']['CashOnDeliv']*self::CDEK_AGENCY_FEE/100;
            }

            // учитываем страховку
            if (isset($arrInfoRequestResult['AddedService']['@attributes'])
                &&($arrInfoRequestResult['AddedService']['@attributes']['ServiceCode']==2))
            {
                $arrDealData['fields'][CBitrix24Config::FIELD_NAME_DELIVERY_PAYMENT] += $arrInfoRequestResult['AddedService']['@attributes']['Sum'];
            }

            $arrDealData['fields'][CBitrix24Config::FIELD_NAME_SHIP_DATE] = date('Y-m-d');

            return $this->pBitrix24->RestCommand('crm.deal.update', $arrDealData);
        }
        else
            return false;
    }

    public function UpdatePickupPointsTable()
    {
        // создаем таблицу, если ее нет
        $arrPVZList = $this->pCdekSdk->pvzList();

        $sCreateTable = 'CREATE TABLE ?s.' . self::TABLENAME_PICKUP_POINTS . ' (?s) ENGINE = INNODB CHARACTER SET utf8 COLLATE utf8_general_ci;';
        if (!CCMS::$DB->IsTableExistsInDatabase(self::TABLENAME_PICKUP_POINTS))
        {
            $arrFields = $arrPVZList['result']['PvzList']['Pvz'][0]['@attributes'];
            $sFields = '';
            foreach ($arrFields as $sParamName => $pField)
            {
                $sFields .= '`' . $sParamName . '` TEXT DEFAULT NULL,';
            }
            $sFields = substr($sFields, 0, strlen($sFields) - 1);
            $sCreateTable = sprintf($sCreateTable, $sFields);

            $this->dbConn->query($sCreateTable, CCMS::$DB->sDatabaseName, $sFields);
        }

        $this->dbConn->query('DELETE FROM ?s.?s', CCMS::$DB->sDatabaseName, self::TABLENAME_PICKUP_POINTS);

        // заполняем таблицу данными
        foreach ( $arrPVZList['result']['PvzList']['Pvz'] as $pPVZ)
        {
            $sQuery = "INSERT INTO ".CCMS::$DB->sDatabaseName.".".self::TABLENAME_PICKUP_POINTS." VALUES(%s)";
            $sValues = '';
            foreach ($pPVZ['@attributes'] as $sParamName => $sValue)
                $sValues .= "'".$sValue ."',";
            $sValues = substr($sValues, 0, strlen($sValues) - 1);
            $sQuery = sprintf($sQuery, $sValues);
            $this->dbConn->query($sQuery);
        }
    }
}
?>