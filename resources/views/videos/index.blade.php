@extends('layouts.app')

@section('content')
<body>
<div class="col">
	<span class="m-5 title_menu_item"><a href="{{ url('videos/create') }}"><i class="fas fa-plus fa-lg color8"></i> Create</a></span>
	<div class="m-5" style="text-align: center;">
		<div class="table-responsive">
			<table class="table table-striped table-bordered">
				<thead>
				    <tr>
					    <th scope="col">#</th>
					    <th scope="col">Id</th>
					    <th scope="col">Name</th>
					    <th scope="col">Total frame</th>
					    <th scope="col">User name</th>
					    <th scope="col">Action</th>
				    </tr>
				</thead>
				<tbody>
					@foreach ($videos as $video)
						<tr>
							<td>{{$loop->iteration}}</td>
							<td>{{$video->id}}</td>
							<td>{{$video->name}}</td>
							<td>{{$video->num_frame}}</td>
							@if ($video->user != null)
								<td>{{$video->user->username}}</td>
							@else
								<td></td>
							@endif
							<td> 
								<a title="Annotate" href="{{ route('videos.annotate', $video->id) }}" style="color: green;"><i class="fas fa-edit fa-lg color9"></i>Annotate</a>
								<a title="Upload more file" href="{{ route('videos.upload_more_file', $video->id) }}" style="color: #A912B4;"><i class="fas fa-edit fa-lg color9"></i>Upload more file</a>
								<a title="Edit" href="{{ route('videos.edit', $video) }}"><i class="fas fa-edit fa-lg color9"></i>Edit</a>
								<a title="Delete" href="{{ route('videos.delete', $video->id) }}" style="color: red;"><i class="fas fa-edit fa-lg color9"></i>Delete</a>
								<a title="Download_json" href="{{ route('videos.download', $video->id) }}" style="color: #C70039;"><i class="fas fa-edit fa-lg color9"></i>Download json</a>
							</td>
						</tr>
					@endforeach
			  	</tbody>
			</table>
		</div>
	</div>
</div>
</body>
@endsection