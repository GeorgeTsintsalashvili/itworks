
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

      <form class="form-horizontal record-update-form" method="POST" action="{{ route('vcUpdate') }}">

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

             <label class="col-sm-1 control-label font-6">რაოდენობა</label>
             <div class="col-sm-2">
                <input type="text" name="quantity" class="form-control font-7" value="{{ $product -> quantity }}">
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

             <label class="col-sm-1 control-label font-6">მეხსიერება</label>
             <div class="col-sm-2">
                <input type="text" name="memory" class="form-control font-7" value="{{ $product -> memory }}">
             </div>

             <label class="col-sm-1 control-label font-6">ინტერფეისი</label>
             <div class="col-sm-2">
                <input type="text" name="memoryBandwidth" class="form-control font-7" value="{{ $product -> memoryBandwidth }}">
             </div>

             <label class="col-sm-1 control-label font-6">კვება</label>
             <div class="col-sm-2">
                <input type="text" name="minPower" class="form-control font-7" value="{{ $product -> minPower }}">
             </div>

           </div>

           <div class="form-group">

            <label class="col-sm-1 control-label font-6">GPU</label>
            <div class="col-sm-2">

             <select name="gpuManufacturerId" class="edit-page-list wpr-100">
              @foreach($gpuManufacturers as $value)
                @if($value -> id == $product -> gpuManufacturerId)
                  <option selected class="font-6" value="{{ $value -> id }}">{{ $value -> gpuTitle }}</option>
                @else
                  <option class="font-6" value="{{ $value -> id }}">{{ $value -> gpuTitle }}</option>
                @endif
              @endforeach
            </select>
           </div>

           <label class="col-sm-1 control-label font-6">VRAM Type</label>
           <div class="col-sm-2">

            <select name="memoryTypeId" class="edit-page-list wpr-100">
             @foreach($memoryTypes as $value)
               @if($value -> id == $product -> memoryTypeId)
                 <option selected class="font-6" value="{{ $value -> id }}">{{ $value -> typeTitle }}</option>
               @else
                 <option class="font-6" value="{{ $value -> id }}">{{ $value -> typeTitle }}</option>
               @endif
             @endforeach
           </select>
          </div>

          <label class="col-sm-1 control-label font-6">ბრენდი</label>
          <div class="col-sm-2">

           <select name="videoCardManufacturerId" class="edit-page-list wpr-100">
            @foreach($videoCardsManufacturers as $value)
              @if($value -> id == $product -> videoCardManufacturerId)
                <option selected class="font-6" value="{{ $value -> id }}">{{ $value -> videoCardManufacturerTitle }}</option>
              @else
                <option class="font-6" value="{{ $value -> id }}">{{ $value -> videoCardManufacturerTitle }}</option>
              @endif
            @endforeach
          </select>
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
              <a href="/videoCards/{{ $product -> id }}" target="_blank">
               <img id="main-image" src="/images/videoCards/main/original/{{ $product -> mainImage }}" class="img-responsive img-thumbnail wpr-100">
               <span id="main-image-link-icon">
                 <i class="fas fa-link"></i>
               </span>
              </a>
            </div>

            <div class="col-sm-8">
              <form method="POST" action="{{ route('vcImageUpdate') }}" class="dropzone" id="main-image-dropzone" enctype="multipart/form-data">
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
                  <a href="/images/videoCards/slides/original/{{ $image -> image }}" data-fancybox-group="button" class="fancy fancybox-buttons zoom-button">
                    <i class="fas fa-expand"></i>
                  </a>
                  <a href="{{ route('vcImgDestroy', ['id' => $image -> id]) }}" class="image-delete-button">
                    <i class="fas fa-trash-alt"></i>
                  </a>
                  <img src="/images/videoCards/slides/preview/{{ $image -> image }}">
                </div>
               @endforeach
             </div>
         </div>
       </div>

        <div class="row mt40">
          <div class="col-md-12">
            <form method="POST" action="{{ route('vcImageUpload') }}" class="dropzone" enctype="multipart/form-data" id="images-dropzone">
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
