<?php

namespace le_54ba\MovieSeeder\App;

use Illuminate\Database\Eloquent\Model;
use \le_54ba\MovieSeeder\App\Movie;

class Category extends Model
{
	protected $guarded = [];

    public function movies()
    {
    	return $this->belongsToMany(Movie::class,'movies_categories'); 
    }
}
