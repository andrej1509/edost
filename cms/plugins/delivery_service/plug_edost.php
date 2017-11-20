<?

include_once ($_SERVER['DOCUMENT_ROOT'].'/cms/code/settings.php');
include_once (CMS_FILE_LOCAL_PATH_PHP.'plugin_base_class.php');
require_once (CMS_FILE_LOCAL_PATH_PHP.'bitrix24.php');


class CEdost
{


    public static $ERROR_DESCRIPTION = array(
        '���������� ������������� ��������: PVZCODE' => '�� ������ ��� ������ ���� "������������� ������ ����������"',
        '��� ������ ���������� ����������� � ���� ����: RecCityCode=0' => '�� ������ ��� ������ ���� "������������� ������ ����������"',
        '���������� �������� ���������: SizeC=' => '�� ������ ��� ������ ���� "������� ��������, ��"',
    );

    private $dbConn = null;

    /** @var CBitrix24 $pBitrix24 */
    private $pBitrix24 = null;
    private $arrBitrixPackageDimensions = array();

    function __construct($dbConnection)
    {
        $this->pBitrix24 = new CBitrix24();
    }

    /**
     * ��� ���� ������ � ������ "  " ��������� ������ ������� � ������ ��������
     * ���������� true ��� ������ � ��������, ������� ������ ����
     * @return array
     */
    public function LoadLeads()
    {
        // ���� ������ � ������ ��� �������
        $arrLeadIDs = $this->pBitrix24->GetList(
            'crm.lead.list',['!STATUS_ID'=> CBitrix24Config::LEADS_INACTIVE_STATUS_ID]);

        function cmp($a, $b)
        {
            return strcmp($b["ID"], $a["ID"]);
        }

         usort($arrLeadIDs, "cmp");
        return $arrLeadIDs;
    }
    /**
     * ��� ���� ������ � ������ "  " ��������� ������ ������� � ������ ��������
     * ���������� true ��� ������ � ��������, ������� ������ ����
     * @return array
     */

    public function LoadItems($id)
    {
        // ���� ������ � ������ ��� �������
        $arrItems = $this->pBitrix24->RestCommand(
            'crm.lead.productrows.get',array('id'=> $id));


        return $arrItems;
    }

    public function LoadProps($id)
    {
        // ���� ������ � ������ ��� �������
        $arrItems = $this->pBitrix24->RestCommand(
            'crm.product.get',array('id'=> $id));


        return $arrItems;
    }


    private function UpdateBitrix24DealFromInfoRequest($arrInfoRequestResult)
    {
        if ($arrInfoRequestResult['@attributes']['ErrorCode'] != 'ERR_INVALID_NUMBER')
        {
            // ��������� ���� "����-���" � "��������� ��������"
            $arrDealData = array();
            $arrDealData['id'] = $arrInfoRequestResult['@attributes']['Number'];
            $arrDealData['fields'][CBitrix24Config::FIELD_NAME_TREK_NUMBER] = $arrInfoRequestResult['@attributes']['DispatchNumber'];

            // ������������ ��������� ��������
            $arrDealData['fields'][CBitrix24Config::FIELD_NAME_DELIVERY_PAYMENT] =
                $arrInfoRequestResult['@attributes']['DeliverySum'];

            // ���� ���������� ������, �� ���������� ��������� ��������������
            if ($arrInfoRequestResult['@attributes']['CashOnDeliv']>0)
            {
                $arrDealData['fields'][CBitrix24Config::FIELD_NAME_DELIVERY_PAYMENT] +=
                    $arrInfoRequestResult['@attributes']['CashOnDeliv']*self::CDEK_AGENCY_FEE/100;
            }

            // ��������� ���������
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
        // ������� �������, ���� �� ���
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

        // ��������� ������� �������
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