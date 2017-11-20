<?

include_once ($_SERVER['DOCUMENT_ROOT'].'/cms/code/settings.php');
include_once (CMS_FILE_LOCAL_PATH_PHP.'plugin_base_class.php');
require_once (CMS_FILE_LOCAL_PATH_PHP.'bitrix24.php');


class CRussianPost
{
    const RUSSIAN_POST_SERVER = 'https://otpravka-api.pochta.ru';
    const RUSSIAN_POST_ACCESS_TOKEN = '';
    const RUSSIAN_POST_ACCESS_KEY = '';

    const TEMPLATE_URL_BATCH_SHIPMENT = '/1.0/batch/%s/shipment';
    const TEMPLATE_URL_SHIPMENT_SEARCH = '/1.0/shipment/search?query=%s';
    const URL_ARCHIVE = '/1.0/archive';
    const URL_BATCH_LAST_SHIPMENTS= '/1.0/batch?size=10&sort=desc&page=1';

    const SEND_CITY_CODE = 419;  /*Чебоксары*/
    const DEFAULT_WEIGHT = 500;  /* Вес в граммах по умолчанию. У нас нет тяжелых изделий, поэтому можно так. */
    const DEFAULT_PRODUCT_NAME =  "Карнавальная продукция";
    const TABLENAME_PICKUP_POINTS = 'cdek_pickup_points';
    const ORDER_SELLER_NAME = 'ИП Голиков Иван Николаевич';
//    const ORDER_SELLER_ADDRESS = array(
//        'Street' => 'ул. Ильенко',
//        'House' => '5',
//        'Flat' => '109');

    private static $arrRequestHeaders = null;

    public static $ERROR_DESCRIPTION = array(
        'Отсутствие обязательного атрибута: PVZCODE' => 'Не задано или пустое поле "Идентификатор пункта самовывоза"',
        'Код города получателя отсутствует в базе СДЭК: RecCityCode=0' => 'Не задано или пустое поле "Идентификатор пункта самовывоза"',
        'Невалидное значение габаритов: SizeC=' => 'Не задано или пустое поле "Размеры упаковки, см"',
    );

    /** @var CBitrix24 $pBitrix24 */
    private $pBitrix24 = null;
    private $arrPackageDimensions = array();

    private $dbConn = null;

    function __construct($dbConnection)
    {
        $this->dbConn = $dbConnection;

        self::$arrRequestHeaders = array(
            "Content-Type: aapplication/json;charset=UTF-8",
            "Accept: application/json;charset=UTF-8",
            "Authorization: AccessToken ".self::RUSSIAN_POST_ACCESS_TOKEN,
            "X-User-Authorization: Basic ".self::RUSSIAN_POST_ACCESS_KEY
        );

        $this->pBitrix24 = new CBitrix24();

        //$this->call('/1.0/counterpart/balance');
        //$this->call('/1.0/backlog/13225');
        //$this->call('/1.0/shipment/search', null, [ 'query'=> 42897116846242 ]);
        //$this->call('/1.0/backlog/search', null, [ 'query'=> 13225 ]);
        //$this->call('/1.0/archive');
        //$this->call('/1.0/batch/24/shipment');
        //$this->call(self::URL_BATCH_LAST_SHIPMENTS);
        //$this->call('/1.0/batch');
        //$this->UpdateDealsInBitrix24(strtotime(date('Y-m-d 00:00:00')),strtotime(date('Y-m-d 23:59:59')));
        //$this->UpdateDealsInBitrix24(strtotime('2017-10-26 00:00:00'),strtotime('2017-10-26 23:59:59'));
    }

    /**
     * @param $dtBeginDate
     * @param $dtEndDate
     * @return bool
     */
    public function UpdateDealsInBitrix24($dtBeginDate, $dtEndDate)
    {
        $arrErrors = array();
        // ищев партии в архиве за указанный интервал дат
        $arrArchive = $this->call(self::URL_ARCHIVE);
        if (is_array($arrArchive)&&count($arrArchive)>0)
        {
            foreach ($arrArchive as $objBatch)
            {
                if (
                    strtotime($objBatch->{'list-number-date'})>=$dtBeginDate&&
                    strtotime($objBatch->{'list-number-date'})<=$dtEndDate)
                {
                    // ищем посылки в партии
                    $arrPackages = $this->call(sprintf(self::TEMPLATE_URL_BATCH_SHIPMENT, $objBatch->{'batch-name'}));
                    if (is_array($arrPackages)&&count($arrPackages)>0)
                    {
                        foreach ($arrPackages as $objPackage)
                        {
                            $arrDealData = array();
                            // ID сделки
                            $arrDealData['id'] = $objPackage->{'order-num'};
                            // Трек-код
                            $arrDealData['fields'][CBitrix24Config::FIELD_NAME_TREK_NUMBER] = $objPackage->{'barcode'};
                            // Стоимость доставки
                            $arrDealData['fields'][CBitrix24Config::FIELD_NAME_DELIVERY_PAYMENT] =
                                ($objPackage->{'ground-rate-with-vat'} + $objPackage->{'insr-rate-with-vat'})/100;
                            // Дата отгрузки
                            $arrDealData['fields'][CBitrix24Config::FIELD_NAME_SHIP_DATE] = $objBatch->{'list-number-date'};

                            // обновляем сделку
                            $oError = null;
                            $Result = $this->pBitrix24->RestCommand('crm.deal.update', $arrDealData, true, $oError);

                            if (!isset($Result)||($Result!==true))
                            {
                                array_push($arrErrors, 'Ошибка обновления сделки №'.$objPackage->{'order-num'}.': '.$oError['error_description']);
                            }
                        }
                    }
                }
            }
            return true;
        }
        return false;
    }

    /** Общий метод для отправки запроса
     *
     * @param       $method - метод (GET, POST, ....)
     * @param       $url - на какой url отправлять запрос
     * @param       $decodeXml - конвертировать ли xml в массив
     * @param mixed $body - тело запроса (для метода POST)
     * @param array $query - параметры GET запроса
     *
     * @return array
     * @throws Exception
     */
    protected function call($url, $queryData = null, array $queryGetParam = [], $method = 'GET')
    {
        $result = false;

        $queryUrl = self::RUSSIAN_POST_SERVER.$url;
        if (isset($queryGetParam)&&count($queryGetParam)>0)
        {
            $queryUrl .= '?'.http_build_query($queryGetParam);
        }
        $curl = curl_init();

        //curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        //curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

        $arrCurlOpt = array(
            //CURLOPT_HEADER => 1,
            CURLOPT_HTTPHEADER => self::$arrRequestHeaders,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSL_VERIFYPEER => 1,
            CURLOPT_URL => $queryUrl,
        );

        if ($method=='POST')
        {
            $arrCurlOpt[CURLOPT_POST] = 1;
            $arrCurlOpt[CURLOPT_POSTFIELDS] = $queryData;
        }

        curl_setopt_array($curl, $arrCurlOpt);

        $pResponse = curl_exec($curl);

        if ($pResponse!==false)
        {
            $result = json_decode($pResponse);
        }
        curl_close($curl);

        return $result;
    }
}
?>