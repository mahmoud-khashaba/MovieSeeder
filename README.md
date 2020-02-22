
**Installing**

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
if it didn't copy the files do 
```
php artisan vendor:publish
```
and choose manually the two service providers

step 4:
to configure num_of_records and the scheduler interval time you can edit in config/MovieSeeder.php
```
	'configrable_interval_timer' =>'*/2 * * * *' ,  //should be cron expression
	'num_of_records'     //Note:that's only for Top rated movie records.there's also one record as recent or latest movie added
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
DB_HOST=mysql
DB_DATABASE=movie_seeder
DB_USERNAME=root
DB_PASSWORD=root
```
after that in CMD
```
docker-compose exec mysql bash
mysql -u root -p
//the password is root
GRANT ALL ON movie_seeder.* TO 'root'@'%' IDENTIFIED BY 'root';
FLUSH PRIVILEGES;
EXIT;
exit
docker-compose exec app php artisan migrate
```
step 7:
run 
```
docker-compose exec app php artisan schedule:run
```
or start all together 

```
docker-compose exec app php artisan queue:work >> /dev/null 2>&1 & docker-compose exec app php artisan schedule:run >> /dev/null 2>&1 & docker-compose exec app php artisan serve //for linux

docker-compose exec app php artisan queue:work > NUL  & docker-compose exec app php artisan schedule:run > NUL & docker-compose exec app php artisan serve //for windows
```
you can make the scheduler executed every interval though crontab in linux 

**Querying**

hit localhost:8080/movies to list all movies ----
you can add options to the url like &category_id=18 ----
you can query by video=0 or 1 adult=(0 or 1) ----
you can query by "word or letter" in the title,overview,original_language 'title=word&overview=word' ----
by release date equal or after the data given formatted as Y-m-d ----
by popularity,rated &popular|desc or popular|asc ----
