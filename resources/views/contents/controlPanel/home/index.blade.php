
@extends('layouts.controlPanel')

@section('content')

<!--- user data update form --->

<div class="row">

 <div class="col-xs-8">
   <div class="panel panel-default">
 		<div class="panel-heading">
 			<h4 class="font-6">მომხმარებლის მონაცემები</h4>
 		</div>

 		<div class="panel-body">
 			<form action="{{ route('dataUpdate') }}" method="POST" class="form-horizontal row-border data-form-no-reload">

 				<div class="form-group">
 					<label class="col-sm-3 control-label font-6">სახელი</label>
 					<div class="col-sm-8">
            <div class="input-group">
             <span class="input-group-addon">
               <i class="fas fa-user"></i>
             </span>
 						 <input name="name" type="text" value="{{ $userData -> name }}" class="form-control font-7">
            </div>
 					</div>
 				</div>

        <div class="form-group">
          <label class="col-sm-3 control-label font-6">ფოსტა</label>
            <div class="col-sm-8">
             <div class="input-group">
              <span class="input-group-addon">
                <i class="fas fa-envelope"></i>
              </span>
              <input name="email" type="text" value="{{ $userData -> email }}" class="form-control font-7">
             </div>
           </div>
        </div>

        <div class="form-group">
					<label class="col-sm-3 control-label font-6">შეტყობინება</label>
					<div class="col-sm-8">
						<div class="input-group font-7">
							<span class="input-group-addon icheck">
								<input type="checkbox" name="notification-enabled" {{ $notificationData -> visibility ? "checked" : null }}>
							</span>
							<input type="text" name="notification" value="{{ $notificationData -> text }}" class="form-control font-7">
						</div>
					</div>
				</div>

 				<div class="panel-footer">
 					<div class="row">
 						<div class="col-sm-8 col-sm-offset-3">
 							<input type="submit" class="btn-primary btn font-6" value="შენახვა">
 						</div>
 					</div>
 				</div>

 			</form>
 		</div>
 	</div>
 </div>

</div>

<!--- password change form --->

<div class="row">

  <div class="col-xs-8">
    <div class="panel panel-default">
  		<div class="panel-heading">
  			<h4 class="font-6">პაროლის შეცვლა</h4>
  		</div>

  		<div class="panel-body">
  			<form action="{{ route('passwordChange') }}" method="POST" class="form-horizontal row-border data-form-no-reload form-reset">

  				<div class="form-group">
  					<label class="col-sm-4 control-label font-6">მიმდინარე პაროლი</label>
  					<div class="col-sm-6">
              <div class="input-group">
               <span class="input-group-addon">
                 <i class="fas fa-key"></i>
               </span>
               <span class="password-eye" data-slash="1">
                 <i class="fas fa-eye-slash"></i>
               </span>
   						 <input name="current-password" type="password" class="form-control font-7">
              </div>
  					</div>
  				</div>

  				<div class="form-group">
  					<label class="col-sm-4 control-label font-6">ახალი პაროლი</label>
  					<div class="col-sm-6">
             <div class="input-group">
              <span class="input-group-addon">
                <i class="fas fa-key"></i>
              </span>
              <span class="password-eye" data-slash="1">
                <i class="fas fa-eye-slash"></i>
              </span>
              <input name="new-password" type="password" class="form-control font-7" placeholder="მინიმუმ 8 სიმბოლო">
             </div>
  					</div>
  				</div>

  				<div class="form-group">
  					<label class="col-sm-4 control-label font-6">გაიმეორეთ პაროლი</label>
  					<div class="col-sm-6">
             <div class="input-group">
              <span class="input-group-addon">
                <i class="fas fa-key"></i>
              </span>
              <span class="password-eye" data-slash="1">
                <i class="fas fa-eye-slash"></i>
              </span>
              <input name="new-confirm-password" type="password" class="form-control font-7" placeholder="მინიმუმ 8 სიმბოლო">
             </div>
  					</div>
  				</div>

  				<div class="panel-footer">
  					<div class="row">
  						<div class="col-sm-6 col-sm-offset-4">
  							<input type="submit" class="btn-primary btn font-6" value="შენახვა">
  						</div>
  					</div>
  				</div>

  			</form>
  		</div>
  	</div>
  </div>

</div>

@include('parts.controlPanel.parameters')

@endsection
