
<div class="row">
	<div class="col-xs-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="font-6">ინვოისი</h4>
			</div>
			<div class="panel-body">

				<div class="tab-content">

           <div class="tab-pane active" id="invoice-form">

             <form class="form-horizontal row-border" method="POST" action="{{ route('displayInvoice') }}" data-send-address="{{ route('sendInvoice') }}">

  						<div class="form-group">

  						 <label class="col-sm-1 control-label font-6">კოდი</label>
  						 <div class="col-sm-3">
  							 <input data-allowed-empty type="text" class="form-control font-7" name="code">
  						 </div>

               <label class="col-sm-1 control-label font-6">სახელი</label>
  						 <div class="col-sm-3">
  							 <input data-allowed-empty type="text" class="form-control font-7" name="name">
  						 </div>

  						</div>

              <div class="form-group">

               <label class="col-sm-1 control-label font-6">ტელეფონი</label>
               <div class="col-sm-3">
                 <input data-allowed-empty type="text" class="form-control font-7" name="phone">
               </div>

               <label class="col-sm-1 control-label font-6">მისამართი</label>
               <div class="col-sm-3">
                 <input data-allowed-empty type="text" class="form-control font-7" name="address">
               </div>

              </div>

              <div class="form-group">

                <label class="col-sm-1 control-label font-6">Email</label>
                <div class="col-sm-3">
                  <input data-allowed-empty type="text" class="form-control font-7" name="email">
                </div>

                <label class="col-sm-1 control-label font-6">ID</label>
                <div class="col-sm-3">
                  <input type="text" class="form-control font-7" name="invoice-id">
                </div>

              </div>

							<div class="form-group">
							 <label class="col-sm-1 control-label font-6">ნამატი</label>
							 <div class="col-sm-3">
								 <input type="text" class="form-control incrp" data-initial-value="0" name="incrp" data-reg-exp="^\-?(0|[1-9]\d*)$" value="0">
							 </div>

							 <label class="col-sm-1 control-label font-6">ინვოისი</label>
	 						 <div class="col-sm-3">
	 							 <div class="fileinput fileinput-new wpr-100" data-provides="fileinput">
	 	 							<div class="input-group">
	 	 								<div class="form-control uneditable-input" data-trigger="fileinput">
	 	 									<i class="fas fa-upload file-upload-icon"></i>
	 										<span class="fileinput-filename font-6">PDF ფაილი</span>
	 	 								</div>
	 	 								<span class="input-group-btn">
	 	 									<a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">
	 											<i class="fas fa-times"></i>
	 										</a>
	 	 									<span class="btn btn-default btn-file">
	 	 										<span class="fileinput-new upload-button font-6">არჩევა</span>
	 	 										<span class="fileinput-exists file-change-button font-6">შეცვლა</span>
	 	 										<input type="file" name="invoice-document">
	 	 									</span>
	 	 								</span>
	 	 							</div>
	 	 						</div>
	 						 </div>

							</div>

							<div class="panel-footer">
	 							<div class="row">
	 								<div class="col-sm-5 col-sm-offset-1">
	 									<input type="submit" class="btn-primary btn font-6 mr10" value="დაბეჭდვა">
										<input id="send-invoice" type="button" class="btn-primary btn font-6 mr10" value="გაგზავნა">
										<input type="button" data-storage="invoiceProducts" class="btn-primary btn font-6 mr4 clear-form" value="გასუფთავება">
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

				</div>

			</div>
		</div>
  </div>
</div>

@include('parts.controlPanel.generatedFormController')

<script type="text/javascript">

  var systemPrice = 0;

  function displayInvoiceForm()
  {
      let storageName = "invoice";
      let formSelector = "#invoice-form form";
      let invoiceStorage = window.localStorage.getItem("invoiceProducts");

      if(invoiceStorage)
      {
        let invoiceObjects = JSON.parse(invoiceStorage);
        let partsForm = document.querySelector(formSelector);
        let totalPrice = 0;

        for(let key in invoiceObjects)
        {
          let title = invoiceObjects[key]["title"];
          let price = Number.parseInt(invoiceObjects[key]["price"]);
          let quantity = Number.parseInt(invoiceObjects[key]["quantity"]);
          let systemPart = invoiceObjects[key]["systemPart"];

          totalPrice += price * quantity;

					if(systemPart) systemPrice += price * quantity;

          addFormComponent(partsForm, title, price, quantity, null, key, storageName, systemPart);
        }

				$(partsForm).find(".total-price").text(totalPrice);
      }

  }

  displayInvoiceForm();

  generatedFormSubmitController("Invoice", "#invoice-form form");

	// delete product handler

	function deleteProductHandler(e)
	{
		e.preventDefault();

		let uuid = $(this).attr("data-uuid");
		let storageName = "invoiceProducts";

		let invoiceStorage = window.localStorage.getItem(storageName);
		let invoiceObjects = JSON.parse(invoiceStorage);

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

		delete invoiceObjects[uuid];

		$(this).closest(".form-group").remove();

		window.localStorage.setItem(storageName, JSON.stringify(invoiceObjects));
	}

	$(".delete-product").click(deleteProductHandler);

  // send invoice

	$("#send-invoice").click(function(){

			let form = document.querySelector("#invoice-form form");
			let options = new Object();

			let inputs = form.querySelectorAll("[name=email],[name=invoice-id]");
			let invalidInput = null;

      for(input of inputs)
			{
				let value = input.value.trim();

				if(!value.length)
				{
					invalidInput = input;

					break;
				}

				else
				{
					let pattern = input.getAttribute("data-reg-exp");

          if(pattern)
					{
						let regEx = new RegExp(pattern);

						if(!regEx.test(value))
						{
							invalidInput = input;

							break;
						}
					}
				}
			}

			if(!invalidInput)
			{
				let errorCallback = (xhr, status, error) => console.log(xhr.responseText);
				let successCallback = (data) => printMessage(data['success'] ? "ინვოისი გაიგზავნა" : "ინვოისი ვერ გაიგზავნა", 3000);

				options["type"] = $(form).attr("method");
				options["url"] = $(form).attr("data-send-address");
				options["processData"] = false;
				options["contentType"] = false;
				options["data"] = new FormData(form);
				options["error"] = errorCallback;
				options["success"] = successCallback;

				jQuery.ajax(options);
			}

      else $(invalidInput).focus();
	});

</script>

@include('parts.controlPanel.priceAndQuantityChangeHandler')
@include('parts.controlPanel.titleAndWarrantyChangeHandler')
