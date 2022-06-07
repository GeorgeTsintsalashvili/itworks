<script type="text/javascript">

let iframeOverlay = document.getElementById("iframe-overlay");
let stockCheckStr = document.getElementById("stock-check-str").value;
let stockMap = new Map();

stockMap.set("0", 1);

document.getElementById("window-close-button").addEventListener("click", () => iframeOverlay.style.display = "none");

document.querySelectorAll(".part-remove-button").forEach((removeButton) => {

  removeButton.addEventListener("click", (e) => {

    let partBlock = document.getElementById(removeButton.getAttribute("data-part-block"));
    let titleElement = partBlock.querySelector(".part-title-field");
    let priceElement = partBlock.querySelector(".part-price-field");
    let partIdElement = partBlock.querySelector(".system-part-id");
    let stockIdElement = partBlock.querySelector(".system-part-stock-type-id");

    titleElement.value = "";
    priceElement.value = "";
    partIdElement.value = "0";
    stockIdElement.value = "1";

    incrementPrice();
    updateEditorText();

  });

});

stockCheckStr.split(":").forEach((stockCheck) => {

  let stockCheckParts = stockCheck.split("-");

  stockMap.set(stockCheckParts[0], parseInt(stockCheckParts[1]));

});

let productClickHandler = (products, selectButton) => {

  products.forEach((product) => {

    let link = product.querySelector("a");

    link.addEventListener("click", (e) => {

      e.preventDefault();

      let productTitle = product.getAttribute("data-product-title");
      let productPrice = product.getAttribute("data-product-price");
      let productId = product.getAttribute("data-product-id");
      let stockTypeId = product.getAttribute("data-stock-type-id");

      let partBlock = document.getElementById(selectButton.getAttribute("data-part-block"));
      let titleElement = partBlock.querySelector(".part-title-field");
      let priceElement = partBlock.querySelector(".part-price-field");
      let partIdElement = partBlock.querySelector(".system-part-id");
      let stockIdElement = partBlock.querySelector(".system-part-stock-type-id");

      titleElement.value = productTitle;
      priceElement.value = productPrice;
      partIdElement.value = productId;
      stockIdElement.value = stockTypeId;

      updateEditorText();

      iframeOverlay.style.display = "none";

      incrementPrice();

    });

  });
}

document.querySelectorAll(".part-select-button").forEach((partSelectButton) => {

  partSelectButton.addEventListener("click", () => {

    let iframe = document.getElementById("part-select-window");

    iframe.src = partSelectButton.getAttribute("data-parts-address");

    iframe.onload = () => {

      iframeOverlay.style.display = "block";

      let resultContent = iframe.contentWindow.document.getElementById("result-content");

      let config = { childList: true, subtree: true };

      let callback = (mutationsList, observer) => {

        for (let mutation of mutationsList)
        {
          if (mutation.type === "childList")
          {
            let products = resultContent.querySelectorAll(".ajax-block-product");

            productClickHandler(products, partSelectButton);
          }
        }
      };

      let observer = new MutationObserver(callback);

      observer.observe(resultContent, config);

      productClickHandler(resultContent.querySelectorAll(".ajax-block-product"), partSelectButton);
    }

  });

});

let partsTitlesFields = document.querySelectorAll(".part-title-field");
let partsPricesFields = document.querySelectorAll(".part-price-field");
let systemPriceElement = document.querySelector(".system-price");

let fullDescriptionContainer = document.getElementById("system-full-description-container");
let headerTemplate = document.getElementById("header-template");
let descriptionBody = document.getElementById("description-body");
let footerTemplate = document.getElementById("footer-template");
let headerTemplateInput = document.querySelector(".header-template");
let footerTemplateInput = document.querySelector(".footer-template");

partsPricesFields.forEach((part) => {

  part.addEventListener("keyup", () => {

    let partBlock = document.getElementById(part.getAttribute("data-part-block"));
    let stockTypeIdElement = partBlock.querySelector(".system-part-stock-type-id");
    let partIdElement = partBlock.querySelector(".system-part-id");

    stockTypeIdElement.value = "0";
    partIdElement.value = "0";

    updateEditorText();
    incrementPrice();

  });

});

partsTitlesFields.forEach((part) => {

  part.addEventListener("keyup", () => {

    updateEditorText();
    incrementPrice();

  });

});

$("#header-template-select").change(function (e) {

  let templateId = parseInt(this.value);

  if (templateId)
  {
    let template = document.getElementById("hidden-header-template-" + templateId);

    headerTemplate.innerHTML = template.innerHTML;
    headerTemplateInput.value = template.innerHTML;
  }

  else
  {
    headerTemplate.innerHTML = "";
    headerTemplateInput.value = "";
  }

  updateEditorText();

});

$("#footer-template-select").change(function (e) {

  let templateId = parseInt(this.value);

  if (templateId)
  {
    let template = document.getElementById("hidden-footer-template-" + templateId);

    footerTemplate.innerHTML = template.innerHTML;
    footerTemplateInput.value = template.innerHTML;
  }

  else
  {
    footerTemplate.innerHTML = "";
    footerTemplateInput.value = "";
  }

  updateEditorText();

});

function updateEditorText()
{
  let bodyDescriptionStr = "";

  partsTitlesFields.forEach((titleField) => {

    let partBlock = document.getElementById(titleField.getAttribute("data-part-block"));
    let stockIdElement = partBlock.querySelector(".system-part-stock-type-id");
    let partIdElement = partBlock.querySelector(".system-part-id");
    let priceElement = partBlock.querySelector(".part-price-field");
    let titlePrefix = partBlock.getAttribute("data-title-prefix");

    if (titleField.value.trim().length && parseInt(priceElement.value))
    {
      let partId = parseInt(partIdElement.value);
      let stockId = stockIdElement.value;
      let checkStock = stockMap.get(stockId);

      bodyDescriptionStr += `<p><b>✔️ ${titlePrefix} - ${titleField.value.trim()}</b></p>`; // `<p><b>✔️ ${titlePrefix} - ${titleField.value.trim()} ${!partId || checkStock ? "(მარაგის გადასამოწმებლად დაგვიკავშირდით)" : ""}</b></p>`;
    }

    else
    {
      stockIdElement.value = "0";
      partIdElement.value = "0";
      priceElement.value = "";
    }

  });

  descriptionBody.innerHTML = bodyDescriptionStr;

  let overalText = "";
  let headerText = headerTemplate.innerHTML.trim();
  let footerText = footerTemplate.innerHTML.trim();

  if (headerText.length)
  {
    overalText += headerText + "<br>";
  }

  if (bodyDescriptionStr.length)
  {
    bodyDescriptionStr = "<p><b>სისტემური ბლოკის მახასიათებლები:</b></p><br>" + bodyDescriptionStr;

    overalText += bodyDescriptionStr + "<br>";
  }

  if (footerText.length)
  {
    overalText += footerText;
  }

  tinymce.get("parts-editor").setContent(overalText);
}

document.getElementById("copy-to-clipboard-button").addEventListener("click", () => {

  tinymce.activeEditor.selection.select(tinymce.activeEditor.getBody());
  tinymce.activeEditor.execCommand("Copy");

});

function incrementPrice()
{
  let computerPrice = 0;

  partsPricesFields.forEach((part) => {

    let partPrice = parseInt(part.value);

    if (!isNaN(partPrice))

    computerPrice += partPrice;

  });

  systemPriceElement.value = computerPrice;
}

</script>
