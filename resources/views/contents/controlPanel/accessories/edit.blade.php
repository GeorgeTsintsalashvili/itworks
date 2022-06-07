
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

      <form class="form-horizontal record-update-form" method="POST" action="{{ route('accUpdate') }}">

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
               <div class="input-group">
                <input type="text" name="quantity" class="form-control font-7" value="{{ $product -> quantity }}">
              </div>
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

              <label class="col-sm-1 control-label font-6">ტიპი</label>
              <div class="col-sm-2">

               <select name="accessoryTypeId" class="edit-page-list wpr-100">
                @foreach($types as $value)
                  @if($value -> id == $product -> accessoryTypeId)
                    <option selected class="font-6" value="{{ $value -> id }}">{{ $value -> typeTitle }}</option>
                  @else
                    <option class="font-6" value="{{ $value -> id }}">{{ $value -> typeTitle }}</option>
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
              <a href="/accessories/{{ $product -> id }}" target="_blank">
               <img id="main-image" src="/images/accessories/main/original/{{ $product -> mainImage }}" class="img-responsive img-thumbnail wpr-100">
               <span id="main-image-link-icon">
                 <i class="fas fa-link"></i>
               </span>
              </a>
            </div>

            <div class="col-sm-8">
              <form method="POST" action="{{ route('accImageUpdate') }}" class="dropzone" id="main-image-dropzone" enctype="multipart/form-data">
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
                  <a href="/images/accessories/slides/original/{{ $image -> image }}" data-fancybox-group="button" class="fancy fancybox-buttons zoom-button">
                    <i class="fas fa-expand"></i>
                  </a>
                  <a href="{{ route('accImgDestroy', ['id' => $image -> id]) }}" class="image-delete-button">
                    <i class="fas fa-trash-alt"></i>
                  </a>
                  <img src="/images/accessories/slides/preview/{{ $image -> image }}">
                </div>
               @endforeach
             </div>
         </div>
       </div>

        <div class="row mt40">
          <div class="col-md-12">
            <form method="POST" action="{{ route('accImageUpload') }}" class="dropzone" enctype="multipart/form-data" id="images-dropzone">
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
