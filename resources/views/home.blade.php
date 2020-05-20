@extends('layouts.app')

@section('content')
<link rel= "stylesheet" type= "text/css" href= "{{URL::asset('css/home.css')}}">
<body>
	@if (in_array( Auth::user()->role->name, ['admin']))
	<div class="container">
		<div class="card-group">
			<div class="m-4">
				<div class="card" style="width: 18rem;">
				  	<div class="card-body">
					    <h5 class="card-title">MANAGE USERS</h5>
					    <ul class="list-group list-group-flush">
						    <li class="list-group-item"><a href="{{ route('users.index') }}" class="card-link">Index</a></li>
						    <li class="list-group-item"><a href="{{ route('users.create') }}" class="card-link">Create</a></li>
						</ul>
				  	</div>
				</div>
			</div>

			<div class="m-4">
				<div class="card" style="width: 18rem;">
				  	<div class="card-body">
					    <h5 class="card-title">MANAGE VIDEOS</h5>
					    <ul class="list-group list-group-flush">
						    <li class="list-group-item"><a href="{{ route('videos.index') }}" class="card-link">Index</a></li>
						    <li class="list-group-item"><a href="{{ route('videos.create') }}" class="card-link">Create</a></li>
						</ul>
				  	</div>
				</div>
			</div>
		</div>
	</div>
	@else
		<div class="container">
			<div class="card-group">
				@foreach ($videos as $video)
					@if (Auth::user()->id == $video->user_id)
						<div class="m-4">
							<div class="card" style="width: 18rem;">
								 <div class="card-body">
								    <h5 class="card-title">{{$video->name}}</h5>
								    <p class="card-text">Total number of frames is {{$video->num_frame}}</p>
								    <a href="{{ route('videos.annotate', $video->id) }}" class="btn btn-primary">Go annotate!</a>
								 </div>
							</div>
						</div>
					@endif
				@endforeach
			</div>
		</div>
	@endif
</body>
@endsection
