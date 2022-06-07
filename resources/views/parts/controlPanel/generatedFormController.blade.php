
<script type="text/javascript">

function generatedFormSubmitController(windowName, selector)
{
  $(selector).submit(function(e){

       e.preventDefault();

       let allInputs = this.querySelectorAll("input[type=text].form-control:not([data-allowed-empty])");
       let invalidField = null;

       allInputs.forEach((item, i) => {

            let value = $(item).prop("value").trim();

            if(value.length != 0)
            {
               let pattern = $(item).attr("data-reg-exp");
               let regEx = new RegExp(pattern);

               if(regEx)
               {
                 if(!regEx.test(value))
                 {
                   invalidField = item;

                   return false;
                 }
               }
            }

            else
            {
              invalidField = item;

              return false;
            }
       });

       if(!invalidField)
       {
         let width = document.body.clientWidth - 100;
         let height = document.body.clientHeight - 100;

         let options = `resizable=yes,height=${height},width=${width},location=0,menubar=0,scrollbars=1`;
         let newWindow = window.open(this.action, windowName, options);

         this.target = windowName;
         this.submit();

         newWindow.print();
       }

       else invalidField.focus();
  });
}

function addFormComponent(form, title, price, quantity, warranty, uuid, storage, systemPart)
{
  let formGroup = document.createElement("div");

  formGroup.className = "form-group";

  formGroup.setAttribute("data-product-uuid", uuid);

  // title elements

  let titleLabelCol = document.createElement("label");
  let titleCol = document.createElement("div");
  let titleInput = document.createElement("input");

  titleLabelCol.className = "col-sm-1 font-4";
  titleLabelCol.innerText = "სათაური";
  titleCol.className = "col-sm-3";

  titleInput.className = "form-control form-product-title";
  titleInput.setAttribute("data-parameter-name", "title");
  titleInput.type = "text";
  titleInput.name = "title[]";
  titleInput.value = title;

  titleCol.appendChild(titleInput);

  // price elements

  let priceLabelCol = document.createElement("label");
  let priceCol = document.createElement("div");
  let priceInput = document.createElement("input");

  priceLabelCol.className = "col-sm-1 font-4";
  priceLabelCol.innerText = "ფასი";
  priceCol.className = "col-sm-1";

  priceInput.className = "form-control product-price";
  priceInput.type = "text";
  priceInput.name = "price[]";
  priceInput.setAttribute("data-reg-exp", "^([1-9]\\d*)$");
  priceInput.value = price;
  priceInput.setAttribute("data-initial-value", price);

  priceCol.appendChild(priceInput);

  // quantity elements

  let quantityLabelCol = document.createElement("label");
  let quantityCol = document.createElement("div");
  let quantityInput = document.createElement("input");

  quantityLabelCol.className = "col-sm-1 font-4";
  quantityLabelCol.innerText = "ერთეული";
  quantityCol.className = "col-sm-1";

  quantityInput.className = "form-control product-quantity";
  quantityInput.type = "text";
  quantityInput.name = "quantity[]";
  quantityInput.setAttribute("data-reg-exp", "^([1-9]\\d*)$");
  quantityInput.value = quantity;
  quantityInput.setAttribute("data-initial-value", quantity);

  quantityCol.appendChild(quantityInput);

  // warranty elements

  let warrantyLabelCol = document.createElement("label");
  let warrantyCol = document.createElement("div");
  let warrantyInput = document.createElement("input");

  warrantyLabelCol.className = "col-sm-1 font-4";
  warrantyLabelCol.innerText = "გარანტია";
  warrantyCol.className = "col-sm-2";

  warrantyInput.className = "form-control form-warranty-title";
  warrantyInput.setAttribute("data-parameter-name", "warranty");
  warrantyInput.type = "text";
  warrantyInput.name = "warranty[]";
  warrantyInput.value = warranty;

  warrantyCol.appendChild(warrantyInput);

  // system part

  let systemPartInput = document.createElement("input");

  systemPartInput.type = "hidden";
  systemPartInput.className = "system-part";
  systemPartInput.name = "systemPart[]";
  systemPartInput.value = systemPart;

  // delete button

  let deleteCol = document.createElement("div");
  let deleteLink = document.createElement("a");
  let deleteIcon = document.createElement("i");

  deleteCol.className = "col-sm-1";
  deleteLink.className = "delete-product text-center wpr-100 block";
  deleteLink.setAttribute("data-uuid", uuid);
  deleteLink.setAttribute("data-storage", storage);
  deleteIcon.className = "fas fa-trash-alt p5";
  deleteIcon.style.fontSize = "24px";

  deleteLink.appendChild(deleteIcon);
  deleteCol.appendChild(deleteLink);

  // append form group parts

  formGroup.appendChild(titleLabelCol);
  formGroup.appendChild(titleCol);

  formGroup.appendChild(priceLabelCol);
  formGroup.appendChild(priceCol);

  formGroup.appendChild(quantityLabelCol);
  formGroup.appendChild(quantityCol);

  if(warranty)
  {
    formGroup.appendChild(warrantyLabelCol);
    formGroup.appendChild(warrantyCol);
  }

  formGroup.appendChild(systemPartInput);
  formGroup.appendChild(deleteCol);

  // append form group to form

  form.prepend(formGroup);
}

// form clear handler

function clearForm()
{
  let storageName = $(this).attr("data-storage");
  let storage = window.localStorage.getItem(storageName);

  if(storage)
  {
    let objects = JSON.parse(storage);

    $(this).parents("form").find(".form-group").remove();
    $(this).parents("form").find(".total-price").text(0);

    for(let key in objects) delete objects[key];

    window.localStorage.setItem(storageName, JSON.stringify(objects));
  }
}

// bind handler

$(".clear-form").click(clearForm);

</script>
