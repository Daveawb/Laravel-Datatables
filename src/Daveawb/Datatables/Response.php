<?php
namespace Daveawb\Datatables;

use Daveawb\Datatables\Columns\Factory;

use Illuminate\Config\Repository;

class Response
{

    /**
     * Configuration object
     * @var \Illuminate\Config\Repository
     */
    protected $config;

    /**
     * Query driver
     * @var \Daveawb\Datatables\Driver
     */
    protected $driver;

    /**
     * Column factory
     * @var \Daveawb\Datatables\Columns\Factory
     */
    protected $factory;

    /**
     * Row attributes
     * @var array
     */
    protected $attributes;

    public function __construct(Repository $config, Driver $driver, Factory $factory, array $attributes)
    {
        $this->config = $config;
        $this->driver = $driver;
        $this->factory = $factory;
        $this->attributes = $attributes;
    }

    /**
     * @return array
     */
    public function get()
    {
        return $this->formattedResponse($this->filter($this->driver->get()));
    }

    /**
     * @param $data
     * @return array
     */
    protected function formattedResponse($data)
    {
        return array(
            "aaData" => $data,
            "iTotalRecords" => $this->driver->getTotalRecords(),
            "iTotalDisplayRecords" => $this->driver->getDisplayRecords(),
            "sEcho" => $this->factory->input->sEcho
        );
    }

    /**
     * Filter the results and organise them by column order. This is the point
     * that column fields are interpreted and applied to the results field.
     * @param $data
     * @return array
     */
    public function filter($data)
    {
        $filtered = array();

        $modified = $data;

        for ($i = 0; $i < count($data); $i++)
        {
            foreach ($this->factory->getColumns() as $key => $column)
            {
                $column->interpret($column->fields[0], $modified[$i]);

                $filtered[$i][$column->mDataProp] = array_get($modified[$i], $column->fields[0]);
            }

            $filtered[$i] = array_merge($filtered[$i], $this->attributes($data[$i]));
        }

        return $filtered;
    }

    /**
     * @param $data
     * @return array
     */
    private function attributes($data)
    {
        $attributes = $this->attributes;

        foreach ($attributes as &$attribute)

        {
            if (array_key_exists($attribute, $data))
            {
                $attribute = $data[$attribute];
            }
        }

        return $attributes;
    }
}