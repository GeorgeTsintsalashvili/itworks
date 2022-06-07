
<div class="panel panel-default" id="product-data-container" data-record-id="{{ $product -> id }}">

 <div class="panel-heading">
   <h2 id="back-button">
     <i class="fas fa-long-arrow-alt-left"></i>
     <span class="font-3"> უკან დაბრუნება </span>
   </h2>
   <div class="options">
    <ul class="nav nav-tabs">
      <li class="active">
        <a href="#editor" data-toggle="tab" class="font-6">რედაქტორი</a>
      </li>
      <li>
        <a href="#images" data-toggle="tab" class="font-6">სურათები</a>
      </li>
    </ul>
   </div>
 </div>

 <div class="panel-body">

  <div class="tab-content">

    <div class="tab-pane active" id="editor">

      <form class="form-horizontal record-update-form" method="POST" action="{{ route('cpuUpdate') }}">

          <div class="form-group">

             <input type="hidden" name="record-id" value="{{ $product -> id}}">

             <div class="col-sm-12">
              <textarea class="kt-tinymce" name="description">{!! $product -> description !!}</textarea>
             </div>

          </div>

          <div class="form-group">

            <label class="col-sm-1 control-label font-6">SEO</label>

            <div class="col-sm-5">
              <textarea class="form-control font-1" name="seoKeywords" rows="6" placeholder="საგასაღებო სიტყვები">{{ $product -> seoKeywords }}</textarea>
            </div>

            <label class="col-sm-1 control-label font-6"> </label>

            <div class="col-sm-5">
              <textarea class="form-control font-1" name="seoDescription" rows="6" placeholder="მოკლე აღწერა">{{ $product -> seoDescription }}</textarea>
            </div>

          </div>

          <div class="form-group">

             <label class="col-sm-1 control-label font-6">სოკეტი</label>
             <div class="col-sm-2">
              <select name="socketId" class="edit-page-list wpr-100">
               @foreach($sockets as $value)
                 @if($value -> id == $product -> socketId)
                   <option data-chipsets-identifiers="{{ $value -> chipsetsIdentifiers }}" data-chipsets-titles="{{ $value -> chipsetsTitles }}" selected class="font-6" value="{{ $value -> id }}">{{ $value -> socketTitle }}</option>
                 @else
                   <option data-chipsets-identifiers="{{ $value -> chipsetsIdentifiers }}" data-chipsets-titles="{{ $value -> chipsetsTitles }}" class="font-6" value="{{ $value -> id }}">{{ $value -> socketTitle }}</option>
                 @endif
               @endforeach
              </select>
             </div>

             <label class="col-sm-1 control-label font-6">CPU TCP</label>
             <div class="col-sm-2">
              <select name="technologyProcessId" class="edit-page-list wpr-100">
               @foreach($technologyProcesses as $value)
                 @if($value -> id == $product -> technologyProcessId)
                   <option selected class="font-6" value="{{ $value -> id }}">{{ $value -> size }}</option>
                 @else
                   <option class="font-6" value="{{ $value -> id }}">{{ $value -> size }}</option>
                 @endif
               @endforeach
             </select>
            </div>

               <label class="col-sm-1 control-label font-6">გარანტია</label>
               <div class="col-sm-2">
                <input type="text" name="warrantyDuration" class="form-control font-7" value="{{ $product -> warrantyDuration }}">
               </div>

               <label class="col-sm-1 control-label font-6">ვადა</label>
               <div class="col-sm-2">

                <select name="warrantyId" class="edit-page-list wpr-100">
                 @foreach($warranties as $value)
                   @if($value -> id == $product -> warrantyId)
                     <option selected class="font-6" value="{{ $value -> id }}">{{ $value -> durationUnit }}</option>
                   @else
                     <option class="font-6" value="{{ $value -> id }}">{{ $value -> durationUnit }}</option>
                   @endif
                 @endforeach
               </select>
              </div>

           </div>

           <div class="form-group">

             <label class="col-sm-1 control-label font-6">ბრენდი</label>
             <div class="col-sm-2">
              <select name="manufacturerId" class="edit-page-list wpr-100">
               @foreach($manufacturers as $value)
                 @if($value -> id == $product -> manufacturerId)
                   <option selected class="font-6" value="{{ $value -> id }}">{{ $value -> manufacturerTitle }}</option>
                 @else
                   <option class="font-6" value="{{ $value -> id }}">{{ $value -> manufacturerTitle }}</option>
                 @endif
               @endforeach
             </select>
            </div>

            <label class="col-sm-1 control-label font-6">სისტემა</label>
            <div class="col-sm-2">
             <select name="seriesId" class="edit-page-list wpr-100">
              @foreach($series as $value)
                @if($value -> id == $product ->seriesId)
                  <option selected class="font-6" value="{{ $value -> id }}">{{ $value -> seriesTitle }}</option>
                @else
                  <option class="font-6" value="{{ $value -> id }}">{{ $value -> seriesTitle }}</option>
                @endif
              @endforeach
            </select>
           </div>

           <label class="col-sm-1 control-label font-6">სიხშირე</label>
           <div class="col-sm-2">
            <input type="text" name="clockSpeed" class="form-control font-7" value="{{ $product -> clockSpeed }}">
           </div>

           <label class="col-sm-1 control-label font-6">ბირთვები</label>
           <div class="col-sm-2">
              <input type="text" name="cores" class="form-control font-7" value="{{ $product -> cores }}">
           </div>

           </div>

           <div class="form-group">

             <label class="col-sm-1 control-label font-6">რაოდენობა</label>
             <div class="col-sm-2">
              <input type="text" name="quantity" class="form-control font-7" value="{{ $product -> quantity }}">
             </div>

             <label class="col-sm-1 control-label font-6">ჩიპსეტი</label>
             <div class="col-sm-5">
              <select multiple id="chipsets-select" class="form-select wpr-100 populate">

                @foreach($chipsets as $chipset)
                  <option data-socket-id="{{ $chipset -> socketId }}" class="font-6" selected value="{{ $chipset -> id }}">{{ $chipset -> chipsetTitle }}</option>
                @endforeach

                @foreach($restOfChipsets as $chipset)
                  <option data-socket-id="{{ $chipset -> socketId }}" class="font-6" value="{{ $chipset -> id }}">{{ $chipset -> chipsetTitle }}</option>
                @endforeach

              </select>

              <input name="chipsetId" type="hidden" id="chipsets" value="{{ $chipsets -> pluck('id') -> implode(',') }}">

             </div>

           </div>

           <div class="panel-footer">
            <div class="row">
              <div class="col-sm-3 col-sm-offset-1">
                <input type="submit" class="btn-primary btn font-6" value="განახლება">
              </div>
            </div>
          </div>

       </form>
    </div>

  <div class="tab-pane" id="images">

    <div class="panel panel-default">
      <div class="panel-heading">
        <h2 class="font-6"> მთავარი სურათი </h2>
      </div>
      <div class="panel-body">
        <div class="tab-content">
          <div class="row mb50">
            <div class="col-sm-4">
              <a href="/processors/{{ $product -> id }}" target="_blank">
               <img id="main-image" src="/images/processors/main/original/{{ $product -> mainImage }}" class="img-responsive img-thumbnail wpr-100">
               <span id="main-image-link-icon">
                 <i class="fas fa-link"></i>
               </span>
              </a>
            </div>

            <div class="col-sm-8">
              <form method="POST" action="{{ route('cpuImageUpdate') }}" class="dropzone" id="main-image-dropzone" enctype="multipart/form-data">
                <div class="fallback">
                 <input type="file" name="mainImage">
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="panel panel-default">
      <div class="panel-heading">
        <h2 class="font-6"> სურათები </h2>
      </div>
      <div class="panel-body">

      <div class="tab-content">

        <div class="row">
         <div class="col-md-12">
             <div class="owl-carousel owl-theme">
               @foreach($images as $image)
                <div class="item">
                  <a href="/images/processors/slides/original/{{ $image -> image }}" data-fancybox-group="button" class="fancy fancybox-buttons zoom-button">
                    <i class="fas fa-expand"></i>
                  </a>
                  <a href="{{ route('cpuImgDestroy', ['id' => $image -> id]) }}" class="image-delete-button">
                    <i class="fas fa-trash-alt"></i>
                  </a>
                  <img src="/images/processors/slides/preview/{{ $image -> image }}">
                </div>
               @endforeach
             </div>
         </div>
       </div>

        <div class="row mt40">
          <div class="col-md-12">
            <form method="POST" action="{{ route('cpuImageUpload') }}" class="dropzone" enctype="multipart/form-data" id="images-dropzone">
            </form>
          </div>
        </div>

      </div> <!--- inline tab content end--->

    </div> <!--- inline panel body end --->

   </div> <!--- inline panel end --->

  </div>

 </div>

 </div>

</div>

@include('parts.controlPanel.general')

@include('parts.controlPanel.plugins.tinymce')

@include('parts.controlPanel.plugins.carousel')

@include('parts.controlPanel.plugins.fancybox')

@include('parts.controlPanel.plugins.dropzone')

@include('parts.controlPanel.editPage')

<script type="text/javascript">

// socket change event handling

$("[name=socketId]").on("change", function(e) {

     let chipsetsIdentifiers = $(this).find(":selected").data("chipsets-identifiers").toString().split("|");
     let chipsetsTitles = $(this).find(":selected").data("chipsets-titles").toString().split("|");
     let numOfElements = chipsetsTitles.length;

     $("#chipsets-select").val(null).trigger("change");

     $("#chipsets-select").find("option").remove();

     for(let i = 0; i < numOfElements; i++)
     {
       let data = {
           id: chipsetsIdentifiers[i],
           text: chipsetsTitles[i]
         };

       let newOption = new Option(data.text, data.id, true, true);

       $("#chipsets-select").append(newOption).trigger("change");
     }

     chipsetsInit();
});

// initialize multiple select

$("#chipsets-select").select2();

// initialize chipsets

function chipsetsInit()
{
  let options = document.querySelector("#chipsets-select").options;
  let chipsetsInput = document.querySelector("#chipsets");
  let chipsets = [];

  for(option of options) chipsets.push(option.value);

  chipsetsInput.value = chipsets.join(",");
}

// handle multi select change event

$("#chipsets-select").on("change", function(e) {

    let data = $(this).select2("data");
    let chipsetsInput = document.querySelector("#chipsets");
    let values = [];

    for(object of data) values.push(object["id"]);

    chipsetsInput.value = values.join(",");
});

</script>
