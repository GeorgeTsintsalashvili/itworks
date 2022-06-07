

  <div class="row">

  <!--- statements --->

  <div class="col-xs-12">
  	<div class="panel panel-default">
  		<div class="panel-heading">
  			<h4 class="font-6">განცხადებები</h4>
  		</div>
  		<div class="panel-body">
  			<table class="table table-hover mb1" data-update-route="{{ route('statementUpdate') }}">
  				<thead>
  					<tr class="font-6">
              <th>აიდები</th>
              <th>განრიგი</th>
              <th>კატეგორია</th>
              <th>განახლდა</th>
              <th>სტატუსი</th>
              <th class="font-6">S-VIP</th>
  						<th>წაშლა</th>
  					</tr>
  				</thead>
  				<tbody>

  				@foreach($statements as $value)

  					<tr data-record-id="{{ $value -> id }}">

  						<!--- identifiers field --->
  						<td class="col-sm-4">
  							<input data-parameter-name="identifiers" type="text" value="{{ $value -> identifiers }}" class="tokenfield no-border transparent-bg-color font-7 field">
  						</td>

              <!--- status update schedule field ---->
              <td class="col-sm-2">
                <input data-parameter-name="updateSchedule" type="text" value="{{ $value -> updateSchedule }}" class="no-border transparent-bg-color font-7 field">
              </td>

              <!--- statements categories list --->
              <td class="col-sm-2">
                <select data-parameter-name="categoryId" class="wpr-100 record-update-data-list field">

                  @foreach($categories as $category)
                    <option class="font-6" {{ $category -> id == $value -> categoryId ? "selected" : null }} value="{{ $category -> id }}">{{ $category -> categoryTitle }}</option>
                  @endforeach

                </select>
              </td>

              <!--- last update field ------>
              <td class="col-sm-2">
                <b class="last-update"> {{ $value -> updateTime }} </b>
              </td>

              <!--- activity status field ---->
              <td>
                <input data-parameter-name="updateEnabled" class="record-update-switch field" data-size="mini" type="checkbox" {{ $value -> updateEnabled ? "checked" : null }} value="{{ $value -> updateEnabled }}">
              </td>

              <!--- super vip field ---->
              <td>
                <input data-parameter-name="superVip" class="record-update-switch field" data-size="mini" type="checkbox" {{ $value -> superVip ? "checked" : null }} value="{{ $value -> superVip }}">
              </td>

  						<!--- delete statements button ----->
  						<td class="text-center">
  							<a class="delete-record" href="{{ route('statementDestroy', [ 'id' => $value -> id ]) }}">
  								<i style="font-size: 16px" class="fa fa-trash" aria-hidden="true"></i>
  							</a>
  						</td>
  					</tr>

  				@endforeach

  			 </tbody>
  			</table>

        <div data-page-key="{{ $statementsPageKey }}" data-current-page="{{ $statementsPaginator -> currentPage }}" class="clearfix pagination-column col-sm-12">

          <select class="control-data-list col-sm-2 p-n mr20" data-control-parameter-name="{{ $categoriesKey }}">
            <option class="font-6 control-field" value="0">ყველა კატეგორია</option>
             @foreach($categories as $category)
               <option value="{{ $category -> id }}" {{ $selectedCategoryId == $category -> id ? "selected" : null }} class="font-6"> {{ $category -> categoryTitle }} </option>
             @endforeach
          </select>

          <ul class="pagination mt0 mb20 col-sm-8">

              @if($statementsPaginator -> currentPage > 1)

               <li>
                <a href="{{ $statementsPaginator -> currentPage - 1 }}" class="page-switch">
                  <i class="fa fa-angle-double-left" aria-hidden="true"></i>
                </a>
               </li>

              @endif

              @foreach($statementsPaginator -> pages as $page)

              @if($page['isPad'])

              <li>
                <span>{{ $page['value'] }}</span>
              </li>

              @elseif($page['isActive'])

              <li class="active">
                <span>{{ $page['value'] }}</span>
              </li>

              @else

              <li>
               <a href="{{ $page -> value }}" class="page-switch">{{ $page['value'] }}</a>
              </li>

              @endif

              @endforeach

              @if($statementsPaginator -> currentPage < $statementsPaginator -> maxPage)

              <li>
                <a href="{{ $statementsPaginator -> currentPage + 1 }}" class="page-switch">
                  <i class="fa fa-angle-double-right" aria-hidden="true"></i>
                </a>
              </li>

              @endif

           </ul>

        </div>

  		 <form action="{{ route('statementStore') }}" method="POST" class="form-horizontal row-border data-form" enctype="multipart/form-data">
  			<div class="form-group">

  				<div class="col-sm-4">
  					<input name="identifiers" type="text" class="form-control font-7 tokenfield" placeholder="აიდები">
  				</div>

          <div class="col-sm-3">
            <input name="updateSchedule" type="text" class="form-control font-7" placeholder="განრიგი">
          </div>

          <div class="col-sm-2">
          <select class="form-select select2-offscreen" name="categoryId" style="width: 100%">
            <option class="font-6" value="0" selected>კატეგორია</option>

            @foreach($categories as $category)
             <option class="font-6" value="{{ $category -> id }}">{{ $category -> categoryTitle }}</option>
            @endforeach

          </select>
          </div>

          <div class="col-sm-1">
            <input name="updateEnabled" class="form-switch" data-size="medium" type="checkbox" data-off-color="default" data-on-text="I" data-off-text="O" checked value="1">
          </div>

          <div class="col-sm-1">
            <input name="superVip" class="form-switch" data-size="medium" type="checkbox" data-off-color="default" data-on-text="I" data-off-text="O" checked value="1">
          </div>

  			</div>
        <div class="panel-footer">
          <div class="row">
            <div class="col-sm-3 col-sm-offset-0">
              <input type="submit" class="btn-primary btn font-6" value="დამატება">
            </div>
          </div>
        </div>
  		</form>

  		</div>
  	</div>
  </div>

</div>

<!--- categories --->

<div class="row">
  <div class="col-xs-6">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4 class="font-6">კატეგორიები</h4>
      </div>
      <div class="panel-body">
        <table class="table table-hover mb1" data-update-route="{{ route('categoryUpdate') }}">
          <thead>
            <tr class="font-6">
              <th>დასახელება</th>
              <th>იდენტიფიკატორი</th>
              <th>წაშლა</th>
            </tr>
          </thead>
          <tbody>

          @foreach($categories as $value)

            <tr data-record-id="{{ $value -> id }}">

              <!--- title field --->
              <td>
                <input data-parameter-name="categoryTitle" type="text" value="{{ $value -> categoryTitle }}" class="no-border transparent-bg-color font-7 field">
              </td>

              <!--- identifier field ---->
              <td>
                <input data-parameter-name="parameterValue" type="text" value="{{ $value -> parameterValue }}" class="no-border transparent-bg-color font-7 field">
              </td>

              <!--- delete button ---->
              <td>
                <a class="delete-record" href="{{ route('categoryDestroy', [ 'id' => $value -> id ]) }}">
                  <i style="font-size: 16px" class="fa fa-trash" aria-hidden="true"></i>
                </a>
              </td>

            </tr>

          @endforeach

         </tbody>
        </table>

       <form action="{{ route('categoryStore') }}" method="POST" class="form-horizontal row-border data-form" enctype="multipart/form-data">
        <div class="form-group">
          <div class="col-sm-2">
            <input type="submit" class="btn-primary btn font-6" value="დამატება">
          </div>
          <div class="col-sm-5">
            <input name="categoryTitle" type="text" class="form-control font-7" placeholder="დასახელება">
          </div>
          <div class="col-sm-5">
            <input name="parameterValue" type="text" class="form-control font-7" placeholder="იდენტიფიკატორი">
          </div>
        </div>
       </form>

      </div>
    </div>
  </div>

    <div class="col-xs-6">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4 class="font-6">სეანსის ქუქი მონაცემები</h4>
      </div>
      <div class="panel-body">

       <form action="{{ route('sessionCookieUpdate') }}" method="POST" class="form-horizontal row-border data-form" enctype="multipart/form-data">
        <div class="form-group">
					<div class="col-sm-12">
					 <textarea name="sessionText" rows="14" class="form-control font-7" style="resize: vertical">{{ $statementsData -> sessionText }}</textarea>
					</div>
        </div>
        <div class="form-group">
          <div class="col-sm-2">
            <input type="submit" class="btn-primary btn font-6" value="განახლება">
          </div>
        </div>
      </form>

      </div>
    </div>
  </div>

</div>

@include('parts.controlPanel.tokenfield')

@include('parts.controlPanel.parameters')

@include('parts.controlPanel.switches')

@include('parts.controlPanel.lists')
