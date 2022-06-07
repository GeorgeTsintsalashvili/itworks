

<div class="row">
	<div class="col-xs-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="font-6 p-abs">საგარანტიო</h4>
				<div class="options">
				 <ul class="nav nav-tabs">
					 <li class="active">
						 <a href="#parts-warranty" data-toggle="tab" class="font-6">ნაწილებისა და ლეპტოპების</a>
					 </li>
					 <li>
						 <a href="#computer-warranty" data-toggle="tab" class="font-6">სისტემური ბლოკის</a>
					 </li>
				 </ul>
				</div>
			</div>
			<div class="panel-body">

				<div class="tab-content">

				 <div class="tab-pane active" id="parts-warranty">

					 <form class="form-horizontal row-border" method="POST" action="{{ route('displayWarranty') }}">
						<div class="form-group">
							<label class="col-sm-1 control-label font-6">პ/ნ</label>
						 <div class="col-sm-3">
							 <input type="text" class="form-control font-7" name="clientId">
							 <input type="hidden" class="form-control font-7" name="incrp" value="0">
						 </div>
						</div>
						<div class="form-group">
							<label class="col-sm-1 control-label font-6">მენეჯერი</label>
						 <div class="col-sm-3">
							 <select class="manager wpr-100" name="manager">
								 @foreach($managers as $manager)
									 <option class="font-6" value="{{ $manager -> name }}">{{ $manager -> name }}</option>
								 @endforeach
							 </select>
						 </div>
						</div>
 						<div class="panel-footer">
							<div class="row">
 								<div class="col-sm-5 col-sm-offset-1">
 									<input type="submit" class="btn-primary btn font-6 mr10" value="დაბეჭდვა">
									<input type="button" data-storage="warrantyProducts" class="btn-primary btn font-6 mr4 clear-form" value="გასუფთავება">
 								</div>
								<div class="col-sm-6 text-right" style="font-size: 20px">
									<b class="font-6" style="color: #247c6c"> სრული თანხა </b>
									<span class="total-price font-6">0</span>
									<span class="currency"> ₾</span>
								</div>
 							</div>
 						</div>
 					</form>

         </div>

         <div class="tab-pane" id="computer-warranty">

					 <form class="form-horizontal row-border" method="POST" action="{{ route('displayWarranty') }}">
						<div class="form-group">
							<label class="col-sm-1 control-label font-6">პ/ნ</label>
						 <div class="col-sm-3">
							 <input type="text" class="form-control font-7" name="clientId">
						 </div>
						</div>
						<div class="form-group">
						 <label class="col-sm-1 control-label font-6">ნამატი</label>
						 <div class="col-sm-3">
							 <input type="text" class="form-control incrp" name="incrp" data-initial-value="0" data-reg-exp="^\-?(0|[1-9]\d*)$" value="0">
						 </div>
						</div>
						<div class="form-group">
							<label class="col-sm-1 control-label font-6">მენეჯერი</label>
						 <div class="col-sm-3">
							 <select class="manager wpr-100" name="manager">
								 @foreach($managers as $manager)
									 <option class="font-6" value="{{ $manager -> name }}">{{ $manager -> name }}</option>
								 @endforeach
							 </select>
						 </div>
						</div>
 						<div class="panel-footer">
 							<div class="row">
 								<div class="col-sm-3 col-sm-offset-1">
 									<input type="submit" class="btn-primary btn font-6 mr10" value="დაბეჭდვა">
									<input type="button" data-storage="warrantyComputer" class="btn-primary btn font-6 mr4 clear-form" value="გასუფთავება">
 								</div>
								<div class="col-sm-8 text-right" style="font-size: 20px">
									<b class="font-6" style="color: #247c6c"> სრული თანხა </b>
									<span class="total-price font-6">0</span>
									<span class="currency"> ₾</span>
								</div>
 							</div>
 						</div>
 					</form>

				 </div>

				</div>

			</div>
		</div>
  </div>
</div>

@include('parts.controlPanel.generatedFormController')

<script type="text/javascript">

  $(".manager").select2();

	var systemPrice = 0;

	function displayWarrantyProducts()
	{
	  let storagesOptions = [ {"name" : "warrantyProducts",
	                           "storage" : "parts",
	                           "form" : "#parts-warranty form"},
	                          {"name":  "warrantyComputer",
	                           "storage": "computer",
	                           "form": "#computer-warranty form"} ];

	  for(let index in storagesOptions)
	  {
	    let storageName = storagesOptions[index]["name"];
	    let formSelector = storagesOptions[index]["form"];
	    let storage = storagesOptions[index]["storage"];
	    let warrantyStorage = window.localStorage.getItem(storageName);

	    if(warrantyStorage)
	    {
	      let warrantyObjects = JSON.parse(warrantyStorage);
	      let partsForm = document.querySelector(formSelector);
				let totalPrice = 0;

	      for(let key in warrantyObjects)
	      {
	        let title = warrantyObjects[key]["title"];
	        let price = Number.parseInt(warrantyObjects[key]["price"]);
	        let quantity = Number.parseInt(warrantyObjects[key]["quantity"]);
	        let warranty = warrantyObjects[key]["warranty"];
	        let systemPart = warrantyObjects[key]["systemPart"];

          totalPrice += quantity * price;

					if(systemPart) systemPrice += price * quantity;

	        addFormComponent(partsForm, title, price, quantity, warranty, key, storage, systemPart);
	      }

				$(partsForm).find(".total-price").text(totalPrice);
	    }
	  }
	}

	displayWarrantyProducts();

	generatedFormSubmitController("Warranty", "#parts-warranty form, #computer-warranty form");

	// delete product handler

	function deleteProductHandler(e)
	{
	  e.preventDefault();

	  let uuid = $(this).attr("data-uuid");
	  let storageName = $(this).attr("data-storage") === "computer" ? "warrantyComputer" : "warrantyProducts";

	  let warrantyStorage = window.localStorage.getItem(storageName);
	  let warrantyObjects = JSON.parse(warrantyStorage);

    let productPrice = Number.parseInt($(this).parents(".form-group").find("input.product-price").prop("value"));
		let quantity = Number.parseInt($(this).parents(".form-group").find("input.product-quantity").prop("value"));
		let systemPart = Number.parseInt($(this).parents(".form-group").find("input.system-part").prop("value"));

    if(productPrice && quantity && productPrice > 0 && quantity > 0)
		{
			let totalPriceElement = $(this).parents("form").find(".total-price");
      let totalPrice = Number.parseInt($(totalPriceElement).text());

      if(!isNaN(totalPrice))
			{
        let priceToSubtract = quantity * productPrice;

				totalPrice -= priceToSubtract;

				$(totalPriceElement).text(totalPrice);

				if(systemPart) systemPrice -= priceToSubtract;
			}
		}

	  delete warrantyObjects[uuid];

	  $(this).closest(".form-group").remove();

	  window.localStorage.setItem(storageName, JSON.stringify(warrantyObjects));
	}

	$(".delete-product").click(deleteProductHandler);

</script>

@include('parts.controlPanel.priceAndQuantityChangeHandler')
@include('parts.controlPanel.titleAndWarrantyChangeHandler')
