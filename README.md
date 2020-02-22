step 1 :
First of all you need to download Laravel version 5.8.x 

```
composer create-project --prefer-dist laravel/laravel MovieSeeder "5.8.*"
```


step 2:
require the movie-seeder package through composer 

```
composer require le_54ba/movie_seeder
```
step 3:
publishing serviceproiveders and configs
this will create folders docker - database/migrations - config/MovieSeeder.php - config/tmdb.php
```
php artisan vendor:publish --tag=le_54ba\MovieSeeder\MovieSeederServiceProvider
php artisan vendor:publish --tag=Tmdb\Laravel\TmdbServiceProviderLaravel5
```
step 4:
to configure num_of_records and the scheduler interval time you can edit in config/MovieSeeder.php
```
	'configrable_interval_timer' =>'*/2 * * * *' ,  //should be cron expression
```
in config/tmdb.php edit 'api_key' value to your TMDB api key

step 5:
start docker container 
```
docker-compose up -d
```
step 6:
creating and migrating the database 
edit the .env file 
```
DB_DATABASE=movie_seeder
DB_USERNAME=root
DB_PASSWORD=root
```
after that in CMD
```
docker-compose exec mysql bash
mysql -u root -p
//the password is root
GRANT ALL ON movie_seeder.* TO 'laraveluser'@'%' IDENTIFIED BY 'root';
FLUSH PRIVILEGES;
EXIT
docker-compose exec app php artisan migrate
```
step 7:
starting the scheduler, queue worker and the API 
```
php artisan queue:work > /dev/null 2>&1 & php artisan schedule:run >> /dev/null 2>&1 & php artisan serve
```
you can make the scheduler executed every interval though crontab in linux 