<?

define("ERR_BITRIX24_CONFIGURATION_ERROR", 1);
define("ERR_BITRIX24_CONNECTION_FAILED", 2);
define("ERR_BITRIX24_CONFIGURATION_DISABLE_CREATE_LEADS", 10);

//define('DEBUG_FILE_NAME', 'bitrix24_log.txt'); // if you need read debug log, you should write unique log name
define('DEBUG_FILE_NAME', ''); // if you need read debug log, you should write unique log name
define('CONFIG_FILE_NAME', 'bitrix24_config.php'); // if you need read debug log, you should write unique log name

require_once ('bitrix24_config.php');

class CBitrix24
{
    public $sDomain = "";
    private $sClientId = "";
    private $sClientSecret = "";
    private $sRefreshToken = "";
    private $sScope = "";
    private $sAccessToken = "";
    private $sWebHookPath = "";
    private $sSourceId = "WEB";
    private $sLeadPrefix = "";

    function __construct()
    {
        $this->sDomain =        CCMS::$SETS[SETTINGS_SECTION_BITRIX24][SETTINGS_BITRIX24_CRM_HOST];
        $this->sWebHookPath =   CCMS::$SETS[SETTINGS_SECTION_BITRIX24][SETTINGS_BITRIX24_WEBHOOK];
        if (!isset($this->sWebHookPath))
        {
            $this->sClientId =      CCMS::$SETS[SETTINGS_SECTION_BITRIX24][SETTINGS_BITRIX24_CLIENT_ID];
            $this->sClientSecret =  CCMS::$SETS[SETTINGS_SECTION_BITRIX24][SETTINGS_BITRIX24_CLIENT_SECRET];
            $this->sRefreshToken =  CCMS::$SETS[SETTINGS_SECTION_BITRIX24][SETTINGS_BITRIX24_REFRESH_TOKEN];
            $this->sScope =         CCMS::$SETS[SETTINGS_SECTION_BITRIX24][SETTINGS_BITRIX24_SCOPE];

            if (isset(CCMS::$SETS[SETTINGS_SECTION_BITRIX24][SETTINGS_BITRIX24_SOURCE_ID]))
                $this->sSourceId =      CCMS::$SETS[SETTINGS_SECTION_BITRIX24][SETTINGS_BITRIX24_SOURCE_ID];
            if (isset(CCMS::$SETS[SETTINGS_SECTION_BITRIX24][SETTINGS_BITRIX24_LEAD_PREFIX]))
                $this->sLeadPrefix =    CCMS::$SETS[SETTINGS_SECTION_BITRIX24][SETTINGS_BITRIX24_LEAD_PREFIX];

            $this->RestAuth();
        }


    }

    /**
     * Save application configuration.
     * WARNING: this method is only created for demonstration, never store config like this
     *
     * @param $params
     * @return bool
     */
    function SaveParams()
    {
        CCMS::$SETS[SETTINGS_BITRIX24_REFRESH_TOKEN] = $this->sRefreshToken;
        $Obj = new CCRMSettings(SETTINGS_INI_FILE_NAME);
        $Obj->Load();
        $Obj->arrSettings[SETTINGS_SECTION_BITRIX24][SETTINGS_BITRIX24_REFRESH_TOKEN] = $this->sRefreshToken;
        $Obj->Save();

        return true;
    }

    /**
     * Send rest query to Bitrix24.
     *
     * @param $method - Rest method, ex: methods
     * @param array $params - Method params, ex: Array()
     * @param array $auth - Authorize data, ex: Array('domain' => 'https://test.bitrix24.com', 'access_token' => '7inpwszbuu8vnwr5jmabqa467rqur7u6')
     * @param boolean $authRefresh - If authorize is expired, refresh token
     * @return mixed
     */
    /*
    public function RestCommand($method, array $params = Array(), $bReturnResultFiled = true, &$oError = false, $authRefresh = true)
    {
        $queryUrl = "";
        $queryData = null;
        if (isset($this->sWebHookPath)) {
            $queryUrl = $this->sWebHookPath . "/" . $method;
            $queryData = http_build_query($params);
            $this->WriteToLog(Array('URL' => $queryUrl, 'PARAMS' => $params), $method.': Send data');
        }
        else
        {
            $queryUrl = "https://".$this->sDomain."/rest/".$method;
            $queryData = http_build_query(array_merge($params, array("auth" => $this->sAccessToken)));
            $this->WriteToLog(Array('URL' => $queryUrl, 'PARAMS' => array_merge($params, array("auth" => $this->sAccessToken))), $method.': Send data');
        }

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

        curl_setopt_array($curl, array(
            CURLOPT_POST => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSL_VERIFYPEER => 1,
            CURLOPT_URL => $queryUrl,
            CURLOPT_POSTFIELDS => $queryData,
        ));

        $result = curl_exec($curl);

        //echo $queryUrl;
        //echo $result;

        $result = json_decode($result, 1);



        curl_close($curl);

        if ($authRefresh && isset($result['error']) && in_array($result['error'], array('expired_token', 'invalid_token')))
        {
            $auth = $this->RestAuth();
            if ($auth)
            {
                $result = $this->RestCommand($method, $params, $bReturnResultFiled, $oError, false);
            }
        }

        if ($bReturnResultFiled) {
            if (isset($result['result']))
            {
                $this->WriteToLog(
                    Array(
                        'URL' => $queryUrl,
                        'RESULT' => $result['result']),
                        $method.': RESULT Send data');
                return $result['result'];
            }
            else {
                if (($oError!==false) && isset($result['error']))
                    $oError = $result;

                if (isset($result['error']))
                {
                    $this->WriteToLog(
                        Array(

                            'URL' => $queryUrl,
                            'ERROR' => $result['error'],
                            'ERROR_DESCRIPTION' => $result['error_description']),
                        $method.': ERROR Send data');
                }

                return null;
            }
        }
        else
            return $result;
    }
    */
    public function RestCommand($method, $params = null, $bReturnResultFiled = true, &$oError = false, $arrResult = null)
    {
        // Битрикс позволяет делать запросы по рест апи не чаще раз в 500мс.
        usleep(510000);

        $queryUrl = "";
        $queryData = null;
        if (isset($this->sWebHookPath)) {
            $queryUrl = $this->sWebHookPath . "/" . $method;
            if (isset($params))
                $queryData = http_build_query($params);
            else
                $queryData = http_build_query(array());
            $this->WriteToLog(Array('URL' => $queryUrl, 'PARAMS' => $params), $method.': Send data');
        }
        else
        {
            return false;
        }

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

        curl_setopt_array($curl, array(
            CURLOPT_POST => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSL_VERIFYPEER => 1,
            CURLOPT_URL => $queryUrl,
            CURLOPT_POSTFIELDS => $queryData,
        ));

        $result = curl_exec($curl);

        $result = json_decode($result, 1);

        curl_close($curl);

        if ($bReturnResultFiled) {
            if (isset($result['result']))
            {
                $this->WriteToLog(
                    Array(
                        'URL' => $queryUrl,
                        'RESULT' => $result['result']),
                    $method.': RESULT Send data');

                if (isset($result['next']))
                {
                    if (!isset($arrResult)) $arrResult = $result['result'];
                    else $arrResult = array_merge($arrResult, $result['result']);

                    $params['start'] = $result['next'];

                    return $this->RestCommand($method, $params, $bReturnResultFiled, $oError, $arrResult);
                }
                else
                {
                    if (isset($arrResult))
                        return array_merge($arrResult, $result['result']);
                    else
                        return $result['result'];
                }
            }
            else {
                if (($oError!==false) && isset($result['error']))
                    $oError = $result['error'];

                if (isset($result['error']))
                {
                    $this->WriteToLog(
                        Array(

                            'URL' => $queryUrl,
                            'ERROR' => $result['error'],
                            'ERROR_DESCRIPTION' => $result['error_description']),
                        $method.': ERROR Send data');
                }

                return null;
            }
        }
        else
            return $result;
    }


    /**
     * Get new authorize data if you authorize is expire.
     *
     * @param array $auth - Authorize data, ex: Array('domain' => 'https://test.bitrix24.com', 'access_token' => '7inpwszbuu8vnwr5jmabqa467rqur7u6')
     * @return bool|mixed
     */
    public function RestAuth()
    {
        if (isset($this->sWebHookPath)) return false;

        if (($this->sClientId == "") || ($this->sClientSecret == ""))
            return false;

        if(!isset($this->sRefreshToken) || !isset($this->sScope) || !isset($this->sDomain))
            return false;

        $queryUrl = 'https://'.$this->sDomain.'/oauth/token/';
        $queryData = http_build_query($queryParams = array(
            'grant_type' => 'refresh_token',
            'client_id' => $this->sClientId,
            'client_secret' => $this->sClientSecret,
            'refresh_token' => $this->sRefreshToken,
            'scope' => $this->sScope,
        ));

        $this->WriteToLog(Array('URL' => $queryUrl, 'PARAMS' => $queryParams), 'Request auth data');

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $queryUrl.'?'.$queryData,
        ));

        $result = curl_exec($curl);
        curl_close($curl);

        $result = json_decode($result, 1);

        if (!isset($result['error']))
        {
            $this->WriteToLog($result, 'Request auth data');
            $this->sAccessToken = $result['access_token'];
            $this->sRefreshToken = $result['refresh_token'];
            //!!!$this->SaveParams();
        }
        else
        {
            $this->WriteToLog(array($result), 'ERROR: Request auth data');
            $result = false;
        }

        return $result;
    }

    /**
     * Write data to log file. (by default disabled)
     * WARNING: this method is only created for demonstration, never store log file in public folder
     *
     * @param mixed $data
     * @param string $title
     * @return bool
     */
    function WriteToLog($data, $title = '')
    {
        if (!DEBUG_FILE_NAME)
            return false;

        $log = "\n------------------------\n";
        $log .= date("Y.m.d G:i:s")."\n";
        $log .= (strlen($title) > 0 ? $title : 'DEBUG')."\n";
        $log .= print_r($data, true);
        $log .= "\n------------------------\n";

        file_put_contents(__DIR__."/".DEBUG_FILE_NAME, $log, FILE_APPEND);

        return true;
    }

    /**
     * Формирует имя таблицы с описанием полей для метода.
     * !!! Но не проверяет ее наличие.
     * @param $sRestMethod
     * @return string
     */
    function GetFieldsMethodNameForRestMethod($sRestMethod)
    {
        return substr($sRestMethod, 0, strrpos($sRestMethod, '.')).'.fields';
    }

    /**
     * Выдает описание колонок таблицы reat-метода, для которых присутствует метод crm.*.fields
     * Также если таблица crm.*.fields отсутствует в базе, то создает и заполняет ее на основе информации Битрикс24
     * @param $sRestMethod
     * @return array
     */
    function GetFieldsForRestMethod($sRestMethod)
    {
        $sFiledsMethod = $this->GetFieldsMethodNameForRestMethod($sRestMethod);
        $arrFields = $this->RestCommand($sFiledsMethod);
        return $arrFields;
    }

    function GetFieldsNamesForRestMethod($sRestMethod)
    {
        $arrFieldNames = array();
        $arrFields = $this->GetFieldsForRestMethod($sRestMethod);
        if (isset($arrFields)&&(count($arrFields)>0))
        {
            foreach ($arrFields as $sFieldName => $pValue)
                array_push($arrFieldNames,$sFieldName);
        }
        return $arrFieldNames;
    }

    public function GetLeadById($nLeadId)
    {
        return $this->RestCommand('crm.lead.get', array('id'=>$nLeadId));
    }

    public function IsLead($nLeadId)
    {
        $Result = $this->GetLeadById($nLeadId);
        return isset($Result);
    }

    public function GetList($RestMethod, $arrFilter = null, $arrSelectedFields = null, $arrOrder = null, $bReturnAllFields = true)
    {
        $arrParams = array();
        if (isset($arrFilter))
            $arrParams['FILTER'] = $arrFilter;

        if (isset($arrSelectedFields))
            $arrParams['SELECT'] = $arrSelectedFields;
        else if ($bReturnAllFields)
            $arrParams['SELECT'] = $this->GetFieldsNamesForRestMethod($RestMethod);

        if (isset($arrOrder))
            $arrParams['ORDER'] = $arrOrder;

        return $this->RestCommand($RestMethod, $arrParams);
    }

    /**
     * @param null $Phone
     * @param null $EMail
     * @return array|bool|mixed
     */
    public function GetLeadsByParams($Phone=null, $EMail = null)
    {
        return $this->GetRecordsByPhoneOrEmail('crm.lead.list', $Phone, $EMail);
/*
        if (isset($Phone)) $Phone = CUtils::NormalizePhoneNumber($Phone);

        $arrList1 = array();
        $arrList2 = array();

        if ($Phone!="") $arrList1 = $this->GetList('crm.lead.list', array('PHONE' => $Phone), null, array("DATE_CREATE"=>"DESC"));
        if ($EMail!="") $arrList2 = $this->GetList('crm.lead.list', array('EMAIL' => $EMail), null, array("DATE_CREATE"=>"DESC"));

        $Result = false;

        if (count($arrList1)>0)
            $Result = $arrList1;
        if (count($arrList2)>0)
            $Result = $arrList2;

        return $Result;
*/
    }

    public function GetContactsByParams($Phone=null, $EMail = null)
    {
        return $this->GetRecordsByPhoneOrEmail('crm.contact.list', $Phone, $EMail);
    }

    public function GetCompaniesByParams($Phone=null, $EMail = null)
    {
        return $this->GetRecordsByPhoneOrEmail('crm.company.list', $Phone, $EMail);
    }

    /**
     * @param $sRestFunction
     * @param null $Phone
     * @param null $EMail
     * @return array|bool|mixed
     */
    public function GetRecordsByPhoneOrEmail($sRestFunction, $Phone=null, $EMail = null)
    {
        $Result = false;

        if (isset($Phone)) $Phone = CUtils::NormalizePhoneNumber($Phone);

        $arrList1 = array();
        $arrList2 = array();

        if (isset($Phone)&&($Phone!="")) {
            $arrList1 = $this->GetList($sRestFunction, array('PHONE' => $Phone), null, array("DATE_CREATE" => "DESC"));
            if (count($arrList1)>=50)
                $arrList1 = array();
        }
        if (count($arrList1)==0) {
            if (isset($EMail) && ($EMail != ""))
                $arrList2 = $this->GetList($sRestFunction, array('EMAIL' => $EMail), null, array("DATE_CREATE" => "DESC"));

            if (count($arrList2)>=50)
                $arrList2 = array();
        }

        if (count($arrList1)>0)
            $Result = $arrList1;
        if (count($arrList2)>0)
            $Result = $arrList2;

        return $Result;
    }

    public function GetDealsForContactIdOrCompanyId($nContactId=null, $nCompanyId = null)
    {
        $arrList1 = array();
        $arrList2 = array();

        if ($nContactId!="") $arrList1 = $this->GetList('crm.deal.list', array('CONTACT_ID' => $nContactId), null, array("DATE_CREATE"=>"DESC"));
        if ($nCompanyId!="") $arrList2 = $this->GetList('crm.deal.list', array('COMPANY_ID' => $nCompanyId), null, array("DATE_CREATE"=>"DESC"));

        $Result = false;

        if (count($arrList1)>0)
            $Result = $arrList1;
        if (count($arrList2)>0)
            $Result = $arrList2;

        return $Result;
    }

    public function CanCreateNewLead($Phone=null, $EMail = null, &$pLastNotClosedLead = null)
    {
        $bCreateNewLead = true;
        $arrLeads = $this->GetLeadsByParams($Phone, $EMail);

        if ($arrLeads!==false)
        {
            if (($arrLeads[0]['STATUS_ID']!='CONVERTED')&&($arrLeads[0]['STATUS_ID']!='JUNK'))
            {
                $dtLeadCreatedate = new DateTime($arrLeads[0]['DATE_CREATE']);
                $dtNow = new DateTime();
                $dtInterval = $dtNow->diff($dtLeadCreatedate);

                // если человек уже отправлял заявку сегодня, то просто обновляем текущую
                if ($dtInterval->d == 0)
                    $bCreateNewLead = false;

                $pLastNotClosedLead = $arrLeads[0];
            }
        }

        return $bCreateNewLead;
    }

    /**
     * @param null $Phone
     * @param null $EMail
     * @param null $arrLeads
     * @return bool
     */
    public function IsLeadEnabled($Phone=null, $EMail = null, &$arrLeads = null)
    {
        $arrLeads = $this->GetLeadsByParams($Phone, $EMail);
        return ($arrLeads!==false)?true:false;
    }

    /**
     * @param $Name
     * @param null $Phone
     * @param null $EMail
     * @param null $Address
     * @param null $Comments
     * @param null $arrAdditionalParams
     * @return mixed
     */
    public function AddLeadFromOrderForm(
        $Name,
        $Phone = null,
        $EMail = null,
        $Address = null,
        $Comments = null,
        $arrAdditionalParams = null,
        $sStatusId = null,
        $sLeadPrefix = null)
    {
        if (!isset($Name)) $Name = "Заявка";

        $Phone = CUtils::NormalizePhoneNumber($Phone);

        $arrData = array();
        if (isset($Phone)) $arrData['fields']['PHONE'] = array(array("VALUE" => $Phone, "VALUE_TYPE" => "WORK"));
        if (isset($EMail)) $arrData['fields']['EMAIL'] = array(array("VALUE" => $EMail, "VALUE_TYPE" => "WORK"));
        if (isset($Address)) $arrData['fields']['ADDRESS'] = $Address;

        $arrData['fields']['COMMENTS'] = '';




        // возможно этот клиент уже есть в базе
        $nContactId = 0;
        if (isset($Phone)||isset($EMail))
        {
            $nContactId = $this->GetContactIdByContactData($Phone, $EMail);

            if (isset(CCMS::$SETS[SETTINGS_SECTION_BITRIX24][SETTINGS_BITRIX24_LEAD_FIELD_CRM_CONTACT]))
                $arrData['fields'][CCMS::$SETS[SETTINGS_SECTION_BITRIX24][SETTINGS_BITRIX24_LEAD_FIELD_CRM_CONTACT]] = $nContactId;
        }

        // определяем регион по номеру телефона
        $nTimeZone = 0;
        $arrRegionInfo = CBaseAddonClass::GetTagValue('PL_RGNCHK_GET_INFO_FROM_HTML_WEB_RU', [$Phone]);

        $sRegionByPhone = '';
        $sRegionByYandex = '';

        if (isset($arrRegionInfo)&&$arrRegionInfo!==false)
        {
            $sRegionByPhone = 'Местоположение по телефону: ';
            if (isset($arrRegionInfo['country']['name'])) {
                $arrData['fields']['ADDRESS_COUNTRY'] = $arrRegionInfo['country']['name'];
                $sRegionByPhone .= $arrRegionInfo['country']['name'].',';
            }
            if (isset($arrRegionInfo['region']['name'])) {
                $arrData['fields']['ADDRESS_REGION'] = $arrRegionInfo['region']['name'];
                $sRegionByPhone .= $arrRegionInfo['region']['name'].',';
            }
            if (isset($arrRegionInfo[0]['name'])) {
                $arrData['fields']['ADDRESS_CITY'] = $arrRegionInfo[0]['name'];
                $sRegionByPhone .= $arrRegionInfo[0]['name'].',';
            }
            if (isset($arrRegionInfo['time_zone'])) {
                $nTimeZone = $arrRegionInfo['time_zone']-3;
                $sRegionByPhone .= ' Часовой пояс: '.$nTimeZone;
            }
        }
        else if (isset($_SESSION['YAGEO']))
        {
            if (isset($_SESSION['YAGEO']['COUNTRY'])) {
                $arrData['fields']['ADDRESS_COUNTRY'] = $_SESSION['YAGEO']['COUNTRY'];
            }
            if (isset($_SESSION['YAGEO']['REGION'])) {
                $arrData['fields']['ADDRESS_REGION'] = $_SESSION['YAGEO']['REGION'];
            }
            if (isset($_SESSION['YAGEO']['CITY'])) {
                $arrData['fields']['ADDRESS_CITY'] = $_SESSION['YAGEO']['CITY'];
            }
            if (isset($_SESSION['YAGEO']['TIMEZONE'])) {
                $nTimeZone = $_SESSION['YAGEO']['TIMEZONE'];
            }
        }

        if (isset($_SESSION['YAGEO']))
        {
            $sRegionByYandex = 'Местоположение от Яндекс: ';
            if (isset($_SESSION['YAGEO']['COUNTRY'])) {
                $sRegionByYandex .= $_SESSION['YAGEO']['COUNTRY'].',';
            }
            if (isset($_SESSION['YAGEO']['REGION'])) {
                $sRegionByYandex .= $_SESSION['YAGEO']['REGION'].',';
            }
            if (isset($_SESSION['YAGEO']['CITY'])) {
                $sRegionByYandex .= $_SESSION['YAGEO']['CITY'].',';
            }
            if (isset($_SESSION['YAGEO']['TIMEZONE'])) {
                $sRegionByYandex .= ' Часовой пояс: '.$_SESSION['YAGEO']['TIMEZONE'];
            }
        }

        $sLeadDate = date("Y-m-d H:i ");
        // указываем часовой пояс в названии лида
        if (isset($_SESSION['YAGEO']['TIMEZONE'])) {
            $nTimeZone = $_SESSION['YAGEO']['TIMEZONE']; // наиболее правильный вариант
        }
        if ($nTimeZone!=0)
            $sLeadDate .= ' '.(($nTimeZone>0)?'+':'-').$nTimeZone.' ';

        if (isset($sLeadPrefix))
            $arrData['fields']['TITLE'] = $sLeadDate.$sLeadPrefix." ".$Name;
        else if (isset(CCMS::$SETS[SETTINGS_SECTION_BITRIX24][SETTINGS_BITRIX24_LEAD_PREFIX]))
            $arrData['fields']['TITLE'] = $sLeadDate.CCMS::$SETS[SETTINGS_SECTION_BITRIX24][SETTINGS_BITRIX24_LEAD_PREFIX]." ".$Name;
        else
            $arrData['fields']['TITLE'] = $sLeadDate.$Name;

        if ($nContactId>0)
            $arrData['fields']['TITLE'] .= ' (повторное обращение)';

        $arrData['fields']['SOURCE_ID'] = isset($sStatusId)?$sStatusId:CCMS::$SETS[SETTINGS_SECTION_BITRIX24][SETTINGS_BITRIX24_SOURCE_ID];
        $arrData['fields']['ASSIGNED_BY_ID'] = CCMS::$SETS[SETTINGS_SECTION_BITRIX24][SETTINGS_BITRIX24_ASSIGNED_BY_ID];
        $arrData['fields']['SOURCE_DESCRIPTION'] = CCMS::$SETS[SETTINGS_SECTION_BITRIX24][SETTINGS_BITRIX24_SOURCE_DESCRIPTION];

        $arrData['fields']['COMMENTS'] .= $sRegionByPhone.'<br>'.$sRegionByYandex.'<br>';
        if (isset($Comments)) $arrData['fields']['COMMENTS'] .= date('Y-m-d H:i:s').'<br>'.$Comments;

        if (isset($arrAdditionalParams))
            $arrData = array_merge($arrData['fields'], $arrAdditionalParams);

        // проверяем наличие лида в базе

        $pLastLead = null;
        $bCreateNewLead = $this->CanCreateNewLead($Phone, $EMail, $pLastLead);

        if ($bCreateNewLead===true)
        {
            /*
            if (isset($_SESSION['COOKIES'][COOKIE_PARAM_FIRST_SOURCE_SITE]))
            {

            }
            */
            $nTrafficSource = 231;
            // определяем первый источник трафика
            if (isset($_SESSION['COOKIES'][COOKIE_PARAM_FIRST_UTM_SOURCE]))
                $nTrafficSource = $this->GetLeadSourceTypeIdByString(
                    $_SESSION['COOKIES'][COOKIE_PARAM_FIRST_UTM_SOURCE],
                    isset($_SESSION['COOKIES'][COOKIE_PARAM_FIRST_HTTP_REFERER])?$_SESSION['COOKIES'][COOKIE_PARAM_FIRST_HTTP_REFERER]:'',
                    true);

            if (isset(CCMS::$SETS[SETTINGS_SECTION_BITRIX24][SETTINGS_BITRIX24_LEAD_FIELD_TRAFFIC_SOURCE_FIRST]))
                $arrData['fields'][CCMS::$SETS[SETTINGS_SECTION_BITRIX24][SETTINGS_BITRIX24_LEAD_FIELD_TRAFFIC_SOURCE_FIRST]] =
                    $nTrafficSource;
            if (isset(CCMS::$SETS[SETTINGS_SECTION_BITRIX24][SETTINGS_BITRIX24_LEAD_FIELD_TRAFFIC_HREF_FIRST]))
                $arrData['fields'][CCMS::$SETS[SETTINGS_SECTION_BITRIX24][SETTINGS_BITRIX24_LEAD_FIELD_TRAFFIC_HREF_FIRST]] =
                    $_SESSION['COOKIES'][COOKIE_PARAM_FIRST_HTTP_REFERER];

            // Для первого лида от клиента сохраняем UTM-метки
            if (isset($_SESSION['COOKIES'][COOKIE_PARAM_FIRST_UTM_SOURCE]))
                $arrData['fields']['UTM_SOURCE']    = $_SESSION['COOKIES'][COOKIE_PARAM_FIRST_UTM_SOURCE];
            if (isset($_SESSION['COOKIES'][COOKIE_PARAM_FIRST_UTM_MEDIUM]))
                $arrData['fields']['UTM_MEDIUM']    = $_SESSION['COOKIES'][COOKIE_PARAM_FIRST_UTM_MEDIUM];
            if (isset($_SESSION['COOKIES'][COOKIE_PARAM_FIRST_UTM_CAMPAIGN]))
                $arrData['fields']['UTM_CAMPAIGN']  = $_SESSION['COOKIES'][COOKIE_PARAM_FIRST_UTM_CAMPAIGN];
            if (isset($_SESSION['COOKIES'][COOKIE_PARAM_FIRST_UTM_CONTENT]))
                $arrData['fields']['UTM_CONTENT']   = $_SESSION['COOKIES'][COOKIE_PARAM_FIRST_UTM_CONTENT];
            /// в битриксе нет других полей, поэтому добавляем в это
            if (isset($_SESSION['COOKIES'][COOKIE_PARAM_FIRST_UTM_TERM]))
                $arrData['fields']['UTM_CONTENT']   = $_SESSION['COOKIES'][COOKIE_PARAM_FIRST_UTM_TERM].'|'.$arrData['fields']['UTM_CONTENT'];

            if (isset($_SESSION['COOKIES'][COOKIE_PARAM_SOURCE_SITE]))
            {
                // определяем первый источник трафика

                $nTrafficSource = $this->GetLeadSourceTypeIdByString($_SESSION['COOKIES'][COOKIE_PARAM_UTM_SOURCE], $_SESSION['COOKIES'][COOKIE_PARAM_HTTP_REFERER], false);

                if (isset(CCMS::$SETS[SETTINGS_SECTION_BITRIX24][SETTINGS_BITRIX24_LEAD_FIELD_TRAFFIC_SOURCE]))
                    $arrData['fields'][CCMS::$SETS[SETTINGS_SECTION_BITRIX24][SETTINGS_BITRIX24_LEAD_FIELD_TRAFFIC_SOURCE]] = $nTrafficSource;
                if (isset(CCMS::$SETS[SETTINGS_SECTION_BITRIX24][SETTINGS_BITRIX24_LEAD_FIELD_TRAFFIC_HREF]))
                    $arrData['fields'][CCMS::$SETS[SETTINGS_SECTION_BITRIX24][SETTINGS_BITRIX24_LEAD_FIELD_TRAFFIC_HREF]] = $_SESSION['COOKIES'][COOKIE_PARAM_HTTP_REFERER];
                /*
                // Для первого лида от клиента сохраняем UTM-метки
                if (isset($_SESSION['COOKIES'][COOKIE_PARAM_FIRST_UTM_SOURCE]))
                    $arrData['fields']['UTM_SOURCE']    = $_SESSION['COOKIES'][COOKIE_PARAM_FIRST_UTM_SOURCE];
                if (isset($_SESSION['COOKIES'][COOKIE_PARAM_FIRST_UTM_MEDIUM]))
                    $arrData['fields']['UTM_MEDIUM']    = $_SESSION['COOKIES'][COOKIE_PARAM_FIRST_UTM_MEDIUM];
                if (isset($_SESSION['COOKIES'][COOKIE_PARAM_FIRST_UTM_CAMPAIGN]))
                    $arrData['fields']['UTM_CAMPAIGN']  = $_SESSION['COOKIES'][COOKIE_PARAM_FIRST_UTM_CAMPAIGN];
                if (isset($_SESSION['COOKIES'][COOKIE_PARAM_FIRST_UTM_CONTENT]))
                    $arrData['fields']['UTM_CONTENT']   = $_SESSION['COOKIES'][COOKIE_PARAM_FIRST_UTM_CONTENT];
                if (isset($_SESSION['COOKIES'][COOKIE_PARAM_FIRST_UTM_TERM]))
                    $arrData['fields']['UTM_TERM']      = $_SESSION['COOKIES'][COOKIE_PARAM_FIRST_UTM_TERM];
                */
            }

            // если $pLastLead не null, значит этот человек вчера или ранее оставлял заявку, поэтому помечаем лид как "Дублированная заявка"
            if  (isset($pLastLead))
            {
                $arrData['fields']['TITLE'] .= ' (дублированная заявка)';
                // сразу заполняем причину отказа и дополнительную информацию по причине отказа.
                $arrData['fields'][CBitrix24Config::FIELD_NAME_LEAD_CAUSE_OF_FAILURE] = CBitrix24Config::LEAD_CAUSE_OF_FAILURE_DUPLICATE_LEAD;
                $arrData['fields'][CBitrix24Config::FIELD_NAME_LEAD_CAUSE_OF_FAILURE_DESC] =
                    sprintf('https://%s/crm/lead/show/%d/',
                        CCMS::$SETS[SETTINGS_SECTION_BITRIX24][SETTINGS_BITRIX24_CRM_HOST],
                        $pLastLead['ID']);
            }

            $nLeadId = $this->RestCommand('crm.lead.add', $arrData);
            if (isset($nLeadId))
            {
                $sNotifyTemplate =
                    '[B]Появился новый лид!!![/B] [URL=/crm/lead/show/%d/]%s[/URL]';
                $sNotifyTemplateWithContact =
                    '[B]Появился новый лид!!![/B] [URL=/crm/lead/show/%d/]%s[/URL][BR]
                        Контакт: [URL=/crm/contact/show/%d/]%s %s %s[/URL] создан %s[BR]';
                if ($nContactId==0)
                {
                    $this->RestCommand(
                        'im.notify',
                        [
                            //'to' => 11,
                            'to' => CCMS::$SETS[SETTINGS_SECTION_BITRIX24][SETTINGS_BITRIX24_ASSIGNED_BY_ID],
                            'message' => sprintf($sNotifyTemplate, $nLeadId, $arrData['fields']['TITLE']),
                            'type' => 'SYSTEM'
                        ]
                    );
                }
                else
                {
                    $pContact = $this->RestCommand('crm.contact.get', [ 'id' => $nContactId ]);
                    if (isset($pContact)) {

                        $d1 = strtotime($pContact['DATE_CREATE']); // переводит из строки в дату
                        $sContactCreateDate = date("d-m-Y", $d1); // переводит в новый формат

                        $this->RestCommand(
                            'im.notify',
                            //['to' => 11,
                            ['to' => CCMS::$SETS[SETTINGS_SECTION_BITRIX24][SETTINGS_BITRIX24_ASSIGNED_BY_ID],
                                'message' => sprintf(
                                    $sNotifyTemplateWithContact,
                                    $nLeadId,
                                    $arrData['fields']['TITLE'],
                                    $nContactId, $pContact['LAST_NAME'], $pContact['NAME'], $pContact['SECOND_NAME'], $sContactCreateDate),
                                'type' => 'SYSTEM'
                            ]
                        );
                    }
                }

                $this->AddProductsToLeadFromShoppingCart($nLeadId);

                return $nLeadId;
            } else
                return 0;
        }
        else
        {
            $oCurrentLead = $pLastLead;
            // Lead already created. We nned to update it
            $arrData['id'] = $oCurrentLead['ID'];
            $arrData['fields']['TITLE'] = $oCurrentLead['TITLE'];

            // определяем текущий источник трафика
            $nTrafficSource = $this->GetLeadSourceTypeIdByString($_SESSION['COOKIES'][COOKIE_PARAM_UTM_SOURCE], $_SESSION['COOKIES'][COOKIE_PARAM_HTTP_REFERER], false);

            if (isset(CCMS::$SETS[SETTINGS_SECTION_BITRIX24][SETTINGS_BITRIX24_LEAD_FIELD_TRAFFIC_SOURCE]))
                $arrData['fields'][CCMS::$SETS[SETTINGS_SECTION_BITRIX24][SETTINGS_BITRIX24_LEAD_FIELD_TRAFFIC_SOURCE]] = $nTrafficSource;
            if (isset(CCMS::$SETS[SETTINGS_SECTION_BITRIX24][SETTINGS_BITRIX24_LEAD_FIELD_TRAFFIC_HREF]))
                $arrData['fields'][CCMS::$SETS[SETTINGS_SECTION_BITRIX24][SETTINGS_BITRIX24_LEAD_FIELD_TRAFFIC_HREF]] = $_SESSION['COOKIES'][COOKIE_PARAM_FIRST_HTTP_REFERER];

            $oCRMObj = $this->RestCommand('crm.lead.get', array('id' => $oCurrentLead['ID']));
            if ($oCurrentLead['HAS_PHONE'])
                $arrData['fields']['PHONE'][0]['ID'] = $oCRMObj['PHONE'][0]['ID'];

            if ($oCurrentLead['HAS_EMAIL'])
                $arrData['fields']['EMAIL'][0]['ID'] = $oCRMObj['EMAIL'][0]['ID'];

            // старые комментарии не будет затирать
            if (isset($Comments))
                $arrData['fields']['COMMENTS'] = $oCurrentLead['COMMENTS'].'<br><br>'.$arrData['fields']['COMMENTS'];

            $arrData[] =

            $Result = $this->RestCommand('crm.lead.update', $arrData);

            $sNotifyTemplate =
                '[B]Повторное обращение по лиду[/B] [URL=/crm/lead/show/%d/]%s[/URL][BR]Дата обращения: %s';

            $this->RestCommand(
                'im.notify',
                [
                    'to' => CCMS::$SETS[SETTINGS_SECTION_BITRIX24][SETTINGS_BITRIX24_ASSIGNED_BY_ID],
                    'message' => sprintf($sNotifyTemplate, $oCurrentLead['ID'], $arrData['fields']['TITLE'], date("Y-m-d H:i")),
                    'type' => 'SYSTEM'
                ]
            );

            // Удаляем продукты, которых нет в корзине. Для этого достаточно выставить QUANTITY = 0
            $arrCurrentProducts = $this->RestCommand('crm.lead.productrows.get', array('id'=> $arrData['id']));
            if (count($arrCurrentProducts)>0)
            {
                $arrProducts = array();

                foreach ($arrCurrentProducts as $arrProd)
                {
                    if (!CShopingCart::IsProductInCart($arrProd['ID']))
                    {
                        // Продукт есть в лиде, но нет в текущей корзине, значит его количество нужно выставить в 0
                        $arrProd['QUANTITY'] = 0;
                        $arrProducts[] = $arrProd;
                    }
                }

                if (count($arrProducts)>0)
                {
                    $this->AddProductsToCrmItem(
                        'crm.lead.productrows.set',
                        $arrData['id'],
                        $arrProducts);
                }
            }

            // обновление товарных позиций
            $this->AddProductsToLeadFromShoppingCart($arrData['id']);

            return $arrData['id'];
        }
    }

    public function AddProductsToCrmItemFromShoppingCart(
        $RestMethod,
        $nParentId,
        $nStartIndex = 0)
    {
        $sParams = array();
        $sParams['id'] = $nParentId;

        if (CShopingCart::GetAllProductCount()>0)
        {
            $arrProducts = CShopingCart::GetAllProducts();
            $sParams['rows'] = array();
            foreach ($arrProducts as $oProduct)
            {
                // возможно имеем дело с товарами у которого есть свойства, поэтому идентификатор товара может быть другой
                $nProductId = CShopingCart::GetProductIdForLead($oProduct['sId']);
                $pDBProduct = CCMS::$DB->FindProductByID($nProductId);

                $fPriceWithDiscount = $pDBProduct['PRICE']*(1-$oProduct['nDiscount']/100);

                $sParams['rows'][] = array(
                    'PRODUCT_ID' => CShopingCart::GetProductIdForLead($oProduct['sId']),
                    'PRODUCT_NAME' => $pDBProduct['NAME'].CShopingCart::GetProductsPropertiesAsText($oProduct['sId']),
                    'PRICE_BRUTTO' => $pDBProduct['PRICE'],
                    'PRICE_NETTO' => $pDBProduct['PRICE'],
                    'PRICE' => $fPriceWithDiscount,
                    'PRICE_EXCLUSIVE' => $pDBProduct['PRICE']*(1-$oProduct['nDiscount']/100),
                    'DISCOUNT_TYPE_ID' => 2,
                    'DISCOUNT_RATE' => $oProduct['nDiscount'],
                    'DISCOUNT_SUM' => $pDBProduct['PRICE'] - $fPriceWithDiscount,
                    'PRICE_ACCOUNT' => $fPriceWithDiscount,
                    'QUANTITY' => $oProduct['sCount']
                );
            }

            if (CShopingCart::GetAdditionalCommonDiscount()>0) {
                $sParams['rows'][] = array(
                    'PRODUCT_ID' => 0,
                    'PRODUCT_NAME' => 'Дополнительная скидка',
                    'PRICE' => -1*CShopingCart::GetAdditionalCommonDiscount(),
                    'PRICE_ACCOUNT' => -1*CShopingCart::GetAdditionalCommonDiscount(),
                    'QUANTITY' => 1);
            }

            return $this->RestCommand($RestMethod, $sParams);
        }
        return false;
    }

    public function AddProductsToCrmItem(
        $RestMethod,
        $nParentId,
        $arrProducts)
    {
        $sParams = array();
        $sParams['id'] = $nParentId;

        if (count($arrProducts)>0)
        {
            $sParams['rows'] = $arrProducts;
            return $this->RestCommand($RestMethod, $sParams);
        }
        return false;
    }

    public function AddProductsToLeadFromShoppingCart($nLeadId)
    {
        return $this->AddProductsToCrmItemFromShoppingCart('crm.lead.productrows.set', $nLeadId);
    }

    /**
     * @param $nDealType
     * @param $nOwnerType
     * @param $nOwnerId
     * @param $arrParams
     * @return mixed
     */
    public function AddDealToCRMObject(
        $nDealType, $nOwnerType, $nOwnerId, $arrParams)
    {
        $arrData = array();
        $arrData['fields'] = $arrParams;
        $arrData['fields']['TYPE_ID']=$nDealType;
        $arrData['fields']['OWNER_TYPE_ID']=$nOwnerType;
        $arrData['fields']['OWNER_ID']=$nOwnerId;
        $arrData['fields']['RESPONSIBLE_ID'] = CCMS::$SETS[SETTINGS_SECTION_BITRIX24][SETTINGS_BITRIX24_ASSIGNED_BY_ID];
        
        return $this->RestCommand('crm.activity.add', $arrData);
    }

    public function FindRecordIdByFilter($sRESTCommand, $arrParams)
    {
        array_unshift($arrParams, "select[0]=ID");

        $sResult = $this->RestCommand($sRESTCommand, $arrParams);

        if (isset($sResult)) return $sResult[0]['ID'];
        else return 0;
    }

    public function CreateCallNotify($sPhoneNumber, $objLead = null)
    {
        $nAssignedById = CCMS::$SETS[SETTINGS_SECTION_BITRIX24][SETTINGS_BITRIX24_ASSIGNED_BY_ID];

        $sNotifyLead =
            '[B]Входящий звонок по лиду: [/B] [URL=/crm/lead/show/%d/]%s[/URL]
            [BR]%s';

        $sNotifyDeal =
            '[B]Входящий звонок по сделке: [/B] [URL=/crm/deal/show/%d/]%s[/URL][BR]
            Контакт: [URL=/crm/contact/show/%d/]%s %s %s[/URL]
            [BR]%s';

        $sNotifyContact =
            '[B]Входящий звонок от:[/B] [URL=/crm/contact/show/%d/]%s %s %s[/URL][BR] создан %s.
            [BR]%s';

        $sNotifyUnknown = 'Входящий звонок с неизвестного номера.
            [BR]%s';

        if (!isset($objLead))
        {
            $arrLeads = $this->GetLeadsByParams($sPhoneNumber);
            if (($arrLeads!==FALSE))
            {
                $objLead = $arrLeads[0];
            }
        }

        // определяем регион по номеру телефона
        $sRegion = 'Неопределен';
        $sCity = 'Неопределен';
        $arrRegionInfo = CBaseAddonClass::GetTagValue('PL_RGNCHK_GET_INFO_FROM_HTML_WEB_RU', [$sPhoneNumber]);
        if ($arrRegionInfo!==false)
        {
            if (isset($arrRegionInfo['region']['name'])) $sRegion = $arrRegionInfo['region']['name'];
            if (isset($arrRegionInfo[0]['name'])) $sCity = $arrRegionInfo[0]['name'];
        }

        $sRegionInnfo = sprintf('Регион: %s. Город: %s', $sRegion, $sCity);

        if (isset($objLead)) {
            // смотрим, есть ли контакт

            $objLead = $this->GetLeadById($objLead['ID']);
            $objContact = $this->GetContactByContactData(
                isset($objLead['PHONE'][0])?$objLead['PHONE'][0]['VALUE']:null,
                isset($objLead['EMAIL'][0])?$objLead['EMAIL'][0]['VALUE']:null);

            if (!isset($objContact))
            {
                $this->RestCommand(
                    'im.notify',
                    [
                        'to' => $nAssignedById,
                        'message' => sprintf($sNotifyLead, $objLead['ID'], $objLead['TITLE'], $sRegionInnfo),
                        'type' => 'SYSTEM'
                    ]
                );
            }
            else
            {
                $d1 = strtotime($objContact['DATE_CREATE']); // переводит из строки в дату
                $sContactCreateDate = date("d-m-Y", $d1); // переводит в новый формат

                $this->RestCommand(
                    'im.notify',
                    [
                        'to' => $nAssignedById,
                        'message' => sprintf(
                            $sNotifyContact,
                            $objContact['ID'],
                            $objContact['LAST_NAME'],
                            $objContact['NAME'],
                            $objContact['SECOND_NAME'],
                            $sContactCreateDate,
                            $sRegionInnfo),
                        'type' => 'SYSTEM'
                    ]
                );

                /// для теста
                $this->RestCommand(
                    'im.notify',
                    [
                        'to' => 11,
                        'message' => sprintf(
                            $sNotifyContact,
                            $objContact['ID'],
                            $objContact['LAST_NAME'],
                            $objContact['NAME'],
                            $objContact['SECOND_NAME'],
                            $sContactCreateDate,
                            $sRegionInnfo),
                        'type' => 'SYSTEM'
                    ]
                );
            }
        }
        else
            $this->RestCommand(
                'im.notify',
                [
                    'to' => $nAssignedById,
                    'message' => sprintf($sNotifyUnknown, $sRegionInnfo),
                    'type' => 'SYSTEM'
                ]
            );
    }

    public function CreateNewTask(
        $sTitle,
        $sDescription,
        $nAssignedToId,
        $sCRMRecordType = null,
        $nCRMRecordId = 0,
        $arrObserverIds = null,
        $dtDeadline = null,
        $dtStartTime = null,
        $dtEndTime = null,
        $dtTimeEstimate = null,
        $sTag = "",
        $bAllowTimeTraking = false,
        $bIsRunning = false)
    {
        $arrParams = array(
            "TITLE" => $sTitle,
            "DESCRIPTION" => $sDescription,
            "RESPONSIBLE_ID" => $nAssignedToId,
            "DEADLINE" => $dtDeadline,
            "ALLOW_TIME_TRACKING" => $bAllowTimeTraking?"Y":"N",
            "PRIORITY" => 2
        );

        if (isset($arrObserverIds))
            $arrParams['AUDITORS'] = $arrObserverIds;

        if (isset($dtStartTime))
            $arrParams['START_DATE_PLAN'] = $dtStartTime;
         if (isset($dtEndTime))
             $arrParams['END_DATE_PLAN'] = $dtEndTime;
        if (isset($dtTimeEstimate))
            $arrParams['TIME_ESTIMATE'] = $dtTimeEstimate;

        $Result = $this->RestCommand('task.item.add', $arrParams);

        if ($bIsRunning & ($Result > 0))
            $this->RestCommand('task.item.startexecution', array($Result));

        return isset($Result)?$Result:0;
    }

    public function GetContactByContactData($sPhone, $sEmail)
    {
        $objContact = null;

        $Result = $this->GetRecordsByPhoneOrEmail("crm.contact.list", $sPhone, $sEmail);
        if ($Result!==FALSE)
        {
            $objContact = $Result[0];
        }
        else {
            //Проверим на разновидность (+7) и (8)
            $tempPhone = "";
            if ($sPhone[0] == '7') $tempPhone = "8".substr($sPhone,1);
            else if ($sPhone[0] == '8') $tempPhone = "7".substr($sPhone,1);

            $Result = $this->GetRecordsByPhoneOrEmail("crm.contact.list", $sPhone, $sEmail);
            if ($Result!==FALSE)
            {
                $objContact = $Result[0];
            }
        }

        return $objContact;
    }

    public function GetContactIdByContactData($sPhone, $sEmail)
    {
        $objContact = $this->GetContactByContactData($sPhone, $sEmail);
        return isset($objContact)?$objContact['ID']:0;
    }

    public function GetLeadSourceTypeIdByString(
        $sSourceType,
        $sSourceDescription = "",
        $bIsFirstSourceType)
    {
        $nResult = 0;

        if ($bIsFirstSourceType)
            $arrSourceTypes = array(
                "yandex.direct.rsya" => 237,
                "yandex.direct" => 207,
                "google.adwords" => 209,
                "google.adwords.kms" => 239,
                "targetvk" => 311,
                "targetok" => 313,
            );
        else
            $arrSourceTypes = array(
                "yandex.direct.rsya" => 1217,
                "yandex.direct" => 1213,
                "google.adwords" => 1215,
                "google.adwords.kms" => 1219,
                "targetvk" => 1221,
                "targetok" => 1243,
            );

        if (isset($arrSourceTypes[$sSourceType]))
            $nResult = $arrSourceTypes[$sSourceType];
        else if (strpos($sSourceType,'email_')!==false)
            $nResult = ($bIsFirstSourceType)?241:1241; // Переход с рассылки
        else
        {
            // источник в чистом виде не задан анализируем ссылку
            if (isset($sSourceDescription) && ($sSourceDescription != ""))
            {
                $sSourceDescription = urlencode($sSourceDescription);

                if (strpos($sSourceDescription, "yandex")!==FALSE)
                    $nResult = ($bIsFirstSourceType)?225:1223;
                else if (strpos($sSourceDescription, "google")!==FALSE)
                    $nResult = ($bIsFirstSourceType)?227:1225;
                else if (strpos($sSourceDescription, "youtube")!==FALSE)
                    $nResult = ($bIsFirstSourceType)?217:1235;
                else if (strpos($sSourceDescription, "go.mail.ru/search")!==FALSE)
                    $nResult = ($bIsFirstSourceType)?229:1227;
                else if (strpos($sSourceDescription, "e.mail.ru")!==FALSE)
                    $nResult = ($bIsFirstSourceType)?237:1217; // РСЯ
                else if (strpos($sSourceDescription, "my.mail.ru")!==FALSE)
                    $nResult = ($bIsFirstSourceType)?237:1217; // РСЯ
                else if ((strpos($sSourceDescription, "vk.com")!==FALSE) || (strpos($sSourceDescription, "vkmonline.ru")!==FALSE))
                    $nResult = ($bIsFirstSourceType)?213:1231;
                else if (strpos($sSourceDescription, "vk.com/away.php")!==FALSE)
                    $nResult = ($bIsFirstSourceType)?213:1231; // ? Таргетированная реклама в ВКонтакте
                else if (strpos($sSourceDescription, "//ok.ru")!==FALSE || strpos($sSourceDescription, "odnoklassniki.ru")!==FALSE)
                    $nResult = ($bIsFirstSourceType)?215:1233; // Одноклассники
                else
                    $nResult = ($bIsFirstSourceType)?231:1237;
            }
            else
                $nResult = ($bIsFirstSourceType)?231:1237;
        }

        return $nResult;
    }
}

?>