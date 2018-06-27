<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\movies;
use Storage;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
Use DB;
Use App\User;
class MoviesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


   public function __construct()
    {
        $this->middleware('auth');
    }


    public function index()
    {  
        $movie = null;
        $id = null;   
        if(User::is_admin()){

            $movies = DB::table('movies2')->get()->toArray();  
        }
        else{

            $movies = DB::table('movies2')->where('email', '=', Auth::user()->email)->get()->toArray();
        }
        return view('movies',compact('movies','movie','id'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        redirect()->route('movies.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
            
        $this->validate($request,['name' => 'required', 'actor' => 'required', 'price' => 'required']);
        
        $entered_path = $request->file('file')->store('public');
        $stored_path = str_replace(config('settings.stored_directory'),config('settings.url_path'),$entered_path);
      
        $movie = new movies([
            'name' => $request->name,
            'price' => $request->price,
            'actor' => $request->actor,
            'file' => $stored_path,
            'email' => Auth::user()->email
        ]);
        $movie->save();
        return redirect()->route('movies.index')->with('success','Movie Added');
    }

    
    public function show($id)
    {
        //
    }

   
    public function edit($id)
    {
        $movie = movies::find($id);
        if(User::is_admin()){

            $movies = DB::table('movies2')->get()->toArray();  
        }

        else{

            $movies = DB::table('movies2')->where('email', '=', Auth::user()->email)->get()->toArray();
        }

        return view('movies',compact('movie','movies','id'));
    }


    public function update(Request $request, $id)
    {

        $this->validate($request,['name' => 'required', 'actor' => 'required', 'price' => 'required']);
        $movie = movies::find($id);

        if($request->file('file')){
          
        $old_file_path = $movie->file;
        Storage::delete($old_file_path);
        $new_file_path = $request->file('file')->store('public');
        $stored_path = str_replace(config('settings.stored_directory'),config('settings.url_path'),$new_file_path);

        }
  
        
        $movie->name = $request->name;
        $movie->price = $request->price;
        $movie->actor = $request->actor;
        if($request->file('file'))
            $movie->file = $stored_path;
        $movie->save();
        return redirect()->route('movies.index')->with('success','Data Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {


        $movies = movies::find($id);
        $storage_path = str_replace(config('settings.url_path'),config('settings.stored_directory'),$movies->file);
        $movies ->delete();
        return redirect()->route('movies.index')->with('success','Movie Deleted sucessfully');;
    }


    


}