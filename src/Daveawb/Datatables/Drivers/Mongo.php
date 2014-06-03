<?php
namespace Daveawb\Datatables\Drivers;

use Daveawb\Datatables\Driver;
use Daveawb\Datatables\Columns\Factory;
use Daveawb\Datatables\Columns\Column;
use Daveawb\Datatables\DatatablesException;

use MongoClient;
use MongoRegex;

class Mongo extends Driver {
    
    /**
     * An instance of the mongo cursor
     * @var {Object} MongoCursor
     */
    protected $db;
    
    /**
     * The connection object
     * @var {Object} MongoConnection
     */
    protected $connection;
    
    /**
     * Search terms built for the query
     * @var {Array}
     */
    protected $searchTerms = array();
    
    /**
     * Sorting terms build for the query
     * @var {Array}
     */
    protected $sortTerms = array();
    
    /**
     * Create an order by clause to add to the query
     * @param {Object} Daveawb\Datatables\Columns\Column
     */
    protected function buildOrder(Column $column)
    {        
        if ($column->bSortable && $column->sort)
        {
            $this->sortTerms[$column->fields[0]] = $column->sort_dir === "asc" ? 1 : -1;
        }
    }
    
    /**
     * Create a where clause to add to the query
     * @param {Object} Daveawb\Datatables\Columns\Column
     */
    protected function buildWhere(Column $column)
    {
        if ( strlen($column->sSearch) > 0 && $column->bSearchable )
        {
            $search = $column->bRegex ?
                "/" . str_replace('/', '\/', $column->sSearch) . "/i" :
                '/' . preg_quote($column->sSearch, '/') . '/i';
                
            if ( ! isset($this->searchTerms['$or']) )
                $this->searchTerms['$or'] = array();
            
            $this->searchTerms['$or'][][$column->fields[0]] = new MongoRegex($search);
        }
    }
    
    /**
     * Set the collection to use
     * @param {String} collection to use
     */
    protected function collection($collection)
    {
        $this->db = $this->db->{$collection};
    }
    
    /**
     * Setup the driver with the correct configuration options
     * @param {Array} array of configuration options
     */
    public function config(array $config)
    {
        $this->config = $config;
        
        $dsn = $this->getDsn($config);
        
        $options = array_get($config, 'options', array());
        
        $this->connection = $this->createConnection($dsn, $config, $options);
        
        $this->db = $this->connection->{$config['database']};
    }
    
    protected function createConnection($dsn, array $config, array $options)
    {
        // Add credentials as options, this makes sure the connection will not fail if
        // the username or password contains strange characters.
        if (isset($config['username']) && $config['username'])
        {
            $options['username'] = $config['username'];
        }

        if (isset($config['password']) && $config['password'])
        {
            $options['password'] = $config['password'];
        }

        return new MongoClient($dsn, $options);
    }
    
    /**
     * Get the results from the query and return
     * @return {Array} of results
     */
    public function get()
    {
        $this->totalCount = $this->db->find($this->searchTerms)->count();
        
        foreach($this->factory->getColumns() as $column)
        {
            $this->buildWhere($column);
            $this->buildOrder($column);
        }
        
        $cursor = $this->db->find($this->searchTerms, array())
            ->limit($this->factory->input->iDisplayLength)
            ->skip($this->factory->input->iDisplayStart)
            ->sort($this->sortTerms);
        
        $this->filteredCount = $cursor->count();
        
        return $this->prepareResponse($cursor);
    }
    
    /**
     * Get the configuration name for this driver
     * @return {String}
     */
    protected function getConfigName()
    {
        return "mongo";
    }
    
    /**
     * Create a DSN string from a configuration.
     *
     * @param  array   $config
     * @return string
     */
    protected function getDsn(array $config)
    {
        // First we will create the basic DSN setup as well as the port if it is in
        // in the configuration options. This will give us the basic DSN we will
        // need to establish the MongoClient and return them back for use.
        extract($config);

        // Treat host option as array of hosts
        $hosts = is_array($config['host']) ? $config['host'] : array($config['host']);

        // Add ports to hosts
        foreach ($hosts as &$host)
        {
            if (isset($config['port']))
            {
                $host = "{$host}:{$port}";
            }
        }

        // The database name needs to be in the connection string, otherwise it will
        // authenticate to the admin database, which may result in permission errors.
        return "mongodb://" . implode(',', $hosts) . "/{$database}";
    }
    
    /**
     * 
     */
    protected function prepareResponse($cursor)
    {
        return array(
            "sEcho" => $this->factory->input->sEcho,
            "aaData" => array_values(iterator_to_array($cursor)),
            "iTotalDisplayRecords" => $this->filteredCount,
            "iTotalRecords" => $this->totalCount
        );
    }
    
    /**
     * When using the mongo driver we just pass in a collection. The rest is managed by
     * the configuration that we pass in by default.
     * @param {String} the collection to use
     */
    public function query($query)
    {
        if (is_array($query))
        {
            if ( count($query) > 2 || count($query) < 2 )
                throw new DatatablesException("Passing an array to query must contain 2 indexes");
              
            if ( ! is_callable($query[1]) )
                throw new DatatablesException("Index 1 passed to query must be callable");
                
            $closure = array_pop($query);
            
            $query = $query[0];
        }
        
        $this->collection($query);
        
        if (isset($closure))
        {
            $this->searchTerms = call_user_func($closure);
        }
    }
}   