<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Genre;
use App\Models\Country;
use App\Models\Movie;
use App\Models\Episode;
use App\Models\Movie_Genre;
use App\Models\Rating;
use App\Models\Info;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

use DB;

class IndexController extends Controller
{
    public function locphim(){
    
        //get
        $order = $_GET['order'];
        $genre_get = $_GET['genre'];
        $country_get = $_GET['country'];
        $year_get = $_GET['year'];
        
        if($order=='' && $genre_get=='' && $country_get=='' && $year_get==''){
            return redirect()->back();
        }else{
            $meta_title = "Lọc theo phim.";
            $meta_description = "Lọc theo phim.";
            $meta_image = "";
        
            //lay du lieu
            $movie_array = Movie::withCount('episode'); // lấy ra phim và đếm số tập


            // if($genre_get){
            //     $movie = $movie->where('genre_id', '=',$genre_get);
            // }
            if($country_get){
                $movie_array = $movie_array->where('country_id',$country_get);
            }
            if($year_get){
                $movie_array = $movie_array->where('year',$year_get);
            }
            if($order){
                $movie_array = $movie_array->orderby($order, 'DESC');
            }
            $movie_array = $movie_array->with('movie_genre'); // id 247 genre 26 18 39

            $movie = array();
            foreach($movie_array as $mov){ 
                foreach($mov->movie_genre as $mov_gen){  // dùng để liệt kê tất cả genre thuộc id phim đó
                    $movie = $movie_array->whereIn('genre_id',[$mov_gen->genre_id]);
                }
            }
            $movie = $movie_array->paginate(40);
            return view('pages.locphim', compact('movie', 'meta_title','meta_description','meta_image'));
        }
        
    }
    public function timkiem(){
        if(isset($_GET['search'])){
            $search = $_GET['search'];
            
            $movie = Movie::withCount('episode')->where('title','LIKE', '%'.$search.'%')->orderBy('ngaycapnhat','DESC')->paginate(40);
    	    return view('pages.timkiem', compact('search','movie'));
        }else{
            return redirect()->to('/');
        }

        
    }

    public function home(){
        
        $phimhot = Movie::withCount('episode')->where('phim_hot',1)->where('status',1)->orderBy('ngaycapnhat','DESC')->get();
         // nested trong laravel
        $category_home = Category::with(['movie' => function($q){
                                                                    $q->withCount('episode')->where('status',1); 
                                                                } 
                                        ])->orderBy('position','ASC')->where('status',1)->get();
        
        
    	return view('pages.home', compact('category_home','phimhot'));
    }
    public function category($slug){
    

        $cate_slug = Category::where('slug',$slug)->first();
        $movie = Movie::withCount('episode')->where('category_id',$cate_slug->id)->orderBy('ngaycapnhat','DESC')->paginate(40);
    	return view('pages.category', compact('cate_slug','movie'));
    }
    public function year($year){
        

        $year = $year;
        $movie = Movie::withCount('episode')->where('year',$year)->orderBy('ngaycapnhat','DESC')->paginate(40);
    	return view('pages.year', compact('year','movie'));
    }
    public function tag($tag){
    

        $tag = $tag;
        $movie = Movie::withCount('episode')->where('tags','LIKE','%'.$tag.'%')->orderBy('ngaycapnhat','DESC')->paginate(40);
    	return view('pages.tag', compact('tag','movie'));
    }
    public function genre($slug){
        

        $genre_slug = Genre::where('slug',$slug)->first();
        // nhiều thể loại
        $movie_genre = Movie_Genre::where('genre_id', $genre_slug->id)->get();
        $many_genre = [];
        foreach($movie_genre as $key => $movi){
            $many_genre [] = $movi->movie_id;
        }
        
        $movie = Movie::withCount('episode')->whereIn('id',$many_genre)->orderBy('ngaycapnhat','DESC')->paginate(40);
    	return view('pages.genre', compact('genre_slug','movie'));
    }
    public function country($slug){
        

        $country_slug = Country::where('slug',$slug)->first();
        $movie = Movie::withCount('episode')->where('country_id',$country_slug->id)->orderBy('ngaycapnhat','DESC')->paginate(40);
    	return view('pages.country', compact('country_slug','movie'));
    }
    public function movie($slug){
    
        $check = Auth::user();
        if($check == null){
            $user=null;
        }else{
            $user = User::where('id',Auth::user()->id)->first();
        }
        $movie = Movie::with('category','genre','country', 'movie_genre')->where('slug',$slug)->where('status',1)->first();
        // lay tập 1
        $episode_tapdau = Episode::with('movie')->where('movie_id', $movie->id)->orderBy('episode', 'ASC')->take(1)->first();
        $related = Movie::with('category','genre','country')->where('category_id',$movie->category->id)->orderBy(DB::raw('RAND()'))->whereNotIn('slug',[$slug])->get();
        // lấy 3 tập gần nhất
        $episode = Episode::with('movie')->where('movie_id', $movie->id)->orderBy('episode', 'DESC')->take(3)->get();
        // lấy tổng tập phim đã thêm
        $episode_current_list = Episode::with('movie')->where('movie_id', $movie->id)->get();
        $episode_current_list_count = $episode_current_list->count();
        // rating movie
        $rating = Rating::where('movie_id', $movie->id)->avg('rating');
        $rating = round($rating);
        $count_total = Rating::where('movie_id', $movie->id)->count();
        //increase movie views
        $count_views = $movie->count_views;
        $count_views = $count_views + 1;
        $movie->count_views = $count_views;
        $movie->save();

    	return view('pages.movie', compact('movie','related', 'episode', 'episode_tapdau', 'episode_current_list_count', 'rating', 'count_total','user'));
    }
    public function add_rating(Request $request){
        $data = $request->all();
        $ip_address = $request->ip();

        $rating_count = Rating::where('movie_id', $data['movie_id'])->where('ip_address',$ip_address)->count();
        if($rating_count>0){
            echo'exist';
        }else{
            $rating = new Rating();
            $rating->movie_id = $data['movie_id'];
            $rating->rating = $data['index'];
            $rating->ip_address = $ip_address;
            $rating->save();
            echo 'done';
        }
    }
    public function watch($slug,$tap){
        
        $movie = Movie::with('category','genre', 'movie_genre', 'country', 'episode')->where('slug',$slug)->where('status',1)->first();
        $related = Movie::with('category','genre','country')->where('category_id',$movie->category->id)->orderBy(DB::raw('RAND()'))->whereNotIn('slug',[$slug])->get();
        // ;lấy tập 1 - FullHD
        if(isset($tap)){
            $tapphim = $tap;
            $tapphim = substr($tap, 4, 20);
            $episode = Episode::where('movie_id', $movie->id)->where('episode', $tapphim)->first();
        }else{
            $tapphim = 1;
            $episode = Episode::where('movie_id', $movie->id)->where('episode', $tapphim)->first();
        }
        $check = Auth::user();
        if($check == null){
            $user=null;
        }else{
            $user = User::where('id',Auth::user()->id)->first();
        }

        if($episode->vip == 1 && $user->vip ==0){
            return redirect()->back();
        }
    	return view('pages.watch', compact('movie', 'episode', 'tapphim', 'related','user'));
    }
    public function episode(){
    	return view('pages.episode');
    }
}
