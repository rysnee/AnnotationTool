@extends('layouts.app')

@section('content')
<link rel= "stylesheet" type= "text/css" href= "{{URL::asset('css/videos_annotate.css')}}">
<body onload="load()">
    <div class="frame_index">
        <input type="number" id="frame_index" placeholder="1/{{$curr_video->num_frame}}"/> 
        <span class="input-group-btn">
        	<button class="btn btn-success" type="button" onclick="change_img_input()"> Go! </button>
        	<button class="btn btn-danger" type="button" onclick="how_to_use()"> How to use? </button>
     	</span>
    </div>

	<table class="table_process" align="center">
		<tr>
			<td>
				<div class="container">
				    <img class='img' id='img1' src="{{URL::asset('dataset/'.$curr_video->name.'/0.jpg')}}">
				    <canvas class="canvas" id="canvas1"></canvas>
				</div>
			</td>
			<td>
				<div class="container">
					<img class='img' id='img2'src="{{URL::asset('dataset/'.$curr_video->name.'/1.jpg')}}"/>
				    <canvas class="canvas" id="canvas2"></canvas>
				</div>
			</td>
		</tr>
		<tr>
			<td class="name_image" id="pre_image"> Previous frame: 0.jpg </td>
			<td class="name_image" id="curr_image"> Current frame: 1.jpg </td>
		</tr>
	</table>
	
	<div class="input">
		<a id="curr_frame"> {{$curr_frame}} </a>
	</div>
</body>

<script type="text/javascript" src="{{URL::asset('js/wheelzoom.js')}}"></script>
<script type="text/javascript">
	//Init--------------------------------------------------------------------------------------------
	var canvas1=document.getElementById("canvas1");
	var img1 = document.getElementById("img1"); 
	ctx1 = canvas1.getContext("2d");

	canvas1.width = img1.clientWidth;
	canvas1.height = img1.clientHeight;

	var canvas2=document.getElementById("canvas2");
	var img2 = document.getElementById("img2"); 
	ctx2 = canvas2.getContext("2d");

	canvas2.width = img2.clientWidth;
	canvas2.height = img2.clientHeight;

	images = wheelzoom(document.querySelectorAll("img"), {zoom: 0.1, maxZoom: 10});

	var curr_points = []; var pre_points = []; 

	var check_curr = []; var check_pre = [];
	
	var pre_point = [-1,-1,-1,-1]; var curr_point = [-1,-1,-1,-1]; var old_pre_point = [-1,-1,-1,-1]; var old_curr_point = [-1,-1,-1,-1];
	var max_id = Number("{{$curr_video->max_id}}");
	var curr_frame = Number("{{$curr_frame}}");

	var xShift = 0; var yShift = 0; var scale = img1.naturalWidth/img1.clientWidth; 
	var dragging = 0; var id = 0; var move = 0; var save = 0; var change = 0;


	//Wheelzoom img behind canvas----------------------------------------------------------------------
	canvas1.addEventListener('wheel', function(e) {
		if (e.deltaY < 0)
		{
			images[0].doZoomIn();
			images[1].doZoomIn();
			scale = img1.naturalWidth/images[0].get_value()[0];
			xShift = images[0].get_value()[1]; yShift = images[0].get_value()[2];
			updateCanvas1();
			updateCanvas2();
		} 
		else
		{
			images[0].doZoomOut();
			images[1].doZoomOut();
			scale = img1.naturalWidth/images[0].get_value()[0];
			xShift = images[0].get_value()[1]; yShift = images[0].get_value()[2];
			updateCanvas1();
			updateCanvas2();
		}	
	});
	
	canvas1.addEventListener('mousedown', function(e) {
		dragging = 0;
		canvas1.addEventListener('mousemove', function() {
        	dragging=1;
        	xShift = images[0].get_value()[1]; yShift = images[0].get_value()[2];
        	scale = img1.naturalWidth/images[0].get_value()[0];
        	updateCanvas1();
        	updateCanvas2();
        });
		images[0].imgDrag(e);
		images[1].imgDrag(e);		
	});

	canvas2.addEventListener('wheel', function(e) {
		if (e.deltaY < 0)
		{
			images[0].doZoomIn();
			images[1].doZoomIn();
			scale = img1.naturalWidth/images[1].get_value()[0];
			xShift = images[1].get_value()[1]; yShift = images[1].get_value()[2];
			updateCanvas1();
			updateCanvas2();
		} 
		else
		{
			images[0].doZoomOut();
			images[1].doZoomOut();
			scale = img1.naturalWidth/images[1].get_value()[0];
			xShift = images[1].get_value()[1]; yShift = images[1].get_value()[2];
			updateCanvas1();
			updateCanvas2();
		}	
	});
	
	canvas2.addEventListener('mousedown', function(e) {
		dragging = 0;
        canvas2.addEventListener('mousemove', function() {
        	dragging=1;
        	xShift = images[0].get_value()[1]; yShift = images[0].get_value()[2];
        	scale = img1.naturalWidth/images[0].get_value()[0];
        	updateCanvas1();
        	updateCanvas2();
        });
		images[0].imgDrag(e);
		images[1].imgDrag(e);
	});

	//Catch key events--------------------------------------------------------------------------------------
	document.addEventListener('keydown', function(e) {
		//Move frame by key <- ->
		if (e.code == "ArrowRight" || e.code == "ArrowLeft")
		{
			curr_frame = Number(document.getElementById("curr_frame").innerHTML);
			if (change == 1)
			{
				checksave(e);
			}			
		}
		//Save
		else if (e.code == "KeyS" )
		{			
			//if (save == 1)
				save_json();
		}
		else if (e.code == "KeyI" )
		{
			if (id == 0)
				id = 1;
			else id = 0;
			updateCanvas1();
			updateCanvas2();
		}
		//Match
		else if (e.code == "KeyM" )
		{
			if (Number(curr_point[2]) != -1 && Number(pre_point[2]) != -1)
			{
				if (Number(old_curr_point[2]) == -1 && Number(old_pre_point[2]) == -1)
				{
					curr_points[Number(curr_point[3])][2] = pre_point[2];
					check_curr[Number(curr_point[3])] = 1;
					check_pre[Number(pre_point[3])] = 1;
				} 
				else if	(Number(old_curr_point[2]) != -1)
				{
					curr_points[Number(curr_point[3])][2] = pre_point[2];
					check_curr[Number(curr_point[3])] = 1;
					check_pre[Number(pre_point[3])] = 1;
					curr_points[Number(old_curr_point[3])][2] = curr_point[2]; 
					if (Number(old_pre_point[2]) == -1)
						check_curr[Number(old_curr_point[3])] = 0;
						
				}
				else alert("You can't match this case\nPlease delete after or before, then add new point and match it");
			}
			init_point();
			updateCanvas1();
			updateCanvas2();
		}
		//Delete before
		else if (e.code == "KeyB" )
		{
			if (change == 1)
				delete_before();
		}
		//Delete after
		else if (e.code == "KeyA" )
		{
			if (change == 1)
				delete_after();
		}
		else if (e.code == "Escape" )
		{
			if (change == 1)
			{
				init_point();
				updateCanvas1();
				updateCanvas2();
			}
		}
		else if (e.code == "ControlLeft" || e.code == "ControlRight") //Ctrl + Rightclick to move point
		{		
			if (change == 1)
			{
				move = 1;
				document.addEventListener('keyup', function(ev) {
					if (e.code == "ControlLeft" || e.code == "ControlRight")
						move = 0;
				});
				canvas2.addEventListener('contextmenu', function(evt) { 
					if (move == 1)
					{
						evt.preventDefault();
						var point = getCursorPosition(img2, evt);
						if (Number(curr_point[2]) != -1)
						{
							curr_points[Number(curr_point[3])] = [point[0],point[1],curr_points[Number(curr_point[3])][2]];
							updateCanvas2();
						}
					}	
				});
			}
		}
	});
	//Catch mouse events----------------------------------------------------------------------------------
	// Right click to select
	canvas1.addEventListener('contextmenu', function(evt) { 
		if (move == 0)
		{
			evt.preventDefault();
			var point = getCursorPosition(img1, evt);
			choose_pre(point);
		}
	});

	canvas2.addEventListener('contextmenu', function(evt) { 
		if (move == 0)
		{
			evt.preventDefault();
			var point = getCursorPosition(img2, evt);
			choose_curr(point);
		}	
	});

	//Left click  to add new point
	canvas2.addEventListener('click', function(e) { 
		e.preventDefault();
		if (dragging == 0){
            var point = getCursorPosition(img2, e);
			max_id = max_id + 1;
			curr_points.push([point[0], point[1], max_id]);	
			check_curr.push(0);
			updateCanvas2();
        }
	});

	//Function--------------------------------------------------------------------------------------------
	function init_point()
	{
		pre_point = [-1,-1,-1,-1];
		curr_point = [-1,-1,-1,-1];
		old_pre_point = [-1,-1,-1,-1];
		old_curr_point = [-1,-1,-1,-1];
	}

	function getCursorPosition(image, event) {
	    const rect = image.getBoundingClientRect();
	    const xc = event.clientX - rect.left;
	    const yc = event.clientY - rect.top;
	    var x = (xc - xShift)*scale;
	    var y = (yc - yShift)*scale;
	    return [Math.round(x), Math.round(y)];
	}

	function choose_pre(point)
	{
		var min = 9007199254740992;
		var d;
		var i;
		var index;
		for (i = 0; i < pre_points.length; i++)
		{
			d = Math.sqrt(Math.pow(Number(pre_points[i][0]) - Number(point[0]), 2) + Math.pow(Number(pre_points[i][1]) - Number(point[1]), 2));
			if (d <= 5/scale && d < min)
			{
				min = d;
				pre_point = [pre_points[i][0],pre_points[i][1],pre_points[i][2],i];
				index = i;
			}
		}
		updateCanvas1();
		if (check_pre[index] == 1)
		{
			var j;
			for (j = 0; j < curr_points.length; j++)
				if (pre_point[2] == curr_points[j][2])
				{
					old_curr_point = [curr_points[j][0],curr_points[j][1],curr_points[j][2],j];
					break;
				}
			updateCanvas2();
		}
	}

	function choose_curr(point)
	{
		var min = 9007199254740992;
		var d;
		var i;
		var index;
		for (i = 0; i < curr_points.length; i++)
		{
			d = Math.sqrt(Math.pow(Number(curr_points[i][0]) - Number(point[0]), 2) + Math.pow(Number(curr_points[i][1]) - Number(point[1]), 2));
			if (d <= 5/scale && d < min)
			{
				min = d;
				curr_point = [curr_points[i][0],curr_points[i][1],curr_points[i][2],i];
				index = i;
			}
		}
		updateCanvas2();
		if (check_curr[index] == 1)
		{
			var j;
			for (j = 0; j < pre_points.length; j++)
				if (curr_point[2] == pre_points[j][2])
				{
					old_pre_point = [pre_points[j][0],pre_points[j][1],pre_points[j][2],j];
					break;
				}
			updateCanvas1();
		}
	}

	function updateCanvas1()
	{
		ctx1.clearRect(0, 0, canvas1.width, canvas1.height);

		var i;
		for (i = 0; i < pre_points.length; i++) 
		{
			var d = 3/scale;
			var db = 4/scale;
			var x = (Number(pre_points[i][0])/scale + xShift) - d;
			var y = (Number(pre_points[i][1])/scale + yShift) - d;
			if (id == 0)
			{
				if (x > 0 && y > 0 )
				{	
					ctx1.beginPath();
					ctx1.rect(x - 1/scale, y - 1/scale, 2*db, 2*db);
					ctx1.fillStyle = "white";
					ctx1.fill();

					ctx1.beginPath();
					ctx1.rect(x, y, 2*d, 2*d);
					if (pre_point[2] == pre_points[i][2] || old_pre_point[2] == pre_points[i][2])
						ctx1.fillStyle = "green";
					else if (check_pre[i] == 0)
						ctx1.fillStyle = "red";
					else ctx1.fillStyle = "blue";
					ctx1.fill();
				} else if (x < 0 &&  (x - 1/scale + 2*db) > 0 && y >= 0)
				{
					ctx1.beginPath();
					ctx1.rect(0, y - 1/scale, 2*db + x - 1/scale, 2*db);
					ctx1.fillStyle = "white";
					ctx1.fill();

					ctx1.beginPath();
					ctx1.rect(0, y, 2*d + x, 2*d);
					if (pre_point[2] == pre_points[i][2] || old_pre_point[2] == pre_points[i][2])
						ctx1.fillStyle = "green";
					else if (check_pre[i] == 0)
						ctx1.fillStyle = "red";
					else ctx1.fillStyle = "blue";
					ctx1.fill();
				} else if (x > 0 &&  y < 0 && (y - 1/scale + 2*db) > 0)
				{
					ctx1.beginPath();
					ctx1.rect(x - 1/scale, 0, 2*db, 2*db + y - 1/scale);
					ctx1.fillStyle = "white";
					ctx1.fill();

					ctx1.beginPath();
					ctx1.rect(x, 0, 2*d, 2*d + y);
					if (pre_point[2] == pre_points[i][2] || old_pre_point[2] == pre_points[i][2])
						ctx1.fillStyle = "green";
					else if (check_pre[i] == 0)
						ctx1.fillStyle = "red";
					else ctx1.fillStyle = "blue";
					ctx1.fill();
				}
				else if ((x - 1/scale + 2*db) > 0 && (y - 1/scale + 2*db) > 0)
				{
					ctx1.beginPath();
					ctx1.rect(0, 0, 2*db + x - 1/scale, 2*db + y - 1/scale);
					ctx1.fillStyle = "white";
					ctx1.fill();

					ctx1.beginPath();
					ctx1.rect(0, 0, 2*d + x, 2*d + y);
					if (pre_point[2] == pre_points[i][2] || old_pre_point[2] == pre_points[i][2])
						ctx1.fillStyle = "green";
					else if (check_pre[i] == 0)
						ctx1.fillStyle = "red";
					else ctx1.fillStyle = "blue";
					ctx1.fill();		
				}
			} else
			{
				ctx1.font = (15/scale).toString() + "px Verdana";
				if (pre_point[2] == pre_points[i][2] || old_pre_point[2] == pre_points[i][2])
					ctx1.fillStyle = "green";
				else if (check_pre[i] == 0)
					ctx1.fillStyle = "red";
				else ctx1.fillStyle = "blue";
				ctx1.fillText(pre_points[i][2], x, y);
			}
		}
	}

	function updateCanvas2()
	{
		ctx2.clearRect(0, 0, canvas1.width, canvas1.height);
		
		var i;
		for (i = 0; i < curr_points.length; i++) 
		{
			var d = 3/scale;
			var db = 4/scale;
			var x = (Number(curr_points[i][0])/scale + xShift) - d;
			var y = (Number(curr_points[i][1])/scale + yShift) - d;
			if (id == 0)
			{
				if (x > 0 && y > 0 )
				{	
					ctx2.beginPath();
					ctx2.rect(x - 1/scale, y - 1/scale, 2*db, 2*db);
					ctx2.fillStyle = "white";
					ctx2.fill();

					ctx2.beginPath();
					ctx2.rect(x, y, 2*d, 2*d);
					if (curr_point[2] == curr_points[i][2] || old_curr_point[2] == curr_points[i][2])
						ctx2.fillStyle = "green";
					else if (check_curr[i] == 0)
						ctx2.fillStyle = "red";
					else ctx2.fillStyle = "blue";
					ctx2.fill();
				} else if (x < 0 &&  (x - 1/scale + 2*db) > 0 && y >= 0)
				{
					ctx2.beginPath();
					ctx2.rect(0, y - 1/scale, 2*db + x - 1/scale, 2*db);
					ctx2.fillStyle = "white";
					ctx2.fill();

					ctx2.beginPath();
					ctx2.rect(0, y, 2*d + x, 2*d);
					if (curr_point[2] == curr_points[i][2] || old_curr_point[2] == curr_points[i][2])
						ctx2.fillStyle = "green";
					else if (check_curr[i] == 0)
						ctx2.fillStyle = "red";
					else ctx2.fillStyle = "blue";
					ctx2.fill();
				} else if (x > 0 &&  y < 0 && (y - 1/scale + 2*db) > 0)
				{
					ctx2.beginPath();
					ctx2.rect(x - 1/scale, 0, 2*db, 2*db + y - 1/scale);
					ctx2.fillStyle = "white";
					ctx2.fill();

					ctx2.beginPath();
					ctx2.rect(x, 0, 2*d, 2*d + y);
					if (curr_point[2] == curr_points[i][2] || old_curr_point[2] == curr_points[i][2])
						ctx2.fillStyle = "green";
					else if (check_curr[i] == 0)
						ctx2.fillStyle = "red";
					else ctx2.fillStyle = "blue";
					ctx2.fill();
				}
				else if ((x - 1/scale + 2*db) > 0 && (y - 1/scale + 2*db) > 0)
				{
					ctx2.beginPath();
					ctx2.rect(0, 0, 2*db + x - 1/scale, 2*db + y - 1/scale);
					ctx2.fillStyle = "white";
					ctx2.fill();

					ctx2.beginPath();
					ctx2.rect(0, 0, 2*d + x, 2*d + y);
					if (curr_point[2] == curr_points[i][2] || old_curr_point[2] == curr_points[i][2])
						ctx2.fillStyle = "green";
					else if (check_curr[i] == 0)
						ctx2.fillStyle = "red";
					else ctx2.fillStyle = "blue";
					ctx2.fill();
				}
			} else
			{
				ctx2.font = (15/scale).toString() + "px Verdana";
				if (curr_point[2] == curr_points[i][2] || old_curr_point[2] == curr_points[i][2])
					ctx2.fillStyle = "green";
				else if (check_curr[i] == 0)
					ctx2.fillStyle = "red";
				else ctx2.fillStyle = "blue";
				ctx2.fillText(curr_points[i][2], x, y);
			}
		}
	}

	function load()
	{
		save = 0;
		$.when(upload_curr_json(), upload_pre_json()).done(function(a1, a2){
			curr_points = JSON.parse(a1[2].responseText);
			check_curr = Array.apply(null, Array(curr_points.length)).map(Number.prototype.valueOf,0);
			pre_points = JSON.parse(a2[2].responseText);
			check_pre = Array.apply(null, Array(pre_points.length)).map(Number.prototype.valueOf,0);
			matched();
			save = 1;
			change = 1;
		});
	}

	function matched()
	{
		var i;
		var j;
		for (i = 0; i < curr_points.length; i++)
			for (j = 0; j < pre_points.length; j++)
				if (curr_points[i][2] == pre_points[j][2])
				{
					check_curr[i] = 1;
					check_pre[j] = 1;
					break;
				}
		updateCanvas1();
		updateCanvas2();
	}

	function upload_curr_json()
	{
		return $.post({ 
				headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
		       	type: "POST", 
		       	cache: false,
		       	url: "/upload_json", 
		       	data: { frame_index: curr_frame},
		       	dataType: 'json'
			}); 
	}

	function upload_pre_json()
	{
		return $.post({ 
				headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
		       	type: "POST", 
		       	cache: false,
		       	url: "/upload_json", 
		       	data: { frame_index: Number(curr_frame) - 1},
		       	dataType: 'json'
			}); 
	}

	function save_json()
	{
		if (curr_points.length != 0)
			{
				$.post({ 
					headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
			       	type: "POST", 
			       	url: "/save_json", 
			       	data: { max_id: max_id, frame_index: curr_frame, points: curr_points},
			       	dataType: 'json'
				}); 
			}
	}

	function change_img_input()
	{
		get_index = document.getElementById("frame_index").value;
		if (get_index != null)
		{
			document.getElementById("frame_index").value = null;
			curr_frame = Math.round(get_index);
			change_img();
		}
	}

	function change_img()
	{
		path = "{{URL::asset('dataset/'. $curr_video->name)}}";
		document.getElementById("curr_frame").innerHTML = curr_frame;
		var new_img1_src = "";
		var new_img2_src = "";
		if (Number(curr_frame) < Number(1))
		{
			new_img1_src = new_img1_src.concat(path,"/",(Number("{{$curr_video->num_frame}}")-1).toString(),".jpg");
			new_img2_src = new_img2_src.concat(path,"/","{{$curr_video->num_frame}}",".jpg");
			document.getElementById("curr_frame").innerHTML = "{{$curr_video->num_frame}}";
			curr_frame = Number("{{$curr_video->num_frame}}");
			load();
		}
		else if (Number(curr_frame) > Number("{{$curr_video->num_frame}}"))
		{
			new_img1_src = new_img1_src.concat(path,"/","0",".jpg");
			new_img2_src = new_img2_src.concat(path,"/","1",".jpg");
			document.getElementById("curr_frame").innerHTML = 1;
			curr_frame = 1;
			load();
		}
		else 
		{
			new_img1_src = new_img1_src.concat(path,"/",(Number(curr_frame)-1).toString(),".jpg");
			new_img2_src = new_img2_src.concat(path,"/",curr_frame.toString(),".jpg");
			load();
		}
		document.getElementById("img1").src = new_img1_src;
		document.getElementById("img2").src = new_img2_src;
		document.getElementById("pre_image").innerHTML = "".concat("Previson frame: ",(Number(curr_frame)-1).toString(),".jpg");
		document.getElementById("curr_image").innerHTML = "".concat("Current frame: ",(curr_frame).toString(),".jpg");
		document.getElementById("frame_index").placeholder  = "".concat((curr_frame).toString(),"/","{{$curr_video->num_frame}}");
		images = wheelzoom(document.querySelectorAll('img'), {zoom: 0.1, maxZoom: 10});
		xShift = 0; yShift = 0; scale = img1.naturalWidth/img1.clientWidth;
		init_point();
	}

	function delete_before()
	{
		if (Number(curr_point[2]) != -1)
		{
			var result = confirm( "DELETE BEFORE:\nDo you want to do this?\nThis will affect another output files and you can't go back!\nBe careful!" );
			if (result)
			{
				change = 0;
				$.post({ 
					headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
			       	type: "POST", 
			       	url: "/delete_before", 
			       	data: { max_id: max_id, frame_index: curr_frame, delete_id: Number(curr_point[2]), points: curr_points},
			       	dataType: 'json',
			       	complete: function(a) {
			       		max_id = JSON.parse(a.responseText);
			       		load();
			       	}
				}); 
			}
		}
	}

	function delete_after()
	{
		if (Number(curr_point[2]) != -1)
		{
			var result = confirm( "DELETE AFTER:\nDo you want to do this?\nThis will affect another output files and you can't go back!\nBe careful!" );
			if (result)
			{
				change = 0; save = 0;
				$.post({ 
					headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
			       	type: "POST", 
			       	url: "/delete_after", 
			       	data: { max_id: max_id, frame_index: curr_frame, delete_id: Number(curr_point[2]), points: curr_points},
			       	dataType: 'json',
			       	complete: function(a) {
			       		max_id = JSON.parse(a.responseText);
			       		load();
			       	}
				}); 
			}
		}
	}

	function how_to_use()
	{
		alert("HOW TO USE\nLeft Click: Add new point in current frame\nRight Click: Select point\nCtrl + Right Click: Move selected point in current frame to new position\nKeyS: Save\nKeyM: Match id of selected point in current frame and previous frame\nKeyI: Show and unshow id instead of rectangle\nKeyA/KeyB: Delete points in all after/before files have same id with selected point in current frame\nEscape: Unselected points\nArrowLeft/ArrowRight: Move to previous/next frame");
	}

	function checksave(e)
	{
		change = 0;
		if (save == 1)
		{
			$.when(upload_curr_json()).done(function(a1){
				check_curr_points = a1;
				if(JSON.stringify(check_curr_points)==JSON.stringify(curr_points)) 
				{
					change = 1;
					if (e.code == "ArrowRight")
						curr_frame += 1;
					else if (e.code == "ArrowLeft")
						curr_frame -= 1;
					change_img();
				}
				else
				 	alert("You haven't saved yet! Please press keyS to save before you change image");
			});
		}
	}

</script>

@endsection
