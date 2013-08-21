<?php
/**
 * Holds Base.
 *
 * @package WALib
 */
namespace WALib\Mvc\Mappers;

use WALib\Mvc\Models\Base as ModelBase;
use WALib\DB\MySQL as MySQL;
use WALib\DB\MySQLQueryGenerator as MySQLQueryGenerator;
/**
 * Base class for mappers.
 *
 * A base mapper for filling models with data from a database.
 *
 * @package WALib
 */
abstract class Base
{
    /**
     * The database object which is used to get the data for models.
     *
     * @var MySQL
     */
    protected $_db = null;

    /**
     * The table name which the models get their data from.
     *
     * @var string
     */
    protected $_table = '';

    /**
     * The columns of the table.
     *
     * @var mixed[]
     */
    protected $_columns = array();

    /**
     * The column name of the primary key.
     *
     * @var string
     */
    protected $_pk = '';

    /**
     * Sets the database object.
     *
     * @param \WALib\DB\MySQL $db
     */
    public function __construct(MySQL $db)
    {
        $this->_db = $db;
        $this->_loadTableSchema();
    }

    /**
     * Fills the columns and primary key using the table name.
     */
    protected function _loadTableSchema()
    {
        $tblSchema = $this->_db->queryArray('SHOW COLUMNS FROM '.$this->_table);

        foreach($tblSchema as $colSchema)
        {
            $column = array
            (
                'name' => $colSchema['Field'],
                'type' => $colSchema['Type']
            );
            $this->_columns[$colSchema['Field']] = $column;
        }
    }

    /**
     * Returns a set of models found by the where array.
     *
     * @param string[] $wheres An array of WHERE clauses used with the query generator.
     * @return \WALib\Mvc\Mappers\Base[]
     */
    public function getModelsBy($wheres = array())
    {
        $models = array();
        $queryGenerator = new MySQLQueryGenerator();
        $queryGenerator->setBaseTable($this->_table);

        foreach($wheres as $where)
        {
            $queryGenerator->addWhere($where);
        }

        $sql = $queryGenerator->getSQL();
        $rawModelDatas = $this->_db->queryArray($sql);

        foreach($rawModelDatas as $rawModelData)
        {
            $models[] = $this->getModelByDataArray($rawModelData);
        }

        return $models;
    }

    /**
     * Returns a single model for the given where array. If more were found, the
     * first will be returned.
     *
     * @param string[] $wheres
     * @return ModelBase
     */
    public function getModelBy($wheres = array())
    {
        return reset($this->getModelsBy($wheres));
    }

    /**
     * Returns a model after filling it using a given data array.
     *
     * @param mixed[] $rawModelData
     * @return \WALib\Mvc\Mappers\Base
     */
    public function getModelByDataArray($rawModelData)
    {
        $modelClass = str_replace('Mappers', 'Models', get_class($this));

        if(class_exists($modelClass))
        {
            $model = new $modelClass();
        }
        else
        {
            /*
             * Use the standard model as a backup.
             */
            $model = new ModelBase();
        }

        foreach($this->_columns as $column)
        {
            $colName = $column['name'];

            if(isset($rawModelData[$colName]))
            {
                $model->$colName = $rawModelData[$colName];
            }
            else
            {
                $model->$colName = $this->_getDefaultColValue($colName);
            }
        }

        return $model;
    }

    /**
     * Returns the default value for a given column.
     *
     * @param string $colName
     * @return null
     */
    protected function _getDefaultColValue($colName)
    {
        return null;
    }

    /**
     * Verifies and, if verified, saves a model to the database.
     */
    abstract public function save(ModelBase $model);
}