<?php
/**
 * This file is part of the Tmdb PHP API created by Michael Roterman.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package Tmdb
 * @author Michael Roterman <michael@wtfz.net>
 * @copyright (c) 2013, Michael Roterman
 * @version 0.0.1
 */
namespace Tmdb\Repository;


/**
 * Class MovieRepository
 * @package Tmdb\Repository
 * @see http://docs.themoviedb.apiary.io/#movies
 */
class MovieRepository extends AbstractRepository
{
   
    /**
     * Return the Movies API Class
     *
     * @return \Tmdb\Api\Movies
     */
    public function getApi()
    {
        return $this->getClient()->getMoviesApi();
    }

   

    public function getTopRatedRaw(array $options = [])
    {
            return $this->getApi()->getTopRated($options);

    }

     public function getLatestRaw(array $options = [])
    {
            return $this->getApi()->getLatest($options);

    }
}
