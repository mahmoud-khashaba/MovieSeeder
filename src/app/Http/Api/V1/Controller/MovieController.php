<?php

namespace le_54ba\MovieSeeder\App\Http\Api\V1\Controller;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use \le_54ba\MovieSeeder\App\Http\Api\V1\Requests\MovieRequest;
use \le_54ba\MovieSeeder\App\Movie;

class MovieController extends Controller
{

    public function index(MovieRequest $request)
    {
		$validated = $request->validated();
		$movies = (new Movie)->listMovies($validated);
		return response()->json($movies);
    }

}
