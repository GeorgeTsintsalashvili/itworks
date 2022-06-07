
<script type="text/javascript">

function addPartWarrantyHandler(e)
{
	e.preventDefault();

  let warrantyPage = $(this).attr("data-warranty-page");
	let warrantyStorageName = warrantyPage === "parts" ? "warrantyProducts" : "warrantyComputer";

	let uuid = $(this).attr("href");
	let warranty = $(this).attr("data-warranty");
	let systemPart = Number.parseInt($(this).attr("data-system-part"));
  let title = $(this).closest("tr").find("[data-parameter-name=title]").attr("value");
	let price = Number.parseInt($(this).closest("tr").find("[data-parameter-name=price]").attr("value"));
	let discount = Number.parseInt($(this).closest("tr").find("[data-parameter-name=discount]").attr("value"));

  if(!isNaN(price) && !isNaN(discount))
	{
		let product = new Object();

		product["warranty"] = warranty;
		product["title"] = title;
		product["price"] = price - discount;
		product["quantity"] = 1;
		product["systemPart"] = systemPart;

		let warrantyStorage = window.localStorage.getItem(warrantyStorageName);

		if(warrantyStorage)
		{
			let warrantyProducts = JSON.parse(warrantyStorage);

			if(!warrantyProducts[uuid])
			{
				warrantyProducts[uuid] = product;

				window.localStorage.setItem(warrantyStorageName, JSON.stringify(warrantyProducts));

				printMessage("პროდუქცია დაემატა საგარანტიოში", 3000);
			}

			else printMessage("პროდუქცია უკვე არის საგარანტიოში");
		}

		else
		{
			 let warrantyObjects = new Object();

			 warrantyObjects[uuid] = product;

			 window.localStorage.setItem(warrantyStorageName, JSON.stringify(warrantyObjects));

			 printMessage("პროდუქცია დაემატა საგარანტიოში", 3000);
		}
	}
}

$(".add-warranty").click(addPartWarrantyHandler);

</script>
