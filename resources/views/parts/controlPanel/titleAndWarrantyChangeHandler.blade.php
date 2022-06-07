
<script type="text/javascript">

$(".form-product-title,.form-warranty-title").change(function(){

  let inputValue = $(this).prop("value").trim();

  if(inputValue.length)
  {
    let storageName = $(this).closest("form").find(".clear-form").attr("data-storage");
    let localStorage = window.localStorage.getItem(storageName);

    if(localStorage)
    {
      let products = JSON.parse(localStorage);
      let productUuid = $(this).parents(".form-group").attr("data-product-uuid");
      let parameterName = $(this).attr("data-parameter-name");

      products[productUuid][parameterName] = inputValue;
      products[productUuid][parameterName];

      window.localStorage.setItem(storageName, JSON.stringify(products));
    }
  }

});

</script>
