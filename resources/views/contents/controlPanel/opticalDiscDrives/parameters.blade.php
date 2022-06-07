
<div class="row">
	<div class="col-xs-8">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="font-6">დისკმძრავის ტიპები</h4>
			</div>
			<div class="panel-body">
				<table class="table table-hover mb1" data-update-route="{{ route('oddtUpdate') }}">
					<thead>
						<tr class="font-6">
							<th>დასახელება</th>
							<th>წაშლა</th>
						</tr>
					</thead>
					<tbody>

          @foreach($oddTypes as $value)

						<tr data-record-id="{{ $value -> id }}">

              <!--- Title field --->
							<td>
								<input data-parameter-name="odd-type-title" type="text" value="{{ $value -> typeTitle }}" class="no-border transparent-bg-color font-7 field">
							</td>

              <!--- Delete button --->
							<td>
								<a class="delete-record" href="{{ route('oddtDestroy', ['id' => $value -> id]) }}">
									<i style="font-size: 16px" class="fa fa-trash" aria-hidden="true"></i>
								</a>
							</td>
						</tr>

          @endforeach

				 </tbody>
				</table>

        <div data-page-key="{{ $oddtPageKey }}" data-current-page="{{ $oddtPaginator -> currentPage }}" class="clearfix pagination-column col-sm-12">

          <ul class="pagination mt0 mb20 col-sm-8">

              @if($oddtPaginator -> currentPage > 1)

               <li>
                <a href="{{ $oddtPaginator -> currentPage - 1 }}" class="page-switch">
                  <i class="fa fa-angle-double-left" aria-hidden="true"></i>
                </a>
               </li>

              @endif

              @foreach($oddtPaginator -> pages as $page)

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

              @if($oddtPaginator -> currentPage < $oddtPaginator -> maxPage)

              <li>
                <a href="{{ $oddtPaginator -> currentPage + 1 }}" class="page-switch">
                  <i class="fa fa-angle-double-right" aria-hidden="true"></i>
                </a>
              </li>

              @endif

           </ul>

         </div>

       <form action="{{ route('oddtStore') }}" method="post" class="form-horizontal row-border data-form" enctype="multipart/form-data">
 				<div class="form-group">
          <div class="col-sm-3">
            <input type="submit" class="btn-primary btn font-6" value="დამატება">
          </div>
 					<div class="col-sm-4">
 						<input name="odd-type-title" type="text" class="form-control font-7" placeholder="დასახელება">
 					</div>
        </div>
      </form>

			</div>
		</div>
  </div>

</div>

@include('parts.controlPanel.parameters')
