<?php

namespace le_54ba\MovieSeeder\App;

use Illuminate\Database\Eloquent\Model;
use \le_54ba\MovieSeeder\App\Category;

class Movie extends Model
{
    protected $guarded = [];

    public function listMovies($input)
    {
    	$query = $this->select('*');

    	if(null != $input['category_id'])
    	{
    		$query->whereHas('categories', function ($query) use ($input) {
    			$query->where('TMD_category_id', '=', $input['category_id']);
			});
    	}

    	if( array_key_exists('title',$input))
    	{
    		$query->where('title','LIKE','%'.$input['title'].'%');
    	}

    	if( array_key_exists('overview',$input))
    	{
    		$query->where('title','LIKE','%'.$input['overview'].'%');
    	}

    	if( array_key_exists('original_language',$input))
    	{
    		$query->where('title','LIKE','%'.$input['original_language'].'%');
    	}
    	if( array_key_exists('video',$input))
    	{
    		if($input['video'] == 1)
    		{
    			$query->where('video',true);
    		}elseif($input['video'] == 0)
    		{
    		 	$query->where('video',false);
    		}

    	}

    	if( array_key_exists('adult',$input))
    	{
    		if($input['adult'] == 1)
    		{
    			$query->where('adult',true);
    		}elseif($input['adult'] == 0)
    		{
    		 	$query->where('adult',false);
    		}

    	}

    	if( array_key_exists('release_date',$input))
    	{
    		$query->where('release_date', '>=',date($input['release_date']));
    	}

    	$search_keys_mapping = ['popular'=>'popularity','rated'=>'vote_average'];
    	$search_keys = array_filter(array_keys($input),function($key)
    	{
    		return strpos($key, '|');
    	});
    	$search_keys_query = [];
    	foreach ($search_keys as $key) {
    		$pairs = explode('|', $key);
    		$search_keys_query[$pairs[0]] = $pairs[1];
    	}

    	foreach ($search_keys_query as $key => $value) {
            if (null == $value)
            {
                $value = 'asc';
            }
    		$query->orderBy($search_keys_mapping[$key],$value);
    	}

        return $query->get();

    } 

    public function categories()
    {
    	return $this->belongsToMany(Category::class,'movies_categories'); 
    }
}
