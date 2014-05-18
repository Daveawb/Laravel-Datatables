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

$datatable->model(new User());

$datatable->columns(array(
    "first_name",
    "last_name"
));

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