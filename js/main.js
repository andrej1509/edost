$(document).ready(function() {
var leads = document.getElementById('leads');
function OnClickOnLeads() {
  var destAdress = document.getElementById('address');
  var destCity = document.getElementById('edost_to_city');
  var currAdress = leads.options[leads.selectedIndex].getAttribute('address');
  var currCity = leads.options[leads.selectedIndex].getAttribute('city');
  var currDimens = leads.options[leads.selectedIndex].getAttribute('dimens');
  destAdress.value = currAdress;
  $("#dest_city").val(currCity);
  $("#edost_to_city").val(currCity);
    fillToReg();
    fillDimensionsToID(currDimens);
  var lead = leads.options[leads.selectedIndex].value;
  var path =   '../cms/plugins/delivery_service/property_items.php';
  SendRequest('post', path ,'idlead='+lead, r_handler);
  };
leads.addEventListener("change", OnClickOnLeads);
    $( "#edost_dimens" ).change(function() {
        changeDimens();
    });
});

function CreateRequest()
{
    var Request = false;

    if (window.XMLHttpRequest)
    {
        //Gecko-совместимые браузеры, Safari, Konqueror
        Request = new XMLHttpRequest();
    }
    else if (window.ActiveXObject)
    {
        //Internet explorer
        try
        {
             Request = new ActiveXObject("Microsoft.XMLHTTP");
        }    
        catch (CatchException)
        {
             Request = new ActiveXObject("Msxml2.XMLHTTP");
        }
    }
 
    if (!Request)
    {
        alert("Невозможно создать XMLHttpRequest");
    }
    
    return Request;
} 

/*
Функция посылки запроса к файлу на сервере
r_method  - тип запроса: GET или POST
r_path    - путь к файлу
r_args    - аргументы вида a=1&b=2&c=3...
r_handler - функция-обработчик ответа от сервера
*/
function SendRequest(r_method, r_path, r_args, r_handler)
{
    //Создаём запрос
    var Request = CreateRequest();
    
    //Проверяем существование запроса еще раз
    if (!Request)
    {
        return;
    }
    
    Request.onreadystatechange = function()
    {
    //Если обмен данными завершен
    if (Request.readyState == 4)
    {
        if (Request.status == 200)
        {
            r_handler(Request);
        }
        else
        {
            //Оповещаем пользователя о произошедшей ошибке
        }
    }
    else
    {
        //Оповещаем пользователя о загрузке
    }
 
    }
    
    //Проверяем, если требуется сделать GET-запрос
    if (r_method.toLowerCase() == "get" && r_args.length > 0)
    r_path += "?" + r_args;
    
    //Инициализируем соединение
    Request.open(r_method, r_path, true);
    
    if (r_method.toLowerCase() == "post")
    {
        //Если это POST-запрос
        
        //Устанавливаем заголовок
        Request.setRequestHeader("Content-Type","application/x-www-form-urlencoded; charset=utf-8");
        //Посылаем запрос
        Request.send(r_args);
    }
    else
    {
        //Если это GET-запрос
        
        //Посылаем нуль-запрос
        Request.send(null);
    }
}

function r_handler(res){
    $("#edost_weight").val(res.response);
}

function fillToReg() {
    var ToCity2=$("#edost_to_city option:selected").text();
    $("#Err1").html('');
    $("#dest_city").val(ToCity2);
    if (ToCity1 != ToCity2) {
        //---Находим код региона по выбранному городу---
        var c1 = -1;
        var i;
        for (i=0; i<cit.length;i++){ if (cit[i]==ToCity2) {c1 = k[i]; break;} }
        if (c1 == -1) $("#ToReg").html('-');
        else $("#ToReg").html(r[c1]);
        $("#edost_weight").focus();
    }

}



function fillDimensions(q) {
    if (q){
        var d = q.split(/\D+/g);
        $('#edost_lenght').val(d[0]);
        $('#edost_width').val(d[1]);
        $('#edost_height').val(d[2]);
    }
}

function fillDimensionsToID(q) {
    if (q){
        $("#edost_dimens [value="+q+"]").attr("selected", "selected");
        changeDimens();
    }else{
        $('#edost_lenght').val("");
        $('#edost_width').val("");
        $('#edost_height').val("");
    }
}
function changeDimens() {
    var q = $("#edost_dimens option:selected").text();
    var d = q.split(/\D+/g);
    $('#edost_lenght').val(d[0]);
    $('#edost_width').val(d[1]);
    $('#edost_height').val(d[2]);
}