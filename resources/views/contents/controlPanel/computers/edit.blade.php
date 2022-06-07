
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

      <form class="form-horizontal row-border record-update-form" method="POST" action="{{ route('sbUpdate') }}">

          <div class="form-group">
            <label class="col-sm-1 control-label font-6">აღწერა</label>
            <input type="hidden" name="record-id" value="{{ $product -> id}}">
            <div class="col-sm-11">
             <textarea id="parts-editor" class="kt-tinymce" name="description">{!! $product -> description !!}</textarea>
            </div>
          </div>

          <div id="system-full-description-container" style="display: none">
            <div id="header-template">{!! $computerParts -> header_template !!}</div>
            <div id="description-body"></div>
            <div id="footer-template">{!! $computerParts -> footer_template !!}</div>
            <input type="hidden" name="header-template" class="header-template" value="{{ $computerParts -> header_template }}">
            <input type="hidden" name="footer-template" class="footer-template" value="{{ $computerParts -> footer_template }}">
          </div>

          <div class="hidden-header-templates">
           @foreach($headerTemplates as $template)
           <div class="hidden-template" id="hidden-header-template-{{ $template -> id }}" style="display: none">{!! $template -> description !!}</div>
           @endforeach
          </div>

          <div class="hidden-footer-templates">
           @foreach($footerTemplates as $template)
           <div class="hidden-template" id="hidden-footer-template-{{ $template -> id }}" style="display: none">{!! $template -> description !!}</div>
           @endforeach
          </div>

          <div class="form-group">
            <label class="col-sm-1 control-label font-6">შაბლონები</label>
            <div class="col-sm-5">
               <select class="edit-page-list wpr-100" id="header-template-select">
                 <option class="font-6" value="0">ზედა შაბლონი არის ცარიელი</option>
                 @foreach($headerTemplates as $template)
                 <option class="font-6" value="{{ $template -> id }}">{{ $template -> title }}</option>
                 @endforeach
               </select>
            </div>
            <div class="col-sm-5 col-sm-offset-1">
               <select class="edit-page-list wpr-100" id="footer-template-select">
                 <option class="font-6" value="0">ქვედა შაბლონი არის ცარიელი</option>
                 @foreach($footerTemplates as $template)
                 <option class="font-6" value="{{ $template -> id }}">{{ $template -> title }}</option>
                 @endforeach
               </select>
            </div>
          </div>

          <div class="form-group part-details-block" id="part-processor" data-title-prefix="CPU">
           <label class="col-sm-1 control-label font-6">პროცესორი</label>
           <div class="col-sm-4">
            <input type="text" class="part-select-field part-title-field font-7" name="part-processor-title" value="{{ $computerParts -> processor_title }}" data-part-block="part-processor">
            <input type="hidden" name="part-processor-id" value="{{ $computerParts -> processor_id }}" class="system-part-id">
            <input type="hidden" name="part-processor-stock-type-id" value="{{ $computerParts -> processor_stock_type_id }}" class="system-part-stock-type-id">
           </div>
           <label class="col-sm-1 control-label font-6">ფასი</label>
           <div class="col-sm-2">
            <input type="text" class="part-select-field part-price-field font-7" name="part-processor-price" value="{{ $computerParts -> processor_price }}" data-part-block="part-processor">
           </div>
           <div class="col-sm-2">
            <input type="button" class="btn-primary btn font-6 part-select-button pull-right" value="ნაწილის არჩევა" data-part-block="part-processor" data-parts-address="{{ route('cpu') }}">
           </div>
           <div class="col-sm-2">
            <input type="button" class="btn-primary btn font-6 part-remove-button pull-right" value="მოშორება" data-part-block="part-processor">
           </div>
          </div>

          <div class="form-group part-details-block" id="part-motherboard" data-title-prefix="MoBo">
           <label class="col-sm-1 control-label font-6">დედაპლატა</label>
           <div class="col-sm-4">
            <input type="text" class="part-select-field part-title-field font-7" name="part-motherboard-title" value="{{ $computerParts -> motherboard_title }}" data-part-block="part-motherboard">
            <input type="hidden" name="part-motherboard-id" value="{{ $computerParts -> motherboard_id }}" class="system-part-id">
            <input type="hidden" name="part-motherboard-stock-type-id" value="{{ $computerParts -> motherboard_stock_type_id }}" class="system-part-stock-type-id">
           </div>
           <label class="col-sm-1 control-label font-6">ფასი</label>
           <div class="col-sm-2">
            <input type="text" class="part-select-field part-price-field font-7" name="part-motherboard-price" value="{{ $computerParts -> motherboard_price }}" data-part-block="part-motherboard">
           </div>
           <div class="col-sm-2">
            <input type="button" class="btn-primary btn font-6 part-select-button pull-right" value="ნაწილის არჩევა" data-part-block="part-motherboard"  data-parts-address="{{ route('mb') }}">
           </div>
           <div class="col-sm-2">
            <input type="button" class="btn-primary btn font-6 part-remove-button pull-right" value="მოშორება" data-part-block="part-motherboard">
           </div>
          </div>

          <div class="form-group part-details-block" id="part-ram" data-title-prefix="RAM">
           <label class="col-sm-1 control-label font-6">ოპერატიული</label>
           <div class="col-sm-4">
            <input type="text" class="part-select-field part-title-field font-7" name="part-ram-title" value="{{ $computerParts -> ram_title }}" data-part-block="part-ram">
            <input type="hidden" name="part-ram-id" value="{{ $computerParts -> ram_id }}" class="system-part-id">
            <input type="hidden" name="part-ram-stock-type-id" value="{{ $computerParts -> ram_stock_type_id }}" class="system-part-stock-type-id">
           </div>
           <label class="col-sm-1 control-label font-6">ფასი</label>
           <div class="col-sm-2">
            <input type="text" class="part-select-field part-price-field font-7" name="part-ram-price" value="{{ $computerParts -> ram_price }}" data-part-block="part-ram">
           </div>
           <div class="col-sm-2">
            <input type="button" class="btn-primary btn font-6 part-select-button pull-right" value="ნაწილის არჩევა" data-part-block="part-ram" data-parts-address="{{ route('mm') }}">
           </div>
           <div class="col-sm-2">
            <input type="button" class="btn-primary btn font-6 part-remove-button pull-right" value="მოშორება" data-part-block="part-ram">
           </div>
          </div>

          <div class="form-group part-details-block" id="part-videocard" data-title-prefix="GPU">
           <label class="col-sm-1 control-label font-6">გრაფიკა</label>
           <div class="col-sm-4">
            <input type="text" class="part-select-field part-title-field font-7" name="part-videocard-title" value="{{ $computerParts -> video_card_title }}" data-part-block="part-videocard">
            <input type="hidden" name="part-videocard-id" value="{{ $computerParts -> video_card_id }}" class="system-part-id">
            <input type="hidden" name="part-videocard-stock-type-id" value="{{ $computerParts -> video_card_stock_type_id }}" class="system-part-stock-type-id">
           </div>
           <label class="col-sm-1 control-label font-6">ფასი</label>
           <div class="col-sm-2">
            <input type="text" class="part-select-field part-price-field font-7" name="part-videocard-price" value="{{ $computerParts -> video_card_price }}" data-part-block="part-videocard">
           </div>
           <div class="col-sm-2">
            <input type="button" class="btn-primary btn font-6 part-select-button pull-right" value="ნაწილის არჩევა" data-part-block="part-videocard" data-parts-address="{{ route('vc') }}">
           </div>
           <div class="col-sm-2">
            <input type="button" class="btn-primary btn font-6 part-remove-button pull-right" value="მოშორება" data-part-block="part-videocard">
           </div>
          </div>

          <div class="form-group part-details-block" id="part-hdd" data-title-prefix="HDD">
           <label class="col-sm-1 control-label font-6">HDD</label>
           <div class="col-sm-4">
            <input type="text" class="part-select-field part-title-field font-7" name="part-hdd-title" value="{{ $computerParts -> hdd_title }}" data-part-block="part-hdd">
            <input type="hidden" name="part-hdd-id" value="{{ $computerParts -> hdd_id }}" class="system-part-id">
            <input type="hidden" name="part-hdd-stock-type-id" value="{{ $computerParts -> hdd_stock_type_id }}" class="system-part-stock-type-id">
           </div>
           <label class="col-sm-1 control-label font-6">ფასი</label>
           <div class="col-sm-2">
            <input type="text" class="part-select-field part-price-field font-7" name="part-hdd-price" value="{{ $computerParts -> hdd_price }}" data-part-block="part-hdd">
           </div>
           <div class="col-sm-2">
            <input type="button" class="btn-primary btn font-6 part-select-button pull-right" value="ნაწილის არჩევა" data-part-block="part-hdd" data-parts-address="{{ route('hdd') }}">
           </div>
           <div class="col-sm-2">
            <input type="button" class="btn-primary btn font-6 part-remove-button pull-right" value="მოშორება" data-part-block="part-hdd">
           </div>
          </div>

          <div class="form-group part-details-block" id="part-ssd" data-title-prefix="SSD">
           <label class="col-sm-1 control-label font-6">SSD</label>
           <div class="col-sm-4">
            <input type="text" class="part-select-field part-title-field font-7" name="part-ssd-title" value="{{ $computerParts -> ssd_title }}" data-part-block="part-ssd">
            <input type="hidden" name="part-ssd-id" value="{{ $computerParts -> ssd_id }}" class="system-part-id">
            <input type="hidden" name="part-ssd-stock-type-id" value="{{ $computerParts -> ssd_stock_type_id }}" class="system-part-stock-type-id">
           </div>
           <label class="col-sm-1 control-label font-6">ფასი</label>
           <div class="col-sm-2">
            <input type="text" class="part-select-field part-price-field font-7" name="part-ssd-price" value="{{ $computerParts -> ssd_price }}" data-part-block="part-ssd">
           </div>
           <div class="col-sm-2">
            <input type="button" class="btn-primary btn font-6 part-select-button pull-right" value="ნაწილის არჩევა" data-part-block="part-ssd" data-parts-address="{{ route('ssd') }}">
           </div>
           <div class="col-sm-2">
            <input type="button" class="btn-primary btn font-6 part-remove-button pull-right" value="მოშორება" data-part-block="part-ssd">
           </div>
          </div>

          <div class="form-group part-details-block" id="part-power-supply" data-title-prefix="PSU">
           <label class="col-sm-1 control-label font-6">კვება</label>
           <div class="col-sm-4">
            <input type="text" class="part-select-field part-title-field font-7" name="part-power-supply-title" value="{{ $computerParts -> power_supply_title }}" data-part-block="part-power-supply">
            <input type="hidden" name="part-power-supply-id" value="{{ $computerParts -> power_supply_id }}" class="system-part-id">
            <input type="hidden" name="part-power-supply-stock-type-id" value="{{ $computerParts -> power_supply_stock_type_id }}" class="system-part-stock-type-id">
           </div>
           <label class="col-sm-1 control-label font-6">ფასი</label>
           <div class="col-sm-2">
            <input type="text" class="part-select-field part-price-field font-7" name="part-power-supply-price" value="{{ $computerParts -> power_supply_price }}" data-part-block="part-power-supply">
           </div>
           <div class="col-sm-2">
            <input type="button" class="btn-primary btn font-6 part-select-button pull-right" value="ნაწილის არჩევა" data-part-block="part-power-supply" data-parts-address="{{ route('ps') }}">
           </div>
           <div class="col-sm-2">
            <input type="button" class="btn-primary btn font-6 part-remove-button pull-right" value="მოშორება" data-part-block="part-power-supply">
           </div>
          </div>

          <div class="form-group part-details-block" id="part-case" data-title-prefix="Case">
           <label class="col-sm-1 control-label font-6">კეისი</label>
           <div class="col-sm-4">
            <input type="text" class="part-select-field part-title-field font-7" name="part-case-title" value="{{ $computerParts -> case_title }}" data-part-block="part-case">
            <input type="hidden" name="part-case-id" value="{{ $computerParts -> case_id }}" class="system-part-id">
            <input type="hidden" name="part-case-stock-type-id" value="{{ $computerParts -> case_stock_type_id }}" class="system-part-stock-type-id">
           </div>
           <label class="col-sm-1 control-label font-6">ფასი</label>
           <div class="col-sm-2">
            <input type="text" class="part-select-field part-price-field font-7" name="part-case-price" value="{{ $computerParts -> case_price }}" data-part-block="part-case">
           </div>
           <div class="col-sm-2">
            <input type="button" class="btn-primary btn font-6 part-select-button pull-right" value="ნაწილის არჩევა" data-part-block="part-case" data-parts-address="{{ route('cases') }}">
           </div>
           <div class="col-sm-2">
            <input type="button" class="btn-primary btn font-6 part-remove-button pull-right" value="მოშორება" data-part-block="part-case">
           </div>
          </div>

          <div class="form-group part-details-block" id="part-cooler" data-title-prefix="FAN">
           <label class="col-sm-1 control-label font-6">ქულერი</label>
           <div class="col-sm-4">
            <input type="text" class="part-select-field part-title-field font-7" name="part-cooler-title" value="{{ $computerParts -> cooler_title }}" data-part-block="part-cooler">
            <input type="hidden" name="part-cooler-id" value="{{ $computerParts -> cooler_id }}" class="system-part-id">
            <input type="hidden" name="part-cooler-stock-type-id" value="{{ $computerParts -> cooler_stock_type_id }}" class="system-part-stock-type-id">
           </div>
           <label class="col-sm-1 control-label font-6">ფასი</label>
           <div class="col-sm-2">
            <input type="text" class="part-select-field part-price-field font-7" name="part-cooler-price" value="{{ $computerParts -> cooler_price }}" data-part-block="part-cooler">
           </div>
           <div class="col-sm-2">
            <input type="button" class="btn-primary btn font-6 part-select-button pull-right" value="ნაწილის არჩევა" data-part-block="part-cooler" data-parts-address="{{ route('pc') }}">
           </div>
           <div class="col-sm-2">
            <input type="button" class="btn-primary btn font-6 part-remove-button pull-right" value="მოშორება" data-part-block="part-cooler">
           </div>
          </div>

          @if($product -> affected)
          <div class="form-group">
           <div class="col-sm-12">
            <div class="alert alert-dismissable alert-warning">
              <!--- processor warning start --->
              @if($computerParts -> processor_price_affected)
              <p class="font-7">
               <i class="fa fa-exclamation-triangle"></i>
               <span style="font-size: 14px">პროცესორის ფასი <b>{{ $computerParts -> processor_old_price }}</b> ლარი შეიცვალა <b>{{ $computerParts -> processor_price }}</b> ლარით </span>
              </p>
              @endif
              @if($computerParts -> processor_stock_type_id_affected)
              <p class="font-7">
               <i class="fa fa-exclamation-triangle"></i>
               <span style="font-size: 14px">პროცესორის საწყობის ტიპი <b>"{{ $stockKeyValueParts[$computerParts -> processor_old_stock_type_id]['title'] }}"</b> შეიცვალა საწყობის ტიპით <b>"{{ $stockKeyValueParts[$computerParts -> processor_stock_type_id]['title'] }}"</b></span>
              </p>
              @endif
              @if($computerParts -> processor_unlinked)
              <p class="font-7">
               <i class="fa fa-exclamation-triangle"></i>
               <span style="font-size: 14px">პროცესორთან გაწყდა კავშირი პროდუქციის წაშლის გამო</span>
              </p>
              @endif
              @if($computerParts -> processor_visibility_affected)
              <p class="font-7">
               <i class="fa fa-exclamation-triangle"></i>
               <span style="font-size: 14px">პროცესორის ხილულობა <?=$computerParts -> processor_visibility ? "ჩაირთო" : "გაითიშა"; ?></span>
              </p>
              @endif
              <!--- processor warning end --->

              <!--- motherboard warning start --->
              @if($computerParts -> motherboard_price_affected)
              <p class="font-7">
               <i class="fa fa-exclamation-triangle"></i>
               <span style="font-size: 14px">დედაპლატის ფასი <b>{{ $computerParts -> motherboard_old_price }}</b> ლარი შეიცვალა <b>{{ $computerParts -> motherboard_price }}</b> ლარით </span>
              </p>
              @endif
              @if($computerParts -> motherboard_stock_type_id_affected)
              <p class="font-7">
               <i class="fa fa-exclamation-triangle"></i>
               <span style="font-size: 14px">დედაპლატის საწყობის ტიპი <b>"{{ $stockKeyValueParts[$computerParts -> motherboard_old_stock_type_id]['title'] }}"</b> შეიცვალა საწყობის ტიპით <b>"{{ $stockKeyValueParts[$computerParts -> motherboard_stock_type_id]['title'] }}"</b></span>
              </p>
              @endif
              @if($computerParts -> motherboard_unlinked)
              <p class="font-7">
               <i class="fa fa-exclamation-triangle"></i>
               <span style="font-size: 14px">დედაპლატასთან გაწყდა კავშირი პროდუქციის წაშლის გამო</span>
              </p>
              @endif
              @if($computerParts -> motherboard_visibility_affected)
              <p class="font-7">
               <i class="fa fa-exclamation-triangle"></i>
               <span style="font-size: 14px">დედაპლატის ხილულობა <?=$computerParts -> motherboard_visibility ? "ჩაირთო" : "გაითიშა"; ?></span>
              </p>
              @endif
              <!--- motherboard warning end --->

              <!--- ram warning start --->
              @if($computerParts -> ram_price_affected)
              <p class="font-7">
               <i class="fa fa-exclamation-triangle"></i>
               <span style="font-size: 14px">ოპერატიულის ფასი <b>{{ $computerParts -> ram_old_price }}</b> ლარი შეიცვალა <b>{{ $computerParts -> ram_price }}</b> ლარით </span>
              </p>
              @endif
              @if($computerParts -> ram_stock_type_id_affected)
              <p class="font-7">
               <i class="fa fa-exclamation-triangle"></i>
               <span style="font-size: 14px">ოპერატიულის საწყობის ტიპი <b>"{{ $stockKeyValueParts[$computerParts -> ram_old_stock_type_id]['title'] }}"</b> შეიცვალა საწყობის ტიპით <b>"{{ $stockKeyValueParts[$computerParts -> ram_stock_type_id]['title'] }}"</b></span>
              </p>
              @endif
              @if($computerParts -> ram_unlinked)
              <p class="font-7">
               <i class="fa fa-exclamation-triangle"></i>
               <span style="font-size: 14px">ოპერატიულთან გაწყდა კავშირი პროდუქციის წაშლის გამო</span>
              </p>
              @endif
              @if($computerParts -> ram_visibility_affected)
              <p class="font-7">
               <i class="fa fa-exclamation-triangle"></i>
               <span style="font-size: 14px">ოპერატიულის ხილულობა <?=$computerParts -> ram_visibility ? "ჩაირთო" : "გაითიშა"; ?></span>
              </p>
              @endif
              <!--- ram warning end --->

              <!--- video_card warning start --->
              @if($computerParts -> video_card_price_affected)
              <p class="font-7">
               <i class="fa fa-exclamation-triangle"></i>
               <span style="font-size: 14px">ვიდეობარათის ფასი <b>{{ $computerParts -> video_card_old_price }}</b> ლარი შეიცვალა <b>{{ $computerParts -> video_card_price }}</b> ლარით </span>
              </p>
              @endif
              @if($computerParts -> video_card_stock_type_id_affected)
              <p class="font-7">
               <i class="fa fa-exclamation-triangle"></i>
               <span style="font-size: 14px">ვიდეობარათის საწყობის ტიპი <b>"{{ $stockKeyValueParts[$computerParts -> video_card_old_stock_type_id]['title'] }}"</b> შეიცვალა საწყობის ტიპით <b>"{{ $stockKeyValueParts[$computerParts -> video_card_stock_type_id]['title'] }}"</b></span>
              </p>
              @endif
              @if($computerParts -> video_card_unlinked)
              <p class="font-7">
               <i class="fa fa-exclamation-triangle"></i>
               <span style="font-size: 14px">ვიდეობარათთან გაწყდა კავშირი პროდუქციის წაშლის გამო</span>
              </p>
              @endif
              @if($computerParts -> video_card_visibility_affected)
              <p class="font-7">
               <i class="fa fa-exclamation-triangle"></i>
               <span style="font-size: 14px">ვიდეო ბარათის ხილულობა <?=$computerParts -> video_card_visibility ? "ჩაირთო" : "გაითიშა"; ?></span>
              </p>
              @endif
              <!--- video_card warning end --->

              <!--- hdd warning start --->
              @if($computerParts -> hdd_price_affected)
              <p class="font-7">
               <i class="fa fa-exclamation-triangle"></i>
               <span style="font-size: 14px">HDD მეხსიერების ფასი <b>{{ $computerParts -> hdd_old_price }}</b> ლარი შეიცვალა <b>{{ $computerParts -> hdd_price }}</b> ლარით </span>
              </p>
              @endif
              @if($computerParts -> hdd_stock_type_id_affected)
              <p class="font-7">
               <i class="fa fa-exclamation-triangle"></i>
               <span style="font-size: 14px">HDD მეხსიერების საწყობის ტიპი <b>"{{ $stockKeyValueParts[$computerParts -> hdd_old_stock_type_id]['title'] }}"</b> შეიცვალა საწყობის ტიპით <b>"{{ $stockKeyValueParts[$computerParts -> hdd_stock_type_id]['title'] }}"</b></span>
              </p>
              @endif
              @if($computerParts -> hdd_unlinked)
              <p class="font-7">
               <i class="fa fa-exclamation-triangle"></i>
               <span style="font-size: 14px">HDD მეხსიერებასთან გაწყდა კავშირი პროდუქციის წაშლის გამო</span>
              </p>
              @endif
              @if($computerParts -> hdd_visibility_affected)
              <p class="font-7">
               <i class="fa fa-exclamation-triangle"></i>
               <span style="font-size: 14px">HDD მეხსიერების ხილულობა <?=$computerParts -> hdd_visibility ? "ჩაირთო" : "გაითიშა"; ?></span>
              </p>
              @endif
              <!--- hdd warning end --->

              <!--- ssd warning start --->
              @if($computerParts -> ssd_price_affected)
              <p class="font-7">
               <i class="fa fa-exclamation-triangle"></i>
               <span style="font-size: 14px">SSD მეხსიერების ფასი <b>{{ $computerParts -> ssd_old_price }}</b> ლარი შეიცვალა <b>{{ $computerParts -> ssd_price }}</b> ლარით </span>
              </p>
              @endif
              @if($computerParts -> ssd_stock_type_id_affected)
              <p class="font-7">
               <i class="fa fa-exclamation-triangle"></i>
               <span style="font-size: 14px">SSD მეხსიერების საწყობის ტიპი <b>"{{ $stockKeyValueParts[$computerParts -> ssd_old_stock_type_id]['title'] }}"</b> შეიცვალა საწყობის ტიპით <b>"{{ $stockKeyValueParts[$computerParts -> ssd_stock_type_id]['title'] }}"</b></span>
              </p>
              @endif
              @if($computerParts -> ssd_unlinked)
              <p class="font-7">
               <i class="fa fa-exclamation-triangle"></i>
               <span style="font-size: 14px">SSD მეხსიერებასთან გაწყდა კავშირი პროდუქციის წაშლის გამო</span>
              </p>
              @endif
              @if($computerParts -> ssd_visibility_affected)
              <p class="font-7">
               <i class="fa fa-exclamation-triangle"></i>
               <span style="font-size: 14px">SSD მეხსიერების ხილულობა <?=$computerParts -> ssd_visibility ? "ჩაირთო" : "გაითიშა"; ?></span>
              </p>
              @endif
              <!--- ssd warning end --->

              <!--- power_supply warning start --->
              @if($computerParts -> power_supply_price_affected)
              <p class="font-7">
               <i class="fa fa-exclamation-triangle"></i>
               <span style="font-size: 14px">კვების ბლოკის ფასი <b>{{ $computerParts -> power_supply_old_price }}</b> ლარი შეიცვალა <b>{{ $computerParts -> power_supply_price }}</b> ლარით </span>
              </p>
              @endif
              @if($computerParts -> power_supply_stock_type_id_affected)
              <p class="font-7">
               <i class="fa fa-exclamation-triangle"></i>
               <span style="font-size: 14px">კვების ბლოკის საწყობის ტიპი <b>"{{ $stockKeyValueParts[$computerParts -> power_supply_old_stock_type_id]['title'] }}"</b> შეიცვალა საწყობის ტიპით <b>"{{ $stockKeyValueParts[$computerParts -> power_supply_stock_type_id]['title'] }}"</b></span>
              </p>
              @endif
              @if($computerParts -> power_supply_unlinked)
              <p class="font-7">
               <i class="fa fa-exclamation-triangle"></i>
               <span style="font-size: 14px">კვების ბლოკთან გაწყდა კავშირი პროდუქციის წაშლის გამო</span>
              </p>
              @endif
              @if($computerParts -> power_supply_visibility_affected)
              <p class="font-7">
               <i class="fa fa-exclamation-triangle"></i>
               <span style="font-size: 14px">კვების ბლოკის ხილულობა <?=$computerParts -> power_supply_visibility ? "ჩაირთო" : "გაითიშა"; ?></span>
              </p>
              @endif
              <!--- power_supply warning end --->

              <!--- case warning start --->
              @if($computerParts -> case_price_affected)
              <p class="font-7">
               <i class="fa fa-exclamation-triangle"></i>
               <span style="font-size: 14px">კეისის ფასი <b>{{ $computerParts -> case_old_price }}</b> ლარი შეიცვალა <b>{{ $computerParts -> case_price }}</b> ლარით </span>
              </p>
              @endif
              @if($computerParts -> case_stock_type_id_affected)
              <p class="font-7">
               <i class="fa fa-exclamation-triangle"></i>
               <span style="font-size: 14px">კეისის საწყობის ტიპი <b>"{{ $stockKeyValueParts[$computerParts -> case_old_stock_type_id]['title'] }}"</b> შეიცვალა საწყობის ტიპით <b>"{{ $stockKeyValueParts[$computerParts -> case_stock_type_id]['title'] }}"</b></span>
              </p>
              @endif
              @if($computerParts -> case_unlinked)
              <p class="font-7">
               <i class="fa fa-exclamation-triangle"></i>
               <span style="font-size: 14px">კეისთან გაწყდა კავშირი პროდუქციის წაშლის გამო</span>
              </p>
              @endif
              @if($computerParts -> case_visibility_affected)
              <p class="font-7">
               <i class="fa fa-exclamation-triangle"></i>
               <span style="font-size: 14px">კეისის ხილულობა <?=$computerParts -> case_visibility ? "ჩაირთო" : "გაითიშა"; ?></span>
              </p>
              @endif
              <!--- case warning end --->

              <!--- cooler warning start --->
              @if($computerParts -> cooler_price_affected)
              <p class="font-7">
               <i class="fa fa-exclamation-triangle"></i>
               <span style="font-size: 14px">ქულერის ფასი <b>{{ $computerParts -> cooler_old_price }}</b> ლარი შეიცვალა <b>{{ $computerParts -> cooler_price }}</b> ლარით </span>
              </p>
              @endif
              @if($computerParts -> cooler_stock_type_id_affected)
              <p class="font-7">
               <i class="fa fa-exclamation-triangle"></i>
               <span style="font-size: 14px">ქულერის საწყობის ტიპი <b>"{{ $stockKeyValueParts[$computerParts -> cooler_old_stock_type_id]['title'] }}"</b> შეიცვალა საწყობის ტიპით <b>"{{ $stockKeyValueParts[$computerParts -> cooler_stock_type_id]['title'] }}"</b></span>
              </p>
              @endif
              @if($computerParts -> cooler_unlinked)
              <p class="font-7">
               <i class="fa fa-exclamation-triangle"></i>
               <span style="font-size: 14px">ქულერთან გაწყდა კავშირი პროდუქციის წაშლის გამო</span>
              </p>
              @endif
              @if($computerParts -> cooler_visibility_affected)
              <p class="font-7">
               <i class="fa fa-exclamation-triangle"></i>
               <span style="font-size: 14px">ქულერის ხილულობა <?=$computerParts -> cooler_visibility ? "ჩაირთო" : "გაითიშა"; ?></span>
              </p>
              @endif
              <!--- cooler warning end --->

            </div>
           </div>
          </div>
          @endif

          <div class="form-group" style="border-top: 2px solid #c3c2c2">
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
              <label class="col-sm-1 control-label font-6">სისტემა</label>
              <div class="col-sm-2">
               <select name="seriesId" class="edit-page-list wpr-100">
                 @foreach($series as $value)
                   @if($value -> id == $product -> seriesId)
                    <option selected class="font-6" value="{{ $value -> id }}">{{ $value -> seriesTitle }}</option>
                   @else
                    <option class="font-6" value="{{ $value -> id }}">{{ $value -> seriesTitle }}</option>
                   @endif
                 @endforeach
               </select>
              </div>

              <label class="col-sm-1 control-label font-6">გრაფიკა</label>
               <div class="col-sm-2">
                  <select name="computerGraphicsId" class="edit-page-list wpr-100">
                    @foreach($graphics as $value)
                      @if($value -> id == $product -> computerGraphicsId)
                       <option selected class="font-6" value="{{ $value -> id }}">{{ $value -> graphicsTitle }}</option>
                      @else
                        <option class="font-6" value="{{ $value -> id }}">{{ $value -> graphicsTitle }}</option>
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
              <label class="col-sm-1 control-label font-6">CPU</label>
              <div class="col-sm-2">
                <input type="text" class="form-control font-7" name="cpu" value="{{ $product -> cpu }}">
              </div>
              <label class="col-sm-1 control-label font-6">RAM</label>
              <div class="col-sm-2">
                <input type="text" class="form-control font-7" name="memory" value="{{ $product -> memory }}">
              </div>
              <label class="col-sm-1 control-label font-6">HDD</label>
              <div class="col-sm-2">
                <input type="text" class="form-control font-7" name="hardDiscDriveCapacity" value="{{ $product -> hardDiscDriveCapacity }}">
              </div>
              <label class="col-sm-1 control-label font-6">SSD</label>
              <div class="col-sm-2">
                <input type="text" class="form-control font-7" name="solidStateDriveCapacity" value="{{ $product -> solidStateDriveCapacity }}">
              </div>
           </div>

          <div class="form-group">
              <label class="col-sm-1 control-label font-6">GPU</label>
              <div class="col-sm-2">
                <input type="text" class="form-control font-7" name="gpuTitle" value="{{ $product -> gpuTitle }}">
              </div>
              <label class="col-sm-1 control-label font-6">VRAM</label>
              <div class="col-sm-2">
                <input type="text" class="form-control font-7" name="videoMemory" value="{{ $product -> videoMemory }}">
              </div>
              <label class="col-sm-1 control-label font-6">ფასი</label>
              <div class="col-sm-2">
                <input type="text" class="form-control font-7 system-price" name="price" value="{{ $product -> price }}">
              </div>
              <label class="col-sm-1 control-label font-6">დაკლება</label>
              <div class="col-sm-2">
                <input type="text" class="form-control font-7" name="discount" value="{{ $product -> discount }}">
              </div>
          </div>

          <div class="form-group">
            <label class="col-sm-1 control-label font-6">სათაური</label>
            <div class="col-sm-5">
              <input type="text" class="form-control font-7" name="title" value="{{ $product -> title }}">
            </div>
            <label class="col-sm-1 control-label font-6">რაოდენობა</label>
            <div class="col-sm-2">
              <input type="text" class="form-control font-7" name="quantity" value="{{ $product -> quantity }}">
            </div>
          </div>
           <div class="panel-footer">
            <div class="row">
              <div class="col-sm-2 col-sm-offset-1">
                <input type="submit" class="btn-primary btn font-6" value="სისტემის განახლება">
              </div>
              <div class="col-sm-2">
               <input type="button" data-copy-action="{{ route('copySystem') }}" data-copy-method="POST" class="btn-primary btn font-6" value="სისტემის დაკოპირება" id="system-copy-button">
              </div>
              <div class="col-sm-2">
                <input type="button" class="btn-primary btn font-6" value="აღწერის დაკოპირება" id="copy-to-clipboard-button">
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
              <a href="/computers/{{ $product -> id }}" target="_blank">
               <img id="main-image" src="/images/computers/main/original/{{ $product -> mainImage }}" class="img-responsive img-thumbnail wpr-100">
               <span id="main-image-link-icon">
                 <i class="fas fa-link"></i>
               </span>
              </a>
            </div>
            <div class="col-sm-8">
              <form method="POST" action="{{ route('sbImageUpdate') }}" class="dropzone" id="main-image-dropzone" enctype="multipart/form-data">
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
                  <a href="/images/computers/slides/original/{{ $image -> image }}" data-fancybox-group="button" class="fancy fancybox-buttons zoom-button">
                    <i class="fas fa-expand"></i>
                  </a>
                  <a href="{{ route('sbImgDestroy', ['id' => $image -> id]) }}" class="image-delete-button">
                    <i class="fas fa-trash-alt"></i>
                  </a>
                  <img src="/images/computers/slides/preview/{{ $image -> image }}">
                </div>
               @endforeach
             </div>
         </div>
       </div>
        <div class="row mt40">
          <div class="col-md-12">
            <form method="POST" action="{{ route('sbImageUpload') }}" class="dropzone" enctype="multipart/form-data" id="images-dropzone">
            </form>
          </div>
        </div>
      </div>
      <!--- inline tab content end--->
    </div>
    <!--- inline panel body end --->
   </div>
   <!--- inline panel end --->
  </div>
 </div>
 </div>
</div>

<input type="hidden" id="stock-check-str" value="{{ $stockCheckStr }}">

<div id="iframe-overlay" style="display: none">
 <span id="window-close-button">
 	<i class="fas fa-times"></i>
 </span>
 <iframe id="part-select-window"></iframe>
</div>

@include('parts.controlPanel.general')
@include('parts.controlPanel.plugins.tinymce')
@include('parts.controlPanel.plugins.carousel')
@include('parts.controlPanel.plugins.fancybox')
@include('parts.controlPanel.plugins.dropzone')
@include('parts.controlPanel.editPage')
@include('parts.controlPanel.computerDescriptionBuilder')

<script type="text/javascript">

let editorTextUpdateButton = document.getElementById("editor-text-update-button");
let systemCopyButton = document.getElementById("system-copy-button");
let systemCopyCreationInProcess = false;
let copyAddress = systemCopyButton.getAttribute("data-copy-action");
let copyMethod = systemCopyButton.getAttribute("data-copy-method");

if (editorTextUpdateButton)
{
  editorTextUpdateButton.addEventListener("click", () => {

    incrementPrice();
    updateEditorText();

  });
}

systemCopyButton.addEventListener("click", () => {

  let form = document.querySelector(".record-update-form");
  let options = new Object();
  let defaultErrorCallback = (xhr, status, error) => console.log(xhr.responseText);

  if (!systemCopyCreationInProcess)
  {
    systemCopyCreationInProcess = true;

    options["type"] = copyMethod;
    options["url"] = copyAddress;
    options["data"] = $(form).serialize();
    options["error"] = (xhr, status, error) => console.log(xhr.responseText);
    options["success"] = (data) => {

      printMessage(data["created"] ? "სისტემის ასლი შეიქმნა" : "სისტემის ასლი ვერ შეიქმნა");

      systemCopyCreationInProcess = false;

    };

    jQuery.ajax(options);
  }

});

</script>
