
<!--- content start --->

@if($contact !== null)

<div data-widget-group="group1">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="font-6">საკონტაქტო ინფორმაცია</h4>
		</div>
		<div class="panel-editbox" data-widget-controls=""></div>
		<div class="panel-body">
			<form action="{{ route('contactUpdate') }}" method="POST" class="form-horizontal row-border data-form">
				<div class="form-group">
					<label class="col-sm-2 control-label font-6">კომპანიის დასახელება</label>
					<div class="col-sm-8">
						<input name="companyName" type="text" value="{{ $contact -> companyName }}" class="form-control font-7">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label font-6">ტელეფონის ნომერი</label>
					<div class="col-sm-8">
						<input name="phone" type="text" value="{{ $contact -> phone }}" class="form-control font-7">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label font-6">ელ. ფოსტა</label>
					<div class="col-sm-8">
						<input name="email" type="text" value="{{ $contact -> email }}" class="form-control font-7">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label font-6">განრიგი</label>
					<div class="col-sm-8">
						<input name="schedule" type="text" value="{{ $contact -> schedule }}" class="form-control font-7">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label font-6">მიწოდება</label>
					<div class="col-sm-8">
						<input name="delivery" type="text" value="{{ $contact -> delivery }}" class="form-control font-7">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label font-6">მისამართი</label>
					<div class="col-sm-8">
						<input name="address" type="text" value="{{ $contact -> address }}" class="form-control font-7">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label font-6">მისამართის ბმული</label>
					<div class="col-sm-8">
						<input name="googleMapLink" type="text" value="{!! $contact -> googleMapLink !!}" class="form-control font-7">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label font-6">ფეისბუქის ბმული</label>
					<div class="col-sm-8">
						<input name="facebookPageLink" type="text" value="{!! $contact -> facebookPageLink !!}" class="form-control font-7">
					</div>
				</div>
				<div class="panel-footer">
					<div class="row">
						<div class="col-sm-8 col-sm-offset-2">
							<input type="submit" class="btn-primary btn font-6" value="შენახვა">
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>

<div class="row">
    <div class="col-xs-12">
        <div class="panel">
          <div class="panel-heading">
            <h4 class="font-6">მისამართის რუქა</h4>
          </div>
					<iframe width="100%" height="400" src="{!! $contact -> googleMapLink !!}" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe>
        </div>
    </div>
</div>

@include('parts.controlPanel.parameters')

@endif

<!--- content end --->
