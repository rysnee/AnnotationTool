@extends('layouts.app')

@section('content')
<body>
	<div class="container">
	    <div class="row justify-content-center">
	        <div class="col-md-8">
	            <div class="card">
	                <div class="card-header">{{ __('Create') }}</div>

	                <div class="card-body">
	                    <form method="POST" action="{{ route('videos.store') }}" enctype="multipart/form-data">
	                        @csrf

	                        <div class="form-group row">
	                            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Name') }}</label>

	                            <div class="col-md-6">
	                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
	                            </div>
	                        </div>

	                        <div class="form-group row">
	                            <label for="user_id" class="col-md-4 col-form-label text-md-right">{{ __('Select user') }}</label>
	                            <div class="col-md-6">
	                            	<select class="custom-select custom-select-sm form-control" name="user_id">
									  	<option selected value="null">NULL</option>
									  	@foreach( $users as $user)
									  		@if ($user->role->name != "admin")
												<option value="{{ $user->id }}" data-name="{{$user->username}}" data-id="{{$user->id}}" >{{$user->username}}</option>
											@endif
										@endforeach
									</select>
	                            </div>
	                        </div>

	                        <div class="form-group row">
	                            <label for="max_id" class="col-md-4 col-form-label text-md-right">{{ __('Max id') }}</label>

	                            <div class="col-md-6">
	                                <input id="max_id" type="number" class="form-control @error('max_id') is-invalid @enderror" name="max_id" value="{{ old('max_id') }}">
	                            </div>
	                        </div>

	                        <div class="form-group row">
	                            <label for="num_frame" class="col-md-4 col-form-label text-md-right">{{ __('Total frame') }}</label>

	                            <div class="col-md-6">
	                                <input id="num_frame" type="number" class="form-control @error('num_frame') is-invalid @enderror" name="num_frame" value="{{ old('num_frame') }}">
	                            </div>
	                        </div>

	                        <div class="form-group row">
	                            <label for="data_zip_file" class="col-md-4 col-form-label text-md-right">{{ __('Data zip') }}</label>

	                            <div class="col-sm-6">
									<div class="custom-file">
										<div class="btn btn-sm float-left">
									      	<input id="data_zip_file" type="file" name="data_zip_file"/>
									    </div>
									</div>
								</div>
	                        </div>

	                        <div class="form-group row">
	                            <label for="json_zip_file" class="col-md-4 col-form-label text-md-right">{{ __('Json zip') }}</label>

	                            <div class="col-sm-6">
									<div class="custom-file">
										<div class="btn btn-sm float-left">
									      	<input id="json_zip_file" type="file" name="json_zip_file"/>
									    </div>
									</div>
								</div>
	                        </div>
					
	                        <div class="form-group row mb-0">
	                            <div class="col-md-6 offset-md-4">
	                                <button type="submit" class="btn btn-primary">
	                                    {{ __('Create') }}
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