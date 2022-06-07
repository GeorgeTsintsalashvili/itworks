
<div class="row">

	<div class="col-xs-6">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="font-6">მწარმოებლები</h4>
			</div>
			<div class="panel-body">
				<table class="table table-hover mb1" data-update-route="{{ route('vcManufacturerUpdate') }}">
					<thead>
						<tr class="font-6">
							<th>დასახელება</th>
							<th>წაშლა</th>
						</tr>
					</thead>
					<tbody>

					@foreach($videoCardsManufacturers as $value)

						<tr data-record-id="{{ $value -> id }}">

							<!--- Title field --->
							<td>
								<input data-parameter-name="video-card-manufacturer-title" type="text" value="{{ $value -> videoCardManufacturerTitle }}" class="no-border transparent-bg-color font-7 field">
							</td>

							<!--- Delete button --->
							<td>
								<a class="delete-record" href="{{ route('vcManufacturerDestroy', ['id' => $value -> id]) }}">
									<i style="font-size: 16px" class="fa fa-trash" aria-hidden="true"></i>
								</a>
							</td>

						</tr>

					@endforeach

				 </tbody>
				</table>

      <div data-page-key="{{ $vcManufacturersPageKey }}" data-current-page="{{ $vcManufacturersPaginator -> currentPage }}" class="clearfix pagination-column col-sm-12">

        <ul class="pagination mt0 mb20 col-sm-8">

            @if($vcManufacturersPaginator -> currentPage > 1)

             <li>
              <a href="{{ $vcManufacturersPaginator -> currentPage - 1 }}" class="page-switch">
                <i class="fa fa-angle-double-left" aria-hidden="true"></i>
              </a>
             </li>

            @endif

            @foreach($vcManufacturersPaginator -> pages as $page)

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

            @if($vcManufacturersPaginator -> currentPage < $vcManufacturersPaginator -> maxPage)

            <li>
              <a href="{{ $vcManufacturersPaginator -> currentPage + 1 }}" class="page-switch">
                <i class="fa fa-angle-double-right" aria-hidden="true"></i>
              </a>
            </li>

            @endif

         </ul>

       </div>

			 <form action="{{ route('vcManufacturerStore') }}" method="post" class="form-horizontal row-border data-form" enctype="multipart/form-data">
				<div class="form-group">
					<div class="col-sm-3">
						<input type="submit" class="btn-primary btn font-6" value="დამატება">
					</div>
					<div class="col-sm-6">
						<input name="video-card-manufacturer-title" type="text" class="form-control font-7" placeholder="დასახელება">
					</div>
				</div>
			</form>

			</div>
		</div>
	</div>

<div class="col-xs-6">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="font-6">მეხსიერების ტიპები</h4>
		</div>
		<div class="panel-body">
			<table class="table table-hover mb1" data-update-route="{{ route('vcMemoryTypeUpdate') }}">
				<thead>
					<tr class="font-6">
						<th>დასახელება</th>
						<th>წაშლა</th>
					</tr>
				</thead>
				<tbody>

				@foreach($videoMemoryTypes as $value)

					<tr data-record-id="{{ $value -> id }}">

						<!--- Title field --->

						<td>
							<input data-parameter-name="video-memory-type-title" type="text" value="{{ $value -> typeTitle }}" class="no-border transparent-bg-color font-7 field">
						</td>

						<!--- Delete button --->

						<td>
							<a class="delete-record" href="{{ route('vcMemoryTypeDestroy', ['id' => $value -> id]) }}">
								<i style="font-size: 16px" class="fa fa-trash" aria-hidden="true"></i>
							</a>
						</td>

					</tr>
				@endforeach

			 </tbody>
			</table>

		 <form action="{{ route('vcMemoryTypeStore') }}" method="post" class="form-horizontal row-border data-form" enctype="multipart/form-data">
			<div class="form-group">
				<div class="col-sm-3">
					<input type="submit" class="btn-primary btn font-6" value="დამატება">
				</div>
				<div class="col-sm-6">
					<input name="video-memory-type-title" type="text" class="form-control font-7" placeholder="დასახელება">
				</div>
			</div>
		</form>

		</div>
	</div>
</div>

</div>

<div class="row">

	<div class="col-xs-6">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="font-6">სისტემური ბლოკის გრაფიკა</h4>
			</div>
			<div class="panel-body">
				<table class="table table-hover" data-update-route="{{ route('graphicsTypeUpdate') }}">
					<thead>
						<tr class="font-6">
							<th>დასახელება</th>
							<th>წაშლა</th>
						</tr>
					</thead>
					<tbody>

          @foreach($systemBlockGraphics as $value)

						<tr data-record-id="{{ $value -> id }}">

              <!--- Title field --->
							<td>
								<input data-parameter-name="computer-graphics-type-title" type="text" value="{{ $value -> graphicsTitle }}" class="no-border transparent-bg-color font-7 field">
							</td>

              <!--- Delete button --->
							<td>
								<a class="delete-record" href="{{ route('graphicsTypeDestroy', ['id' => $value -> id]) }}">
									<i style="font-size: 16px" class="fa fa-trash" aria-hidden="true"></i>
								</a>
							</td>

						</tr>

          @endforeach

				 </tbody>
				</table>

       <form action="{{ route('graphicsTypeStore') }}" method="post" class="form-horizontal row-border data-form" enctype="multipart/form-data">
 				<div class="form-group mt2">
          <div class="col-sm-3">
            <input type="submit" class="btn-primary btn font-6" value="დამატება">
          </div>
 					<div class="col-sm-6">
 						<input name="computer-graphics-type-title" type="text" class="form-control font-7" placeholder="დასახელება">
 					</div>
        </div>
      </form>

			</div>
		</div>
  </div>

	<div class="col-xs-6">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="font-6">გრაფიკული პროცესორები</h4>
			</div>
			<div class="panel-body">
				<table class="table table-hover mb1" data-update-route="{{ route('gpuManufacturerUpdate') }}">
					<thead>
						<tr class="font-6">
							<th>დასახელება</th>
							<th>წაშლა</th>
						</tr>
					</thead>
					<tbody>

					@foreach($gpuManufacturers as $value)

						<tr data-record-id="{{ $value -> id }}">

							<!--- Title field --->
							<td>
								<input data-parameter-name="gpu-manufacturer-title" type="text" value="{{ $value -> gpuTitle }}" class="no-border transparent-bg-color font-7 field">
							</td>

							<!--- Delete button ---->
							<td>
								<a class="delete-record" href="{{ route('gpuManufacturerDestroy', ['id' => $value -> id]) }}">
									<i style="font-size: 16px" class="fa fa-trash" aria-hidden="true"></i>
								</a>
							</td>

						</tr>

					@endforeach

				 </tbody>
				</table>

			 <form action="{{ route('gpuManufacturerStore') }}" method="post" class="form-horizontal row-border data-form" enctype="multipart/form-data">
				<div class="form-group">
					<div class="col-sm-3">
						<input type="submit" class="btn-primary btn font-6" value="დამატება" style="width: 100%">
					</div>
					<div class="col-sm-6">
						<input name="gpu-manufacturer-title" type="text" class="form-control font-7" placeholder="დასახელება">
					</div>
				</div>
			</form>

			</div>
		</div>
	</div>

</div>

@include('parts.controlPanel.parameters')
