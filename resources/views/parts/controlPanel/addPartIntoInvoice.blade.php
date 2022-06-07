
<script type="text/javascript">

// add into invoice handler

function addIntoInvoice(systemPart)
{
	let uuid = $(this).attr("data-uuid");
  let title = $(this).closest("tr").find("[data-parameter-name=title]").attr("value");
	let price = Number.parseInt($(this).closest("tr").find("[data-parameter-name=price]").attr("value"));
	let discount = Number.parseInt($(this).closest("tr").find("[data-parameter-name=discount]").attr("value"));

  if(!isNaN(price) && !isNaN(discount))
	{
		let product = new Object();

		product["title"] = title;
		product["price"] = price - discount;
		product["quantity"] = 1;
		product["systemPart"] = systemPart;

		let invoiceStorage = window.localStorage.getItem("invoiceProducts");
		let message = systemPart ? "დამატებულია სისტემური ბლოკის ინვოისში" : "პროდუქცია ინვოისში დამატებულია";

		if(invoiceStorage)
		{
			let invoiceProducts = JSON.parse(invoiceStorage);

			if(!invoiceProducts[uuid])
			{
				invoiceProducts[uuid] = product;

				window.localStorage.setItem("invoiceProducts", JSON.stringify(invoiceProducts));

				printMessage(message, 3000);
			}

			else printMessage("პროდუქცია უკვე არის ინვოისში");
		}

		else
		{
			 let invoiceProducts = new Object();

			 invoiceProducts[uuid] = product;

			 window.localStorage.setItem("invoiceProducts", JSON.stringify(invoiceProducts));

			 printMessage(message, 3000);
		}
	}
}

// bind handlers

$(".invoice-image").click(function(event){

    addIntoInvoice.call(this, 0);
});

$(".invoice-image").contextmenu(function(event){

    event.preventDefault();

    addIntoInvoice.call(this, 1);
});

</script>
