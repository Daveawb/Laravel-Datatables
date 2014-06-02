Laravel-Datatables [![Build Status](https://travis-ci.org/Daveawb/Laravel-Datatables.svg?branch=master)](https://travis-ci.org/Daveawb/Laravel-Datatables)
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
        "daveawb/datatables": "v0.2.0-beta"
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
    
    $datatable->query(new User());

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

The query method maps directly to the driver being used, as standard Eloquent models/builders and query builders are accepted:
`Illuminate\Database\Eloquent\Model` or `Illuminate\Database\Eloquent\Builder`

Example passing in a standard query builder.

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

As thequery method accepts builder instances you can pass a predefined query before inserting it into the datatables package.

````php
$user = new User();
$datatable->query($user->with('roles'));
````

Or using a standard query builder

````php
$datatable->query(DB::table('users')->where('deleted_at', '!=', 'NULL');
````

##Column interpretation / decoration
Every now and again you find that you need to merge the contents of fields or wrap them in HTML tags. This is where column interpretation / decoration comes in. Each of the decorations / interpretations are executed in the order you declare them. If you use two or more decorators on the same column, the result of the previous operation will be the value passed into the next decorator. This will allow you to build some complex decorations with a few core methods.

**At present the first field declared is modified to hold the result of the combination of the two fields.**

###Built in methods

- Append
- Prepend
- Combine

####Append
Append takes two arguments, the value to append and an optional separator.
````php
$datatable->columns(array(
    // Note the space as second arg to append
    array("first_name", array("append" => "eats lots of pies, "))
));

// If value of first_name is David the output would be
array(
    // Only the aaData values are shown here
    "aaData" => array(
        array(
            "first_name" => "David eats lots of pies"
        )
    )
);
````

####Prepend
Prepend takes two arguments, the value to prepend and an optional separator.
````php
$datatable->columns(array(
    // Note the space as second arg to prepend
    array("last_name", array("prepend" => "Mr, "))
));

// If value of last_name is Barker the output would be
array(
    // Only the aaData values are shown here
    "aaData" => array(
        array(
            "last_name" => "Mr Barker"
        )
    )
);
````

####Combine
````php
$datatable->columns(array(
    // Note the space as the last arg to combine
    array("first_name", "last_name", array("combine" => "first_name,last_name, "))
));
````
Instead of passing a string into the column we pass an array, with the last value always being an array that declares the decorators/interpreters you want to use with their unique settings. Each interpreter will have separate documentation in the future. For now only `combine` is available and takes in field names to combine with the last value being the seperator. If the database values returned are `first_name = "David"` and `last_name = "Barker"` the above code would produce:

````php
array(
    // Only the aaData values are shown here
    "aaData" => array(
        array(
            "first_name" => "David Barker"
        )
    )
);
````
You can combine as many fields as you like, you are not limited to two.

####Chaining interpreters / decorators
You can chain as many decorators together as you like, interpreters are slightly different as they have terminal and non terminal expressions. For now all interpreters are terminal expressions and treat each call as a new interpretation.

````php
$datatable->columns(array(
    array(
        "first_name", 
        "last_name", 
        array(
            "combine" => "first_name,last_name, ",
            "append" => "Mr, ",
            "prepend" => "BSc(hons), "
        )
    );
));

// The result of the above would be
array(
    // Only the aaData values are shown here
    "aaData" => array(
        array(
            "first_name" => "Mr David Barker BSc(hons)"
        )
    )
);
````

###Use a closure on your column!
To allow some fine grained control over the contents of a specific field you can use a closure instead / as well as the decorators. You must declare a closure **BEFORE** any decorators / interpreters. Also be aware your closure will be executed **AFTER** decorators / interpretaters have been run.

````php
$datatable->columns(array(
    array(
        "first_name", 
        function($field, $databaseRowData)
        {
            return sprintf("A modified first_name field, it was %s before", $databaseRowData->$field);
        }
    );
));

// The result of the above would be
array(
    "aaData" => array(
        array(
            "first_name" => "A modified first_name field, it was David before"
        )
    )
);
````

Please note that to date the second field is not subject to any search, ordering or any other database related functionality. This will more than likely be added in the future.

#Roadmap
- Support for dataTables 1.10.x options
- A query extension allowing for query manipulation after datatables has taken a count of the fields in the database
- A driver interface to allow custom database drivers to be used such as MongoDb, Cassandra or CouchDB instead of Eloquent/Fluent.

#Testing
There are a full suite of tests written to make sure that this project works as expected. If you want to run the tests you will need to be running on a Linux OS with SQLite3 and PHPUnit. The tests are portable to mySQL however as it stands there is no support for it in the project.

If you wish to contribute to the project only pull requests that have been properly tested and commented will be accepted.
