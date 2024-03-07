<?php


namespace App\Http\Controllers;

use File;
use Illuminate\Http\Request;
use App\Models\Movie;
use App\Models\Category;
use App\Models\Genre;
use App\Models\Country;
use App\Models\Episode;
use App\Models\Movie_Genre;
use App\Models\Info;
use Carbon\Carbon;
use Storage;



class MovieController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function category_choose(Request $request){
        $data = $request->all();
        $movie = Movie::find($data['movie_id']);
        $movie->category_id = $data['category_id'];
        $movie->save();
    }
    public function country_choose(Request $request){
        $data = $request->all();
        $movie = Movie::find($data['movie_id']);
        $movie->country_id = $data['country_id'];
        $movie->save();
    }
    public function phimhot_choose(Request $request){
        $data = $request->all();
        $movie = Movie::find($data['movie_id']);
        $movie->phim_hot = $data['phimhot_val'];
        $movie->save();
    }
    public function phude_choose(Request $request){
        $data = $request->all();
        $movie = Movie::find($data['movie_id']);
        $movie->phude = $data['phude_val'];
        $movie->save();
    }
    public function trangthai_choose(Request $request){
        $data = $request->all();
        $movie = Movie::find($data['movie_id']);
        $movie->status = $data['trangthai_val'];
        $movie->save();
    }
    public function thuocphim_choose(Request $request){
        $data = $request->all();
        $movie = Movie::find($data['movie_id']);
        $movie->thuocphim = $data['thuocphim_val'];
        $movie->save();
    }
    public function resolution_choose(Request $request){
        $data = $request->all();
        $movie = Movie::find($data['movie_id']);
        $movie->resolution = $data['resolution_val'];
        $movie->save();
    }
    public function update_image_movie_ajax(Request $request){
        $get_image = $request->file('file');
        $movie_id = $request->movie_id;
        if($get_image){
            //xóa ảnh cũ
            $movie = Movie::find($movie_id);
            unlink('uploads/movie/'.$movie->image);
            // thêm ảnh mới
            $get_name_image = $get_image->getClientOriginalName(); // lấy cái tên phương thức, chứa tất cả dữ liệu name . jpg
            $name_image = current(explode('.',$get_name_image)); // lấy tên hình ảnh name
            $new_image = $name_image.rand(0,99).'.'.$get_image->getClientOriginalExtenstion(); // tạo tên ảnh mới name-moi . jpg
            $get_image->move('uploads/movie/',$new_image);
            $movie->image = $new_image;
            $movie->save();
        }
    }
    public function index()
    {
        $list = Movie::with('category','movie_genre','country', 'genre')->withCount('episode')->orderBy('id','DESC')->get();
        
        $category = Category::pluck('title', 'id');
        $country = Country::pluck('title', 'id');
        $path = public_path()."/json_file/";

        if(!is_dir($path)){
            mkdir($path,0777, true);
        }
        File::put($path.'movies.json',json_encode($list));

        return view('admincp.movie.index', compact('list', 'category', 'country'));
    }

    public function update_year(Request $request){
        $data = $request->all();
        $movie = Movie::find($data['id_phim']);
        $movie->year = $data['year'];
        $movie->save();
    }
    public function update_season(Request $request){
        $data = $request->all();
        $movie = Movie::find($data['id_phim']);
        $movie->year = $data['season'];
        $movie->save();
    }
    public function update_topview(Request $request){
        $data = $request->all();
        $movie = Movie::find($data['id_phim']);
        $movie->topview = $data['topview'];
        $movie->save();
    }
    public function filter_topview(Request $request){
        $data = $request->all();
        $movie = Movie::where('topview',$data['value'])->orderBy('ngaycapnhat','DESC')->take(20)->get();
        
        $output = '';
        foreach($movie as $key => $mov){
            if($mov->resolution==0){
                $text = 'HD';
            }elseif($mov->resolution==1){
                $text = 'SD';
            }elseif($mov->resolution==2){
                $text = 'HDCam';
            }
            elseif($mov->resolution==3){
                $text = 'Cam';
            }elseif($mov->resolution==4){
                $text = 'FullHD';
            }else{
                $text = 'Trailer';
            }
            $output.=' <div class="item">
                        <a href="'.url('phim/'.$mov->slug).'" title="'.$mov->title.'">
                            <div class="item-link">
                                <img src="'.url('uploads/movie/'.$mov->image).'" class="lazy post-thumb" alt="'.$mov->title.'" title="'.$mov->title.'" />
                                <span class="is_trailer">
                                '.$text.'
                                </span>
                            </div>
                            <p class="title">'.$mov->title.'</p>
                        </a>
                        <div class="viewsCount" style="color: #9d9d9d;">';
                            if($mov->count_views>0){
                                $mov->count_views; $output.='lượt quan tâm';
                            }else{
                                echo rand(100,99999);
                                $output.='lượt quan tâm';
                            }
                            $output.='</div>
                        <div class="viewsCount" style="color: #9d9d9d;">'.$mov->year.'</div>
                            <div style="float:left;">
                            <ul class="list-inline rating" title="Average Rating">';
                                for($count=1; $count<=5; $count++){
                                    $output.='<li title="star_rating" style="font-size:20px;color:#ffcc00;padding:0">&#9733</li>';
                                }
                                $output.='<ul class="list-inline rating" title="Average Rating"></ul>
                            </ul>
                        </div>
                        <div style="float: left;">
                            <span class="user-rate-image post-large-rate stars-large-vang" style="display: block;/* width: 100%; */">
                            <span style="width: 0%"></span>
                            </span>
                        </div>
                    </div>';
        }
        echo $output;
    }

    public function filter_default(Request $request){
        $data = $request->all();
        $movie = Movie::where('topview',0)->orderBy('ngaycapnhat','DESC')->take(20)->get();
        $output = '';
        foreach($movie as $key => $mov){
            if($mov->resolution==0){
                $text = 'HD';
            }elseif($mov->resolution==1){
                $text = 'SD';
            }elseif($mov->resolution==2){
                $text = 'HDCam';
            }
            elseif($mov->resolution==3){
                $text = 'Cam';
            }elseif($mov->resolution==4){
                $text = 'FullHD';
            }else{
                $text = 'Trailer';
            }
            $output.=' <div class="item">
                        <a href="'.url('phim/'.$mov->slug).'" title="'.$mov->title.'">
                            <div class="item-link">
                                <img src="'.url('uploads/movie/'.$mov->image).'" class="lazy post-thumb" alt="'.$mov->title.'" title="'.$mov->title.'" />
                                <span class="is_trailer">
                                '.$text.'
                                </span>
                            </div>
                            <p class="title">'.$mov->title.'</p>
                        </a>
                        <div class="viewsCount" style="color: #9d9d9d;">';
                            if($mov->count_views>0){
                                $mov->count_views; $output.='lượt quan tâm';
                            }else{
                                echo rand(100,99999);
                                $output.='lượt quan tâm';
                            }
                            $output.='</div>
                        <div class="viewsCount" style="color: #9d9d9d;">'.$mov->year.'</div>
                            <div style="float:left;">
                            <ul class="list-inline rating" title="Average Rating">';
                                for($count=1; $count<=5; $count++){
                                    $output.='<li title="star_rating" style="font-size:20px;color:#ffcc00;padding:0">&#9733</li>';
                                }
                                $output.='<ul class="list-inline rating" title="Average Rating"></ul>
                            </ul>
                        </div>
                        <div style="float: left;">
                            <span class="user-rate-image post-large-rate stars-large-vang" style="display: block;/* width: 100%; */">
                            <span style="width: 0%"></span>
                            </span>
                        </div>
                    </div>';
        }
        echo $output;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $category = Category::pluck('title','id');
        $genre = Genre::pluck('title','id');
        $list_genre = Genre::all();
        $country = Country::pluck('title','id');
        return view('admincp.movie.form', compact('category','genre','country', 'list_genre'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $movie = new Movie();
        $movie->title = $data['title'];
        $movie->trailer = $data['trailer'];
        $movie->sotap = $data['sotap'];
        $movie->tags = $data['tags'];
        $movie->thoiluong = $data['thoiluong'];
        $movie->phude = $data['phude'];
        $movie->resolution = $data['resolution'];
        $movie->slug = $data['slug'];
        $movie->name_eng = $data['name_eng'];
        $movie->phim_hot = $data['phim_hot'];
        $movie->description = $data['description'];
        $movie->status = $data['status'];
        $movie->category_id = $data['category_id'];
        $movie->thuocphim = $data['thuocphim'];

        $movie->country_id = $data['country_id'];
        $movie->count_views = rand(100,99999);
        $movie->ngaytao = Carbon::now('Asia/Ho_Chi_Minh');
        $movie->ngaycapnhat = Carbon::now('Asia/Ho_Chi_Minh');
        // thêm nhiều thể loại phim
        foreach($data['genre'] as $key => $gen){
            $movie->genre_id = $gen[0];
        }
        
    
        

        $get_image = $request->file('image');

        if($get_image){

            $get_name_image = $get_image->getClientOriginalName();
            $name_image = current(explode('.',$get_name_image));
            $new_image = $name_image.rand(0,9999).'.'.$get_image->getClientOriginalExtension();
            $get_image->move('uploads/movie/',$new_image);
            $movie->image = $new_image;
        }
        $movie->save();
        // thêm nhiều thể loại cho phim
        $movie->movie_genre()->sync($data['genre']);


        return redirect()->route('movie.index');
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
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $category = Category::pluck('title','id');
        $genre = Genre::pluck('title','id');
        $country = Country::pluck('title','id');
        $list_genre = Genre::all();
        $movie =  Movie::find($id);
        $movie_genre = $movie->movie_genre;
        return view('admincp.movie.form', compact('category','genre','country','movie', 'list_genre', 'movie_genre'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->all();
        $movie = Movie::find($id);
        $movie->resolution = $data['resolution'];
        $movie->trailer = $data['trailer'];
        $movie->sotap = $data['sotap'];
        $movie->tags = $data['tags'];
        $movie->thoiluong = $data['thoiluong'];
        $movie->phude = $data['phude'];
        $movie->title = $data['title'];
        $movie->slug = $data['slug'];
        $movie->name_eng = $data['name_eng'];
        $movie->phim_hot = $data['phim_hot'];
        $movie->description = $data['description'];
        $movie->status = $data['status'];
        $movie->category_id = $data['category_id'];
        $movie->thuocphim = $data['thuocphim'];
        
        $movie->country_id = $data['country_id'];
       // $movie->count_views = rand(100,99999);
        $movie->ngaycapnhat = Carbon::now('Asia/Ho_Chi_Minh');

        foreach($data['genre'] as $key => $gen){
            $movie->genre_id = $gen[0];
        }

        $get_image = $request->file('image');

        if($get_image){
            if(file_exists('uploads/movie/'.$movie->image)){
                unlink('uploads/movie/'.$movie->image);
            }else{
                $get_name_image = $get_image->getClientOriginalName();
                $name_image = current(explode('.',$get_name_image));
                $new_image = $name_image.rand(0,9999).'.'.$get_image->getClientOriginalExtension();
                $get_image->move('uploads/movie/',$new_image);
                $movie->image = $new_image;
            }
        }
        $movie->save();
        $movie->movie_genre()->sync($data['genre']);
        return redirect()->route('movie.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $movie = Movie::find($id);
        // xóa ảnh
        if(file_exists('uploads/movie/'.$movie->image)){
            unlink('uploads/movie/'.$movie->image);
        }
        
        //xóa thể loại
        Movie_Genre::whereIn('movie_id',[$movie->id])->delete();
        
        // xóa tập phim
        Episode::whereIn('movie_id',[$movie->id])->delete();
        $movie->delete();

        return redirect()->back();
    }
    public function watch_video(Request $request)
    {
        $data = $request->all();
        $movie = Movie::find($data['movie_id']);
        $video = Episode::where('movie_id',$data['movie_id'])->where('episode',$data['episode_id'])->first();

        $output['video_title'] = $movie->title.'- tập '.$video->episode;
        $output['video_desc'] = $movie->description;

        $output['video_link'] = $video->linkphim;

        echo json_encode($output);
    }
}
