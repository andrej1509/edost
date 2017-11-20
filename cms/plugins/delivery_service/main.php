<?

include_once ($_SERVER['DOCUMENT_ROOT'].'/cms/code/settings.php');
include_once (CMS_FILE_LOCAL_PATH_PHP.'plugin_base_class.php');
require_once (CMS_FILE_LOCAL_PATH_PHP.'bitrix24.php');
require_once(PLUGINS_CODE_DIRECTORY . '/delivery_service/plug_edost.php');
require_once (PLUGINS_CODE_DIRECTORY.'/delivery_service/russianpost.php');
require_once (PLUGINS_CODE_DIRECTORY.'/delivery_service/cdek.php');


class CDeliveryService extends CBasePluginClass
{
    private $pRussianPost = null;
    private $pCDEK = null;
    private $pEdost = null;
    

    function __construct()
    {
        parent::__construct("delivery_service");

        $this->pRussianPost = new CRussianPost($this->dbConn);
        $this->pCDEK = new CCDEK($this->dbConn);
        $this->pEdost = new CEdost($this->dbConn);
    }

    public function GetTagValue($sTagName, $arrParameters = null)
    {
        $Result = "";


        return $Result;
    }

    /**
     * Создание сделок СДЭК и выдача результата
     *
     * @return string
     */
    public function OnButtonCreateCDEKOrders()
    {
        $sResult = '';

        $arrResult = $this->pCDEK->CreateOrders(true);

        if (isset($arrResult))
        {
            // Выводим общий результат, он в конце массива
            $sResult .= '<h2>Общий результат: '.$arrResult['']['Ошибки'][0].'</h2>';
            array_pop($arrResult);
            foreach ($arrResult as $sNumder => $arrErrors)
            {
                if (isset($arrErrors['Ошибки']))
                    foreach ($arrErrors['Ошибки'] as $sError)
                        if (isset($sError))
                        {
                            $sResult .= '<b>Заказ №'.$sNumder.'</b><br>';
                            if (isset(CCDEK::$ERROR_DESCRIPTION[$sError]))
                                $sError = CCDEK::$ERROR_DESCRIPTION[$sError];

                            $sResult .= '<span style="color:red;">Ошибка: '.$sError.'</span><br>';
                        }
                $sResult .= '<br>';
            }
        }

        return $sResult;
    }

    /**
     * Формирование сделок и выдача результата
     *
     * @return string
     */
    public function OnButtonLoadLeads()
    {
        $arrResult = '';

        $arrResult = $this->pEdost->LoadLeads();

        return $arrResult;
    }

    /**
     * Формирование товаров и выдача результата
     *
     * @return string
     */
    public function OnButtonLoadItems($id)
    {
        $arrResult = '';

        $arrResult = $this->pEdost->LoadItems($id);

        return $arrResult;
    }

    /**
     * Формирование свойств товаров и выдача результата
     *
     * @return string
     */
    public function OnButtonLoadProps($id)
    {
        $arrResult = '';

        $arrResult = $this->pEdost->LoadProps($id);

        return $arrResult;
    }



    /**
     * Создание сделок Почта России и выдача результата
     */
    public function OnButtonCreateRussianPortOrders()
    {
        return 'Функция в разработке';
    }

    /**
     * Выставление трек-кодов, стоимости отправки, даты отправки и т.д в сделках Битрикс24
     * @return string
     */
    public function OnButtonUpdateCDEKDealsInnBitrix24()
    {
        return 'Функция в разработке';
    }

    /**
     * Выставление трек-кодов, стоимости отправки, даты отправки и т.д в сделках Битрикс24
     * @return string
     */
    public function OnButtonUpdateRussianPortDealsInBitrix24()
    {
        $sResult = '';
        $dtBeginDate = time();
        $dtEndDate = time();
        $arrErrors = $this->pRussianPost->UpdateDealsInBitrix24(strtotime(date('Y-m-d 00:00:00'), $dtBeginDate),strtotime(date('Y-m-d 23:59:59'), $dtEndDate));
        if (isset($arrErrors)&&count($arrErrors)>0&&($arrErrors!==true))
        {
            foreach ($arrErrors as $sError)
            {
                $sResult .= '<span style="color:red;">Ошибка: '.$sError.'</span><br>';
            }
        }
        else
        {
            $sResult = '<h2>Все сделки "Почта России" успешно обновлены</h2>';
        }

        return $sResult;
    }

    /**
     * Расчет стоимости доставки службой Edost
     * @return string
     */
    public function OnButtonCalculateCostEdost()
    {
        $sResult = '';
        return $sResult;
    }

    /**
     * Выставление трек-кодов, стоимости отправки, даты отправки и т.д в сделках Битрикс24
     * @return string
     */
    public function OnButtonUpdateCDEKDealsInBitrix24()
    {
        $sResult = '';
        $dtBeginDate = time();
        $dtEndDate = time();
        $arrErrors = $this->pRussianPost->UpdateDealsInBitrix24(strtotime(date('Y-m-d 00:00:00'), $dtBeginDate),strtotime(date('Y-m-d 23:59:59'), $dtEndDate));
        if (isset($arrErrors)&&count($arrErrors)>0&&($arrErrors!==true))
        {
            foreach ($arrErrors as $sError)
            {
                $sResult .= '<span style="color:red;">Ошибка: '.$sError.'</span><br>';
            }
        }
        else
        {
            $sResult = '<h2>Все сделки "Почта России" успешно обновлены</h2>';
        }

        return $sResult;
    }
}
?>