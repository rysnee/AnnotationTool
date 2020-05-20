@extends('layouts.app')

@section('content')
<body>
	<div class="container">
	    <div class="row justify-content-center">
	        <div class="col-md-8">
	            <div class="card">
	                <div class="card-header">{{ __('Create') }}</div>

	                <div class="card-body">
	                    <form method="POST" action="{{ route('users.update', $user) }}">
	                        @csrf
	                        @method("PUT")
	                        <div class="form-group row">
	                            <label for="username" class="col-md-4 col-form-label text-md-right">{{ __('Name') }}</label>

	                            <div class="col-md-6">
	                                <input id="username" type="text" class="form-control @error('username') is-invalid @enderror" name="username" value="{{$user->username}}" required autocomplete="username" autofocus disabled>

	                                @error('name')
	                                    <span class="invalid-feedback" role="alert">
	                                        <strong>{{ $message }}</strong>
	                                    </span>
	                                @enderror
	                            </div>
	                        </div>

	                        <div class="form-group row">
	                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

	                            <div class="col-md-6">
	                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{$user->email}}" required autocomplete="email" disabled>

	                                @error('email')
	                                    <span class="invalid-feedback" role="alert">
	                                        <strong>{{ $message }}</strong>
	                                    </span>
	                                @enderror
	                            </div>
	                        </div>

	                        <div class="form-group row">
	                            <label for="admin" class="col-md-4 col-form-label text-md-right">{{ __('Admin') }}</label>
	                            <div class="col-md-6">
		                            <div class="custom-control custom-switch">
		                            	@if ($user->role->name != "admin")
									  		<input type="checkbox" class="custom-control-input" id="customSwitches" name="admin">
									  	@else
									  		<input type="checkbox" class="custom-control-input" id="customSwitches" name="admin" checked="">
									  	@endif
									  <label class="custom-control-label" for="customSwitches">Switch on to set user's role as admin</label>
									</div>
								</div>
	                        </div>

	                        <div class="form-group row">
	                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

	                            <div class="col-md-6">
	                                <input id="password" type="text" class="form-control" name="password">
	                            </div>
	                        </div>

	                        @if ($user->role->name != "admin")
		                        <div class="form-group row">
		                            <label for="videos" class="col-md-4 col-form-label text-md-right">{{ __('Select videos') }}</label>
		                            <div class="col-md-6">
		                                <select class="js-example-basic-multiple form-control" multiple="multiple" name="video_id[]">
											@foreach( $videos as $video)
												@if ($video->user_id == null)
													<option value="{{ $video->id }}" data-name="{{$video->name}}" data-id="{{$video->id}}" > {{$video->name}}</option>
												@elseif ($video->user_id == $user->id)
													<option value="{{ $video->id }}" data-name="{{$video->name}}" data-id="{{$video->id}}" selected="selected"> {{$video->name}}</option>
												@endif	
											@endforeach
										</select>
		                            </div>
		                        </div>
		                       @endif

	                        <div class="form-group row mb-0">
	                            <div class="col-md-6 offset-md-4">
	                                <button type="submit" class="btn btn-primary">
	                                    {{ __('Edit') }}
	                                </button>
	                            </div>
	                        </div>
	                    </form>
	                </div>
	            </div>
	        </div>
	    </div>
	</div>
</body>
@endsection