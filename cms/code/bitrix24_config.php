<?

class CBitrix24Config
{
    // поля сделок
    const FIELD_NAME_DEAL_PAYMENT_TYPE = 'UF_CRM_1389641850';
    const FIELD_NAME_DEAL_PARTIAL_PAYMENT = 'UF_CRM_1445598393';
    const FIELD_NAME_DEAL_DELIVERY_TYPE = 'UF_CRM_1389641923';
    const FIELD_NAME_DEAL_COURIER_DELIVERY = 'UF_CRM_1394524457'; // доставка курьером или нет
    const FIELD_NAME_DEAL_PVZ_CODE = 'UF_CRM_1508354597'; /* Идентификатор точки самовывоза. Пока для СДЭК. */
    const FIELD_NAME_DEAL_PACKAGE_DIMENSIONS = 'UF_CRM_1508412357'; /* Размеры упаковки. Значение должно иметь формат Размер1xРазмер2xРазмер3/ Разделение латинской маленькой "x"*/
    const FIELD_NAME_DEAL_TREK_NUMBER = 'UF_CRM_1389944505'; //
    const FIELD_NAME_DEAL_DELIVERY_PAYMENT = 'UF_CRM_1392179147';
    const FIELD_NAME_DEAL_SHIP_DATE = 'UF_CRM_1392189590'; // Дата отгрузки
    const FIELD_NAME_DEAL_DELIVERY_DATE = 'UF_CRM_1400396977'; // Дата доставки
    const FIELD_NAME_DEAL_RECIEVE_DATE = 'UF_CRM_1393427563'; // Дата вручения клиенту
    const FIELD_NAME_DEAL_END_DATE = 'CLOSEDATE'; // Дата завершения сделки
    const FIELD_NAME_LEAD_PACKAGE_DIMENSIONS = 'UF_CRM_1511788770974'; /* Размеры упаковки. Значение должно иметь формат Размер1xРазмер2xРазмер3/ Разделение латинской маленькой "x"*/
    const FIELD_NAME_DEAL_PACKAGE_LOCATION = 'UF_CRM_1390938671'; // Местоположение посылки
    const FIELD_NAME_DEAL_RETURN_REASON_DESC = 'UF_CRM_1389789590'; // Дополнительно о причине возврата

    const FIELD_NAME_LEAD_CAUSE_OF_FAILURE = 'UF_CRM_1389641650'; // Причина отказа
    const FIELD_NAME_LEAD_CAUSE_OF_FAILURE_DESC = 'UF_CRM_1389643622'; // Дополнительно о причине отказа

    const FIELD_NAME_PRODUCT_DISCOUNT = 'PROPERTY_134';

    const DELIVERY_TYPE_RUSSIAN_POST = '79';
    const DELIVERY_TYPE_RUSSIAN_POST_EMS = '81';
    const DELIVERY_TYPE_CDEK = '99';

    const PAYMENT_TYPE_PAY_ON_DELIVERY = '75';

    const LEAD_CAUSE_OF_FAILURE_DUPLICATE_LEAD = 59; // Дублированная заявка
    const LEADS_INACTIVE_STATUS_ID = ['PROCESSED','JUNK'];


    const PAYMENT_TYPE_CASH_ON_DELIVERY = '75';

    // используемые статусы активных сделок от "Трек-код отправлен" до "Товар вручен. Деньги у посредника"
    //const DEALS_USING_ACTIVE_STAGE_ID = ['9'];
//    const DEALS_USING_ACTIVE_STAGE_ID = ['10','3','17','9','4','14'];
    // используемые статусы сделок от "Трек-код отправлен" до "Товар вручен. Деньги у посредника"
    //const DEALS_USING_RETURN_STAGE_ID = ['4','14'];
    // стутусы сделок
    const DEAL_STAGE_PACKAGE_DELIVERED = '3';
    const DEAL_STAGE_PACKAGE_RECIEVED = '9';
    const DEAL_STAGE_WON = 'WON';
    const DEAL_STAGE_RETURN_BEGIN = '4';
    const DEAL_STAGE_RETURN_IN_DELIVERY_SERVICE = '14';
}

?>