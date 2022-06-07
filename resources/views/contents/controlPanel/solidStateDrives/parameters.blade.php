
<div class="row">
	<div class="col-xs-6">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="font-6">ფორმფაქტორები</h4>
			</div>
			<div class="panel-body">
				<table class="table table-hover mb1" data-update-route="{{ route('ssdffUpdate') }}">
					<thead>
						<tr class="font-6">
							<th>დასახელება</th>
							<th>პლატის</th>
							<th>წაშლა</th>
						</tr>
					</thead>
					<tbody>

          @foreach($formFactors as $value)

						<tr data-record-id="{{ $value -> id }}">

              <!--- Title field --->
							<td>
								<input data-parameter-name="ssd-ff-title" type="text" value="{{ $value -> formFactorTitle }}" class="no-border transparent-bg-color font-7 field">
							</td>

							<!--- switch button --->
							<td>
								<input data-parameter-name="onboard" data-size="mini" class="record-update-switch field" type="checkbox" {{ $value -> onboard ? "checked" : null }} value="{{ $value -> onboard }}">
							</td>

              <!--- Delete button --->
							<td>
								<a class="delete-record" href="{{ route('ssdffDestroy', ['id' => $value -> id]) }}">
									<i style="font-size: 16px" class="fa fa-trash" aria-hidden="true"></i>
								</a>
							</td>
						</tr>

          @endforeach

				 </tbody>
				</table>

        <div data-page-key="{{ $ssdffPageKey }}" data-current-page="{{ $ssdffPaginator -> currentPage }}" class="clearfix pagination-column col-sm-12">

          <ul class="pagination mt0 mb20 col-sm-8">

              @if($ssdffPaginator -> currentPage > 1)

               <li>
                <a href="{{ $ssdffPaginator -> currentPage - 1 }}" class="page-switch">
                  <i class="fa fa-angle-double-left" aria-hidden="true"></i>
                </a>
               </li>

              @endif

              @foreach($ssdffPaginator -> pages as $page)

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

              @if($ssdffPaginator -> currentPage < $ssdffPaginator -> maxPage)

              <li>
                <a href="{{ $ssdffPaginator -> currentPage + 1 }}" class="page-switch">
                  <i class="fa fa-angle-double-right" aria-hidden="true"></i>
                </a>
              </li>

              @endif

           </ul>

         </div>

       <form action="{{ route('ssdffStore') }}" method="post" class="form-horizontal row-border data-form" enctype="multipart/form-data">
 				<div class="form-group">
          <div class="col-sm-3">
            <input type="submit" class="btn-primary btn font-6" value="დამატება">
          </div>
 					<div class="col-sm-4">
 						<input name="ssd-ff-title" type="text" class="form-control font-7" placeholder="დასახელება">
 					</div>
					<div class="col-sm-2">
						<input name="onboard" class="form-switch" data-size="medium" type="checkbox" data-off-color="default" data-on-text="I" data-off-text="O" value="0">
					</div>
        </div>
      </form>

			</div>
		</div>
  </div>

	<div class="col-xs-6">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="font-6">ტექნოლოგიები</h4>
			</div>
			<div class="panel-body">
				<table class="table table-hover mb1" data-update-route="{{ route('ssdTcUpdate') }}">
					<thead>
						<tr class="font-6">
							<th>დასახელება</th>
							<th>წაშლა</th>
						</tr>
					</thead>
					<tbody>

          @foreach($technologies as $value)

						<tr data-record-id="{{ $value -> id }}">

              <!--- Title field --->
							<td>
								<input data-parameter-name="ssd-technology" type="text" value="{{ $value -> technologyTitle }}" class="no-border transparent-bg-color font-7 field">
							</td>

              <!--- Delete button --->
							<td>
								<a class="delete-record" href="{{ route('ssdTcDestroy', ['id' => $value -> id]) }}">
									<i style="font-size: 16px" class="fa fa-trash" aria-hidden="true"></i>
								</a>
							</td>
						</tr>

          @endforeach

				 </tbody>
				</table>

       <form action="{{ route('ssdTcStore') }}" method="post" class="form-horizontal row-border data-form" enctype="multipart/form-data">
 				<div class="form-group">
          <div class="col-sm-3">
            <input type="submit" class="btn-primary btn font-6" value="დამატება">
          </div>
 					<div class="col-sm-4">
 						<input name="ssd-technology" type="text" class="form-control font-7" placeholder="დასახელება">
 					</div>
        </div>
      </form>

			</div>
		</div>
  </div>

</div>

@include('parts.controlPanel.parameters')

@include('parts.controlPanel.switches')
