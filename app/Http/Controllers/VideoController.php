<?php

namespace App\Http\Controllers;

use App\User;
use App\Video;
use App\Setting;
use ZipArchive;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class VideoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        if(Auth::user()->role->name != 'admin'){
            abort(403, 'You do not have permission');
        }
        return view('videos.index',['videos' => Video::all()]); 
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        if(Auth::user()->role->name != 'admin'){
            abort(403, 'You do not have permission');
        }
        return view('videos.create',['users' => User::all()]); 
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        if(Auth::user()->role->name != 'admin'){
            abort(403, 'You do not have permission');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:videos',
            'data_zip_file' => 'required|mimes:zip',
            'json_zip_file' => 'mimes:zip',
            'max_id' => 'required|integer|gte:0',
            'num_frame' => 'required|integer|gte:0'
        ]);

        $video = new Video;
        $video->name = $request->name;
        $video->num_frame = $request->num_frame;
        $video->max_id = $request->max_id;
        if ($request->user_id != "null")
            $video->user_id = $request->user_id;
        else $video->user_id = null;
        $video->save();

        if ($request->hasFile('data_zip_file')) {
            $data_path = Setting::get("dataset_path");
            $data_path = $data_path . "/" .  (string)$video->name;
            mkdir($data_path);
            $zip_name =  (string)$request->data_zip_file->getClientOriginalName();
            $path = $request->data_zip_file->storeAs($data_path, $zip_name,'my_local');
            shell_exec("cd $data_path; unzip  $zip_name");
            shell_exec("cd $data_path; rm -rf $zip_name");
        }

        if ($request->hasFile('json_zip_file')) {
            $data_path = Setting::get("output_path");
            $data_path = $data_path . "/" .  (string)$video->name;
            mkdir($data_path);
            $zip_name =  (string)$request->json_zip_file->getClientOriginalName();
            $path = $request->json_zip_file->storeAs($data_path, $zip_name,'my_local');
            shell_exec("cd $data_path; unzip  $zip_name");
            shell_exec("cd $data_path; rm -rf $zip_name");
        }

        return redirect('videos');
    }

    public function upload_more_file($video_id)
    {
        //
        if(Auth::user()->role->name != 'admin'){
            abort(403, 'You do not have permission');
        }

        $video = Video::find($video_id);

        return view('videos.upload_more_file',['video' => $video, 'users' => User::all()]); 
    }

    public function upload_more_file_store(Request $request)
    {
        //
        if(Auth::user()->role->name != 'admin'){
            abort(403, 'You do not have permission');
        }

        if ($request->hasFile('data_zip_file')) {
            $data_path = Setting::get("dataset_path");
            $data_path = $data_path . "/" .  (string)$request->name;
            $zip_name =  (string)$request->data_zip_file->getClientOriginalName();
            $path = $request->data_zip_file->storeAs($data_path, $zip_name,'my_local');
            shell_exec("cd $data_path; unzip  $zip_name");
            shell_exec("cd $data_path; rm -rf $zip_name");
        }

        if ($request->hasFile('json_zip_file')) {
            $data_path = Setting::get("output_path");
            $data_path = $data_path . "/" .  (string)$request->name;
            $zip_name =  (string)$request->json_zip_file->getClientOriginalName();
            $path = $request->json_zip_file->storeAs($data_path, $zip_name,'my_local');
            shell_exec("cd $data_path; unzip  $zip_name");
            shell_exec("cd $data_path; rm -rf $zip_name");
        }
        return redirect('videos');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        if(Auth::user()->role->name != 'admin'){
            abort(403, 'You do not have permission');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Video $video)
    {
        //
        if(Auth::user()->role->name != 'admin'){
            abort(403, 'You do not have permission');
        }

        return view('videos.edit',['video' => $video, 'users' => User::all()]); 
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Video $video)
    {
        //
        if(Auth::user()->role->name != 'admin'){
            abort(403, 'You do not have permission');
        }

        $validated = $request->validate([
            'data_zip_file' => 'mimes:zip',
            'json_zip_file' => 'mimes:zip',
            'max_id' => 'required|integer|gte:'. $video->max_id,
            'num_frame' => 'required|integer|gte:0'
        ]);

        $video->num_frame = $request->num_frame;
        $video->max_id = $request->max_id;
        if ($request->user_id != "null")
            $video->user_id = $request->user_id;
        else $video->user_id = null;
        $video->save();

        if ($request->hasFile('data_zip_file')) {
            $data_path = Setting::get("dataset_path");
            $data_path = $data_path . "/" .  (string)$video->name;
            shell_exec("rm -rf $data_path");
            mkdir($data_path);
            $zip_name =  (string)$request->data_zip_file->getClientOriginalName();
            $path = $request->data_zip_file->storeAs($data_path, $zip_name,'my_local');
            shell_exec("cd $data_path; unzip  $zip_name");
            shell_exec("cd $data_path; rm -rf $zip_name");
        }

        if ($request->hasFile('json_zip_file')) {
            $data_path = Setting::get("output_path");
            $data_path = $data_path . "/" .  (string)$video->name;
            shell_exec("rm -rf $data_path");
            mkdir($data_path);
            $zip_name =  (string)$request->json_zip_file->getClientOriginalName();
            $path = $request->json_zip_file->storeAs($data_path, $zip_name,'my_local');
            shell_exec("cd $data_path; unzip  $zip_name");
            shell_exec("cd $data_path; rm -rf $zip_name");
        }

        return redirect('videos');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        if(Auth::user()->role->name != 'admin'){
            abort(403, 'You do not have permission');
        }
    }

    public function delete($video_id)
    {
        if(Auth::user()->role->name != 'admin'){
            abort(403, 'You do not have permission');
        }
        $video = Video::find($video_id);

        $data_path = Setting::get("dataset_path");
        $data_path = $data_path . "/" .  (string)$video->name;
        shell_exec("rm -rf $data_path");

        $data_path = Setting::get("output_path");
        $data_path = $data_path . "/" .  (string)$video->name;
        shell_exec("rm -rf $data_path");
        $video->delete();
        return redirect('videos'); 
    }

    /**
     * Anntotate video
     *
     * @param  \App\Video  $video
     * @return \Illuminate\Http\Response
     */
    public function annotate($video_id, $curr_frame = 1)
    {
        $curr_video = Video::find($video_id);
        if ($curr_video == null) 
            abort(403,'Video not found');
        if ($curr_video->user_id != Auth::user()->id && !in_array( Auth::user()->role->name, ['admin']))
            abort(403,'You do not have permission to annotate this video');
        Auth::user()->selected_video_id = $video_id;
        Auth::user()->save(); 
        return view('videos.annotate',['curr_video' => $curr_video , 'curr_frame' => $curr_frame]); 
    }
    
    public function download($video_id)
    {
        $video = Video::find($video_id);
        $data_path = Setting::get("output_path");
        $data_path = $data_path . "/" .  (string)$video->name;
        if (!file_exists($data_path)) 
            return redirect('videos'); 

        $zip_name = $data_path . "/" . (string)$video->name . ".zip";
        shell_exec("rm $zip_name");
        
        $zip = new ZipArchive();
        $zip->open($zip_name, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($data_path),
            RecursiveIteratorIterator::LEAVES_ONLY
        );
        foreach ($files as $name => $file)
        {
            if (!$file->isDir())
            {
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($data_path) + 1);

                $zip->addFile($filePath, $relativePath);
            }
        }

        $zip->close();
        return Storage::disk("public")->download((string)$video->name . "/" . (string)$video->name . ".zip");
    }

    // Function for annotate blade
    public function save_json()
    {
        $max_id = json_decode($_POST['max_id']);
        $curr_video = Video::find(Auth::user()->selected_video_id);
        $curr_video->max_id = $max_id;
        $curr_video->save();

        $frame_index = json_decode($_POST['frame_index']);
        $points = $_POST['points'];
        $path_json = (string)Video::find(Auth::user()->selected_video_id)->name . "/" . (string)$frame_index . ".json";
        Storage::disk('public')->put($path_json, response()->json($points)->getContent());
    }

    public function upload_json()
    {
        $frame_index = json_decode($_POST['frame_index']);
        $path_json = (string)Video::find(Auth::user()->selected_video_id)->name . "/" . (string)$frame_index . ".json";
        if (Storage::disk('public')->exists($path_json) == true)
            $curr_points = Storage::disk('public')->get($path_json);
        else $curr_points = "[]";
        return($curr_points);
    }

    public function search_id($points, $delete_id)
    {
        foreach ($points as $id => $point)
            if ((int)$point[2] == (int)$delete_id)
                return $id;
        return -1;
    }

    public function delete_array($num)
    {
        $max_id = json_decode($_POST['max_id']);
        $frame_index = json_decode($_POST['frame_index']);
        $curr_frame = $frame_index;

        $points = $_POST['points'];
        $path_json = (string)Video::find(Auth::user()->selected_video_id)->name . "/" . (string)$frame_index . ".json";
        Storage::disk('public')->put($path_json, response()->json($points)->getContent());

        $delete_id = json_decode($_POST['delete_id']);
        $check = true;
        while ($check)
        {
            $path_json = (string)Video::find(Auth::user()->selected_video_id)->name . "/" . (string)$frame_index . ".json";
            if (Storage::disk('public')->exists($path_json) == true)
            {
                $curr_points = Storage::disk('public')->get($path_json);
                $curr_points = json_decode($curr_points);
                $id = $this->search_id($curr_points, $delete_id);
                if ($id != -1)
                {
                    if ($frame_index == $curr_frame)
                        array_splice($curr_points, $id, 1);
                    else 
                    {
                        $max_id = $max_id + 1;
                        $curr_points[$id][2] = (string)$max_id;
                    }
                    Storage::disk('public')->put($path_json, response()->json($curr_points)->getContent());
                    $frame_index = (int)$frame_index + (int)$num;
                }
                else $check = false;
            }
            else $check = false;
        } 

        $curr_video = Video::find(Auth::user()->selected_video_id);
        $curr_video->max_id = $max_id;
        $curr_video->save();
        return $max_id;    
    }

    public function delete_after()
    {
        $max_id = $this->delete_array(1);  
        return $max_id;
    }

    public function delete_before()
    {
        $max_id = $this->delete_array(-1);  
        return $max_id;
    }


}
