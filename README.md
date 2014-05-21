Laravel-Datatables
==================

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
- Continued development until the project is production ready
- Column interpretation language for manipulating column data as well as concatenating multiple fields
- A query extension allowing for query manipulation after datatables has taken a count of the fields in the database
- Multiple fields per column
- A driver interface to allow custom database drivers to be used such as MongoDb, Cassandra or CouchDB instead of Eloquent/Fluent.

#What can you expect from this project?
- Continuous maintenance
- Fast responses to issues
