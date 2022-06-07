
<script type="text/javascript">

function priceChangeHandler()
{
  let price = Number.parseInt($(this).prop("value"));

  if(!isNaN(price) && price > 0)
  {
    let storageName = $(this).closest("form").find(".clear-form").attr("data-storage");
    let productUuid = $(this).parents(".form-group").attr("data-product-uuid");
    let localStorage = window.localStorage.getItem(storageName);

    let quantity = Number.parseInt($(this).parents(".form-group").find(".product-quantity").prop("value"));
    let systemPart = Number.parseInt($(this).parents(".form-group").find(".system-part").prop("value"));

    if(!isNaN(quantity) && quantity > 0 && localStorage)
    {
      let products = JSON.parse(localStorage);

      let oldPrice = Number.parseInt($(this).attr("data-initial-value"));
      let systemOldPrice = systemPrice;
      let priceToAdd = quantity * price;
      let priceToSubtract = oldPrice * quantity;

      let totalPriceElement = $(this).parents("form").find(".total-price");
      let totalPrice = Number.parseInt($(totalPriceElement).text());

      products[productUuid]["price"] = price;

      window.localStorage.setItem(storageName, JSON.stringify(products));

      if(systemPart)
      {
        systemPrice -= priceToSubtract;
        systemPrice += priceToAdd;

        $(totalPriceElement).text(totalPrice - systemOldPrice + systemPrice);
      }

      else $(totalPriceElement).text(totalPrice - priceToSubtract + priceToAdd);

      $(this).attr("data-initial-value", price);
    }
  }
}

function quantityChangeHandler()
{
  let quantity = Number.parseInt($(this).prop("value"));

  if(!isNaN(quantity) && quantity > 0)
  {
    let price = Number.parseInt($(this).parents(".form-group").find(".product-price").prop("value"));
    let systemPart = Number.parseInt($(this).parents(".form-group").find(".system-part").prop("value"));

    let storageName = $(this).closest("form").find(".clear-form").attr("data-storage");
    let productUuid = $(this).parents(".form-group").attr("data-product-uuid");
    let localStorage = window.localStorage.getItem(storageName);

    if(!isNaN(price) && price > 0 && localStorage)
    {
      let products = JSON.parse(localStorage);

      let oldQuantity = Number.parseInt($(this).attr("data-initial-value"));
      let systemOldPrice = systemPrice;
      let priceToAdd = quantity * price;
      let priceToSubtract = oldQuantity * price;

      let totalPriceElement = $(this).parents("form").find(".total-price");
      let totalPrice = Number.parseInt($(totalPriceElement).text());

      products[productUuid]["quantity"] = quantity;

      window.localStorage.setItem(storageName, JSON.stringify(products));

      if(systemPart)
      {
        systemPrice -= priceToSubtract;
        systemPrice += priceToAdd;

        $(totalPriceElement).text(totalPrice - systemOldPrice + systemPrice);
      }

      else $(totalPriceElement).text(totalPrice - priceToSubtract + priceToAdd);

      $(this).attr("data-initial-value", quantity);
    }
  }
}

$(".product-quantity").change(quantityChangeHandler);

$(".product-price").change(priceChangeHandler);

$(".incrp").change(function(){

    let incrp = Number.parseInt($(this).prop("value"));

    if(!isNaN(incrp))
    {
      let totalPriceElement = $(this).parents("form").find(".total-price");
      let totalPrice = Number.parseInt($(totalPriceElement).text());
      let absIncrp = Math.abs(incrp);

      if(incrp > 0)
      {
        totalPrice -= systemPrice;
        systemPrice += incrp;
        totalPrice += systemPrice;
      }

      else if(absIncrp < systemPrice)
      {
        totalPrice -= systemPrice;
        systemPrice += incrp;
        totalPrice += systemPrice;
      }

      $(totalPriceElement).text(totalPrice);
    }
});

</script>
