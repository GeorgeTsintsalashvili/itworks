
<script type="text/javascript">

// control data list value change handler

function controlDataListValueChangeHandler()
{
  let controlDataLists = document.querySelectorAll("select.control-data-list");
  let paginationLists = document.querySelectorAll(".pagination");
  let formData = new Object();
  let currentParameterName = $(this).attr("data-control-parameter-name");
  let currentDataListValue = $(this).prop("value");
  let address = $("#main-content").attr("data-page-address");

  if(controlDataLists.length >= 2)
  {
     for(let controlDataList of controlDataLists)
     {
       if(!$(controlDataList).is(this))
       {
         let value = $(controlDataList).prop("value");
         let pageKey = $(controlDataList).attr("data-control-parameter-name");

         formData[pageKey] = value;
       }
     }
  }

  if(paginationLists.length != 0)
  {
    for(let paginationList of paginationLists)
    {
      let currentPage = $(paginationList).find(".active").text().trim();
      let pageKey = $(paginationList).parent().attr("data-page-key");

      formData[pageKey] = currentPage;
    }
  }

  formData[currentParameterName] = currentDataListValue;

  refreshPage("POST", address, formData);
}

// initialize update select lists

$(".record-update-data-list").select2();

// initialize form select lists

$(".form-select").select2();

// initialize control data lists

$(".control-data-list").select2();

// bind handlers

$("select.control-data-list").change(controlDataListValueChangeHandler);

</script>
