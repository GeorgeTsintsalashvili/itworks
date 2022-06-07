
@include('parts.controlPanel.general')

<script type="text/javascript">

// page refresh

function refreshPage(method, address, formData = {})
{
    let options = new Object();

    let successCallback = function(data){

      localStorage.setItem("requestMethod", method);
      localStorage.setItem("pageAddress", address);
      localStorage.setItem("pageFormData", JSON.stringify(formData));

      $("#main-content").html(data);
    }

    options["type"] = method;
    options["url"] = address;
    options["data"] = formData;
    options["error"] = (xhr, status, error) => console.log(xhr.responseText);
    options["success"] = successCallback;
    options["async"] = true;

    jQuery.ajax(options);
}

// retrieve control form data

function getControlFormData()
{
  let formData = new Object();
  let controlDataLists = document.querySelectorAll("select.control-data-list");
  let paginationLists = document.querySelectorAll(".pagination");
  let controlTextInputs = document.querySelectorAll("input.control-field");

  if(controlTextInputs.length)
  {
     for(let controlTextInput of controlTextInputs)
     {
       let value = $(controlTextInput).prop("value");
       let paramName = $(controlTextInput).attr("data-control-parameter-name");

       formData[paramName] = value;
     }
  }

  if(paginationLists.length)
  {
    for(let paginationList of paginationLists)
    {
      let currentPage = $(paginationList).find(".active").text().trim();
      let pageKey = $(paginationList).parent().attr("data-page-key");

      formData[pageKey] = currentPage;
    }
  }

  if(controlDataLists.length)
  {
     for(let controlDataList of controlDataLists)
     {
       let value = $(controlDataList).prop("value");
       let pageKey = $(controlDataList).attr("data-control-parameter-name");

       formData[pageKey] = value;
     }
  }

  return formData;
}

// page switch handler

function pageSwitchHandler(e)
{
  e.preventDefault();

  let currentPaginationList = $(this).parents(".pagination");
  let currentPageKey = $(currentPaginationList).parent().attr("data-page-key");
  let controlFormData = getControlFormData(currentPaginationList);
  let address = $("#main-content").attr("data-page-address");

  delete controlFormData[currentPageKey];

  controlFormData[currentPageKey] = $(this).attr("href").trim();

  refreshPage("POST", address, controlFormData);
}

// record delete handler

function recordDeleteHandler(e)
{
  e.preventDefault();

  let parameters = new Object();
  let pageAddress = $("#main-content").attr("data-page-address");
  let successCallback = (data) => (data["deleted"] && refreshPage("POST", pageAddress)) || printMessage(data["deleted"] ? "წაშლა შესრულდა" : "წაშლა არ შესრულდა", 3000);

  sendRequest("GET", $(this).attr("href"), parameters, successCallback);
}

// form submit handler

function formSubmitHandler(e)
{
  e.preventDefault();

  let pageAddress = $("#main-content").attr("data-page-address");
  let successCallback = (data) => (data["success"] && refreshPage("POST", pageAddress)) || printMessage(data["success"] ? "მოთხოვნა დაკმაყოფილდა" : "მოთხოვნა არ დაკმაყოფილდა", 3000);

  sendRequest($(this).attr("method"), $(this).attr("action"), $(this).serialize(), successCallback);
}

// form submit handler without page refresh

function formSubmitHandlerWithoutPageRefresh(e)
{
  e.preventDefault();

  let formReset = $(this).hasClass("form-reset");

  let successCallback = (data) => printMessage(data["success"] ? (formReset && !$(this).trigger("reset")) || "მოთხოვნა დაკმაყოფილდა" : "მოთხოვნა არ დაკმაყოფილდა", 3000);

  sendRequest($(this).attr("method"), $(this).attr("action"), $(this).serialize(), successCallback);
}

// field value change handler

function fieldValueChangeHandler()
{
  let route = $(this).closest("table").attr("data-update-route");
  let method = "POST";
  let formData = new Object();
  let successCallback = function(data){

      let message = "განახლება არ შესრულდა";

      if(data["updated"])
      {
        let pageAddress = $("#main-content").attr("data-page-address");
        let controlFormData = getControlFormData();

        refreshPage("POST", pageAddress, controlFormData);

        message = "განახლება შესრულდა";
      }

      printMessage(message, 3000);
  };

  formData["record-id"] = $(this).closest("tr").attr("data-record-id");

  $(this).closest("tr").find("input,select").each(function(index, object){

       if($(object).attr("data-parameter-name"))
       {
         let key = $(object).attr("data-parameter-name");
         let value = $(object).prop("value");

         formData[key] = value;
       }
  });

  sendRequest(method, route, formData, successCallback);
}

// password eye click handler

function passwordEyeClickHandler()
{
   let dataSlash = Number.parseInt($(this).attr("data-slash"));
   let valuesToAssign = dataSlash ? ["fa-eye", "text", "fa-eye-slash"] : ["fa-eye-slash", "password", "fa-eye"];

   $(this).attr("data-slash", Number(!dataSlash));
   $(this).find("i").removeClass(valuesToAssign[2]).addClass(valuesToAssign[0]);
   $(this).next("input").attr("type", valuesToAssign[1]);
}

// search text input change handler

function runSearch()
{
  let inputText = $(this).val();

  if(inputText.indexOf(" ") != 0)
  {
    let pageAddress = $("#main-content").attr("data-page-address");
    let controlFormData = getControlFormData();

    refreshPage("POST", pageAddress, controlFormData);
  }
}

// bind handlers

$(".field").change(fieldValueChangeHandler);
$(".data-form").submit(formSubmitHandler);
$("#search-button").click(runSearch);
$(".data-form-no-reload").submit(formSubmitHandlerWithoutPageRefresh);
$(".delete-record").click(recordDeleteHandler);
$(".page-switch").click(pageSwitchHandler);
$(".password-eye").click(passwordEyeClickHandler);

$("#search-field").bind("keypress", function(e) {

    if (e.keyCode == 13)
    {
      let inputText = $(this).prop("value").trim();

      runSearch.call(this);
    }
});

</script>
