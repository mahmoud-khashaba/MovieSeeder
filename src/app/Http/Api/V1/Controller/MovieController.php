<?php

namespace le_54ba\MovieSeeder\App\Http\Api\V1\Controller;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use \le_54ba\MovieSeeder\App\Http\Api\V1\Requests\MovieRequest;
use \le_54ba\MovieSeeder\App\Movie;


use Tmdb\Repository\MovieRepository;
use \le_54ba\MovieSeeder\App\Category;
use \le_54ba\MovieSeeder\App\JobPersist;
class MovieController extends Controller
{

	private $movies;
    private $records_count;
    private $per_page = 20;
    private $current_page = 1;
    private $movies_to_seed = [];
    private $have = [];
    private $need = [];
    private $next_page_movies = [];
    private $previous_remaining = 0;


	 public function __construct($movies)
    {
        $this->movies = $movies;
        $this->records_count = 35;
        $persisted = JobPersist::latest()->first();
        if(null != $persisted)
        {
            $this->current_page = $persisted->current_page;
            $this->previous_remaining = $persisted->previous_remaining;
            $this->next_page_movies =unserialize($persisted->next_page_movies);    
        }
        
    }


    public function index(MovieRequest $request)
    {
		$validated = $request->validated();
		$movies = (new Movie)->listMovies($validated);
		return response()->json($movies);
    }

}
