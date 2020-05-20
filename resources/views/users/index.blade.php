@extends('layouts.app')

@section('content')
<body>
<div class="col">
	<span class="m-5 title_menu_item"><a href="{{ url('users/create') }}"><i class="fas fa-plus fa-lg color8"></i> Create</a></span>
	<div class="m-5" style="text-align: center;">
		<div class="table-responsive">
			<table class="table table-striped table-bordered">
				<thead>
				    <tr>
					    <th scope="col">#</th>
					    <th scope="col">Id</th>
					    <th scope="col">Name</th>
					    <th scope="col">Role</th>
					    <th scope="col">Video name</th>
					    <th scope="col">Action</th>
				    </tr>
				</thead>
				<tbody>
					@foreach ($users as $user)
						<tr>
							<td>{{$loop->iteration}}</td>
							<td>{{$user->id}}</td>
							<td>{{$user->username}}</td>
							<td>{{$user->role->name}}</td>
							<td>
								@if ($user->role->name == "admin")
									<span class="badge badge-pill badge-info">all</span>
								@else
									@foreach ($user->video as $video)
										<span class="badge badge-pill badge-info">{{$video->name}}</span>
									@endforeach
								@endif
							</td>
							<td> 
								<a title="Edit" href="{{ route('users.edit', $user) }}"><i class="fas fa-edit fa-lg color9"></i>Edit</a>
								<a title="Delete" href="{{ route('users.delete', $user->id) }}" style="color: red;"><i class="fas fa-edit fa-lg color9"></i>Delete</a>
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