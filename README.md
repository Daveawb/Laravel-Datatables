Laravel-Datatables
==================

#Introduction
This project is aimed at anyone using the fantastic dataTables jQuery plugin written by [SpryMedia](http://sprymedia.co.uk/). It was written originally for dataTables 1.9.x, however since 1.10.x has now been released with a new API and data structure there will be updates to allow you to make use of the new syntax in the near future. If you haven't used datatables before check it out at [Datatables.net](http://datatables.net/).

For the mean time you will need to use the old 1.9.x API that is still compatable with 1.10.x. You can find the docs at [the legacy Datatables site](http://legacy.datatables.net/).

#Installation

Add the following you your composer.json file

````
{
    "require": {
        "Daveawb\Datatables" : "dev-master"
    },
}
````

Once you have run a `composer update` you will need to add the service provider.

Open up `config/app.php` and add the followng to the service providers array.

````
"Daveawb\Datatables\DatatablesServiceProvider"
````
#Optional Facade

Add the following to your `config/app.php` alias' array.

````
"Datatable" => "Daveawb\Datatables\Facades\Datatable"
````

#Basic Usage

````
$datatable = App::make("Daveawb\Datatables\Datatable");

//The model method will accept an instance of Illuminate\Database\Eloquent\Model
//or Illuminate\Database\Eloquent\Builder allowing complex queries to be built
//prior to any datatable query manipulation
$datatable->model(new User());
 
// Array values are the column fields
$datatable->columns(array(
    "first_name",
    "last_name"
));

// An instance of Illuminate\Http\JsonResponse is returned
// no need to wrap it any further
return $datatable->result();
````

You can also pass in an instance of a query builder as well if you like to create pre built base queries.

````
$user = new User();
$datatable->model($user->with('roles'));
````

If you don't want to use Eloquent models. You can pass in an instance of a query builder instead.

````
$datatable->query(DB::table('users'));
````
** Note you don't need to pass in a model and a query **

#Roadmap
- Support for dataTables 1.10.x options
- Column interpretation language for manipulating column data as well as concatenating multiple fields
- A query extension allowing for query manipulation after datatables has taken a count of the fields in the database
- Multiple fields per column
- A driver interface to allow custom database drivers to be used such as MongoDb, Cassandra or CouchDB instead of Eloquent/Fluent.

#Testing
There are a full suite of tests written to make sure that this project works as expected. If you want to run the tests you will need to be running on a Linux OS with SQLite3 and PHPUnit. The tests are portable to mySQL however as it stands there is no support for it in the project.

If you wish to contribute to the project only pull requests that have been properly tested and commented will be accepted.
