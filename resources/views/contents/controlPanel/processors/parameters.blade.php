
<!--- sockets --->

  <div class="row">
  	<div class="col-xs-6">
  		<div class="panel panel-default">
  			<div class="panel-heading">
  				<h4 class="font-6">სოკეტები</h4>
  			</div>
  			<div class="panel-body">
  				<table class="table table-hover mb1" data-update-route="{{ route('socketUpdate') }}">
  					<thead>
  						<tr class="font-6">
  							<th>დასახელება</th>
  							<th>კონფიგურატორი</th>
  							<th>წაშლა</th>
  						</tr>
  					</thead>
  					<tbody>

            @foreach($sockets as $value)

  						<tr data-record-id="{{ $value -> id }}">

                <!--- title field --->
  							<td>
  								<input data-parameter-name="socketTitle" type="text" value="{{ $value -> socketTitle }}" class="no-border transparent-bg-color font-7 field">
  							</td>

  							<!--- switch button --->
  							<td>
  								<input data-parameter-name="configuratorPart" data-size="mini" class="record-update-switch field" type="checkbox" {{ $value -> configuratorPart ? "checked" : null }} value="{{ $value -> configuratorPart }}">
  							</td>

                <!--- delete button --->
  							<td>
  								<a class="delete-record" href="{{ route('socketDestroy', [ 'id' => $value -> id ]) }}">
  									<i style="font-size: 16px" class="fa fa-trash" aria-hidden="true"></i>
  								</a>
  							</td>

  						</tr>
            @endforeach
  				 </tbody>
  				</table>

         <form action="{{ route('socketStore') }}" method="POST" class="form-horizontal row-border data-form" enctype="multipart/form-data">
   				<div class="form-group">
            <div class="col-sm-2">
              <input type="submit" class="btn-primary btn font-6" value="დამატება">
            </div>
   					<div class="col-sm-3">
   						<input name="socketTitle" type="text" class="form-control font-7" placeholder="დასახელება">
   					</div>
  					<div class="col-sm-2">
  						<input name="configuratorPart" class="form-switch" data-size="medium" type="checkbox" data-off-color="default" data-on-text="I" data-off-text="O" checked value="1">
  					</div>
          </div>
        </form>

  			</div>
  		</div>
    </div>

  <!--- chipsets --->

  <div class="col-xs-6">
  	<div class="panel panel-default">
  		<div class="panel-heading">
  			<h4 class="font-6">ჩიპსეტები</h4>
  		</div>
  		<div class="panel-body">
  			<table class="table table-hover mb1" data-update-route="{{ route('chipsetUpdate') }}">
  				<thead>
  					<tr class="font-6">
  						<th>დასახელება</th>
              <th>სოკეტი</th>
  						<th>წაშლა</th>
  					</tr>
  				</thead>
  				<tbody>

  				@foreach($chipsets as $value)

  					<tr data-record-id="{{ $value -> id }}">

  						<!--- title field ---->
  						<td>
  							<input data-parameter-name="chipsetTitle" type="text" value="{{ $value -> chipsetTitle }}" class="no-border transparent-bg-color font-7 field">
  						</td>

              <!--- socket field ---->
              <td>
								<select data-parameter-name="socketId" class="record-update-data-list field" style="width: 100%">
									@foreach($sockets as $socket)
										<option class="font-6" {{ $socket -> id == $value -> socketId ? "selected" : null }} value="{{ $socket -> id }}">{{ $socket -> socketTitle }}</option>
									@endforeach
								</select>
							</td>

  						<!--- delete button --->
  						<td style="text-align: center">
  							<a class="delete-record" href="{{ route('chipsetDestroy', ['id' => $value -> id ]) }}">
  								<i style="font-size: 16px" class="fa fa-trash" aria-hidden="true"></i>
  							</a>
  						</td>

  					</tr>

  				@endforeach

  			 </tbody>
  			</table>

        <div data-page-key="{{ $chipsetsPageKey }}" data-current-page="{{ $chipsetsPaginator -> currentPage }}" class="clearfix pagination-column col-sm-12">

          <select class="control-data-list col-sm-4 p-n mr20" data-control-parameter-name="{{ $socketsKey }}">
            <option class="font-6" value="0">ყველა სოკეტი</option>
             @foreach($sockets as $socket)
               <option value="{{ $socket -> id }}" {{ $selectedSocketId == $socket -> id ? "selected" : null }} class="font-6"> {{ $socket -> socketTitle }} </option>
             @endforeach
          </select>

          <ul class="pagination mt0 mb20 col-sm-7">

              @if($chipsetsPaginator -> currentPage > 1)

               <li>
                <a href="{{ $chipsetsPaginator -> currentPage - 1 }}" class="page-switch">
                  <i class="fa fa-angle-double-left" aria-hidden="true"></i>
                </a>
               </li>

              @endif

              @foreach($chipsetsPaginator -> pages as $page)

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
               <a href="{{ $page['value'] }}" class="page-switch">{{ $page['value'] }}</a>
              </li>

              @endif

              @endforeach

              @if($chipsetsPaginator -> currentPage < $chipsetsPaginator -> maxPage)

              <li>
                <a href="{{ $chipsetsPaginator -> currentPage + 1 }}" class="page-switch">
                  <i class="fa fa-angle-double-right" aria-hidden="true"></i>
                </a>
              </li>

              @endif

           </ul>

        </div>

  		 <form action="{{ route('chipsetStore') }}" method="POST" class="form-horizontal row-border data-form" enctype="multipart/form-data">
  			<div class="form-group">
  				<div class="col-sm-3">
  					<input type="submit" class="btn-primary btn font-6" value="დამატება" style="width: 100%">
  				</div>
  				<div class="col-sm-3">
  					<input name="chipsetTitle" type="text" class="form-control font-7" placeholder="დასახელება">
  				</div>
          <div class="col-sm-4">
          <select name="socketId" class="form-select" style="width: 100%">
            <option class="font-6" value="0" selected>აირჩიეთ სოკეტი</option>
             @foreach($sockets as $socket)
              <option class="font-6" value="{{ $socket -> id }}">{{ $socket -> socketTitle }}</option>
             @endforeach
          </select>
          </div>
  			</div>
  		</form>

  		</div>
  	</div>
  </div>

  </div>

<!--- systems --->

<div class="row">
  <div class="col-xs-6">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4 class="font-6">სისტემები</h4>
      </div>
      <div class="panel-body">
        <table class="table table-hover mb1" data-update-route="{{ route('systemUpdate') }}">
          <thead>
            <tr class="font-6">
              <th>სათაური სისტემებში</th>
              <th>სათაური პროცესორებში</th>
              <th>მთავარზე</th>
              <th>წაშლა</th>
            </tr>
          </thead>
          <tbody>

          @foreach($systems as $value)

            <tr data-record-id="{{ $value -> id }}">

              <!--- title field --->
              <td>
                <input data-parameter-name="homePageTitle" type="text" value="{{ $value -> homePageTitle }}" class="no-border transparent-bg-color font-7 field">
              </td>

              <!--- homepage title field --->
              <td>
                <input data-parameter-name="seriesTitle" type="text" value="{{ $value -> seriesTitle }}" class="no-border transparent-bg-color font-7 field">
              </td>

              <!--- visibility field --->
              <td>
                <input data-parameter-name="visibleOnHomePage" class="record-update-switch field" data-size="mini" type="checkbox" {{ $value -> visibleOnHomePage ? "checked" : null }} value="{{ $value -> visibleOnHomePage }}">
              </td>

              <!--- delete button --->
              <td>
                <a class="delete-record" href="{{ route('systemDestroy', [ 'id' => $value -> id ]) }}">
                  <i style="font-size: 16px" class="fa fa-trash" aria-hidden="true"></i>
                </a>
              </td>

            </tr>

          @endforeach

         </tbody>
        </table>

       <form action="{{ route('systemStore') }}" method="POST" class="form-horizontal row-border data-form" enctype="multipart/form-data">
        <div class="form-group">
          <div class="col-sm-2">
            <input type="submit" class="btn-primary btn font-6" value="დამატება">
          </div>
          <div class="col-sm-4">
            <input name="homePageTitle" type="text" class="form-control font-7" placeholder="სათაური სისტემებში">
          </div>
          <div class="col-sm-4">
            <input name="seriesTitle" type="text" class="form-control font-7" placeholder="სათაური პროცესორებში">
          </div>
          <div class="col-sm-2">
            <input class="form-switch" name="visibleOnHomePage" data-size="medium" type="checkbox" data-off-color="default" data-on-text="I" data-off-text="O" checked value="1">
          </div>
        </div>
      </form>

      </div>
    </div>
  </div>

  <!--- technology process --->

  <div class="col-xs-6">
  	<div class="panel panel-default">
  		<div class="panel-heading">
  			<h4 class="font-6">ტექნოლოგიური პროცესები</h4>
  		</div>
  		<div class="panel-body">
  			<table class="table table-hover mb1" data-update-route="{{ route('tcpUpdate') }}">
  				<thead>
  					<tr class="font-6">
  						<th>დასახელება</th>
  						<th>წაშლა</th>
  					</tr>
  				</thead>
  				<tbody>

  				@foreach($technologyProcesses as $value)

  					<tr data-record-id="{{ $value -> id }}">

  						<!--- title field --->
  						<td>
  							<input data-parameter-name="size" type="text" value="{{ $value -> size }}" class="no-border transparent-bg-color font-7 field">
  						</td>

  						<!--- delete button --->
  						<td>
  							<a class="delete-record" href="{{ route('tcpDestroy', [ 'id' => $value -> id ]) }}">
  								<i style="font-size: 16px" class="fa fa-trash" aria-hidden="true"></i>
  							</a>
  						</td>

  					</tr>

  				@endforeach

  			 </tbody>
  			</table>

  		 <form action="{{ route('tcpStore') }}" method="POST" class="form-horizontal row-border data-form" enctype="multipart/form-data">
  			<div class="form-group">
  				<div class="col-sm-3">
  					<input type="submit" class="btn-primary btn font-6" value="დამატება" style="width: 100%">
  				</div>
  				<div class="col-sm-4">
  					<input name="size" type="text" class="form-control font-7" placeholder="აკრიფეთ დასახელება">
  				</div>
  			</div>
  		</form>

  		</div>
  	</div>
  </div>
</div>

@include('parts.controlPanel.parameters')

@include('parts.controlPanel.switches')

@include('parts.controlPanel.lists')
