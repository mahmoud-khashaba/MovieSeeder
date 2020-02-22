<?php

namespace le_54ba\MovieSeeder\App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Tmdb\Repository\MovieRepository;
use \le_54ba\MovieSeeder\App\Movie;
use \le_54ba\MovieSeeder\App\Category;
use \le_54ba\MovieSeeder\App\JobPersist;

class SeedMoviesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $movies;
    private $records_count;
    private $per_page = 20;
    private $current_page = 1;
    private $movies_to_seed = [];
    private $have = [];
    private $need = [];
    private $next_page_movies = [];
    private $previous_remaining = 0;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->records_count = config('MovieSeeder.num_of_records');
        $persisted = JobPersist::latest()->first();
        if(null != $persisted)
        {
            $this->current_page = $persisted->current_page;
            $this->previous_remaining = $persisted->previous_remaining;
            $this->next_page_movies =unserialize($persisted->next_page_movies);    
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(MovieRepository $movies)
    {
      //previous_remaining is count of results in the next $next_page_movies (i have how much)
        //remaining count is the results needed to make a full query count of the shared page 
        //between the two requests (i need how much)
        $this->movies = $movies;

        $this->have = $this->next_page_movies; 
        $remaining_count = ($this->records_count - $this->previous_remaining ) % $this->per_page ;
        $num_of_pages = ceil(($this->records_count - $this->previous_remaining)/$this->per_page);
        $this->previous_remaining = $this->per_page - $remaining_count;
        $query_num_of_pages = $this->current_page + $num_of_pages;



        for ($i=$this->current_page; $i < $query_num_of_pages ; $i++) { 
            if($i == $query_num_of_pages-1)
            {
               if($remaining_count > 0 )
               {

                $repo = $this->movies->getTopRatedRaw(['page' =>$i])['results'];

                $this->need = array_slice($repo, 0,$remaining_count);
                $this->next_page_movies = array_slice($repo,$remaining_count);
               }
            }else
            {

                $this->movies_to_seed =array_merge($this->movies_to_seed,$this->movies->getTopRatedRaw(['page' =>$i])['results']);

            }

            $this->current_page++;
        }
        if(count($this->need) > 0)
        {
           $this->movies_to_seed = array_merge($this->movies_to_seed,$this->need);
        }
        if(count($this->have) > 0)
        {
           $this->movies_to_seed = array_merge($this->movies_to_seed,$this->have);
        }

        $latestMovie = $this->movies->getLatestRaw();
        
        if(!count(Movie::where('title',$latestMovie['title'])->get()))
        {
            $this->movies_to_seed = array_merge($this->movies_to_seed,[$latestMovie]);
        }
        foreach ($this->movies_to_seed as $raw_movie)
        {

            $movie_object = new Movie ; 
            $movie_object->title = $raw_movie['title'];
            $movie_object->overview = $raw_movie['overview']; 
            $movie_object->adult = $raw_movie['adult'];
            $movie_object->poster_path = $raw_movie['poster_path']; 
            $movie_object->original_title = $raw_movie['original_title']; 
            $movie_object->original_language = $raw_movie['original_language']; 
            $movie_object->popularity = $raw_movie['popularity']; 
            $movie_object->vote_average = $raw_movie['vote_average']; 
            $movie_object->vote_count = $raw_movie['vote_count']; 
            $movie_object->video = $raw_movie['video']; 
            $movie_object->release_date = (empty($raw_movie['release_date']))?null:$raw_movie['release_date'];
            $movie_object->save();
            $movie_object->refresh();
            
            $DB_Genres_ids = [];

            if(array_key_exists('genre_ids',$raw_movie))
            {
                foreach ($raw_movie['genre_ids'] as $genre_id)
                {
                    if(!count($existing_category = Category::where('TMD_category_id',$genre_id)->get()))
                    {
                        $category = (new Category)->create(['TMD_category_id'=>$genre_id]);
                        $DB_Genres_ids[] = $category->id ;

                    }else 
                    {
                        $DB_Genres_ids[] = $existing_category[0]->id;
                    }

                }
            }elseif (array_key_exists('genres', $raw_movie))
            {
                foreach ($raw_movie['genres'] as $genre_id)
                {
                    if(!count($existing_category = Category::where('TMD_category_id',$genre_id['id'])->get()))
                    {
                        $category = (new Category)->create(['TMD_category_id'=>$genre_id['id']]);
                        $DB_Genres_ids[] = $category->id ;

                    }else 
                    {
                        $DB_Genres_ids[] = $existing_category[0]->id;
                    }

                }
            }
            
            $movie_object->categories()->attach($DB_Genres_ids);
        }
        $to_persist_object = new JobPersist();

        $to_persist_object->current_page = $this->current_page;
        $to_persist_object->previous_remaining = $this->previous_remaining;
        $to_persist_object->next_page_movies  = serialize($this->next_page_movies);
        $to_persist_object->save();
    }
}
