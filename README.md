[![Build Status](https://travis-ci.org/Daveawb/Laravel-Datatables.svg?branch=master)](https://travis-ci.org/Daveawb/Laravel-Datatables)

Laravel-Datatables
==================
#Introduction
This project is aimed at anyone using the fantastic dataTables jQuery plugin written by [SpryMedia](http://sprymedia.co.uk/) and Laravel 4.1 or greater. It was written originally for dataTables 1.9.x, however since 1.10.x has now been released with a new API and data structure there will be updates to allow you to make use of the new syntax in the near future. If you haven't used datatables before check it out at [Datatables.net](http://datatables.net/).

For the mean time you will need to use the old 1.9.x API that is still compatable with 1.10.x. You can find the docs at [the legacy Datatables site](http://legacy.datatables.net/).

## Supported datatables components
- Client side column re-ordering
- Per column sorting
- Global search
- Search in a specific column
- Set number of rows to return
- Filtered and Total rows

All components above work without any extra configuration. In a future release there will be the ability to send back named attributes per row such as `DT_RowClass` and any other data you want to return by row or if you want in the top level of the returned JSON for global data.

#Requirements
- >= PHP 5.4
- >= Laravel 4.1.*

#Installation
##Composer
Add the following to your composer.json file

````json
{
    "require": {
        "daveawb/datatables": "v0.1.0-beta"
    },
}
````

##Add the Laravel service provider
Once you have run a `composer update` you will need to add the service provider.

Open up `config/app.php` and add the followng to the service providers array.

````
"Daveawb\Datatables\DatatablesServiceProvider"
````
##Add the Laravel facade (optional)

Add the following to your `config/app.php` alias' array.

````
"Datatable" => "Daveawb\Datatables\Facades\Datatable"
````

#Basic Usage

````php
Route::post('datatable', function()
{
    $datatable = App::make("Daveawb\Datatables\Datatable");
    
    $datatable->model(new User());

    $datatable->columns(array(
        "first_name",
        "last_name",
        "username",
        "verified",
        "created_at",
        "updated_at"
    ));
    
    return $datatable->result();
});
````

The columns method takes an array in the order you wish to send data back to the client. Each field maps to a field in the database available from the query you've injected.

The model method will take an instance of either:
`Illuminate\Database\Eloquent\Model` or `Illuminate\Database\Eloquent\Builder`

You can also pass in an instance of a standard query builder using the `query` method instead of `model`.

````php
Route::post('datatable', function()
{
    $datatable = App::make("Daveawb\Datatables\Datatable");
    
    $datatable->query(DB::table('users'));

    $datatable->columns(array(
        "first_name",
        "last_name",
        "username",
        "verified",
        "created_at",
        "updated_at"
    ));
    
    return $datatable->result();
});
````

As the model and query methods accept builder instances you can pass a predefined query before inserting it into the datatables package.

````php
$user = new User();
$datatable->model($user->with('roles'));
````

Or using a standard query builder

````php
$datatable->query(DB::table('users')->where('deleted_at', '!=', 'NULL');
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
