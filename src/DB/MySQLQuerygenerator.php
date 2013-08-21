<?php
/*
 * Holds MySQLQueryGenerator.
 *
 * @package WALib
 */
namespace WALib\DB;
/*
 * Generates queries for use with MySQL.
 *
 * @package WALib
 */
class MySQLQueryGenerator
{
    /**
     * The fields which will be returned by the resulting sql query.
     *
     * @var mixed[]
     */
    protected $_fields = array();

    /**
     * Holds the tables the query should use.
     *
     * @var mixed[]
     */
    protected $_tables = array();

    /**
     * The where clauses the query should use.
     *
     * @var string[]
     */
    protected $_wheres = array();

    /**
     * The "group by"´s the query should use.
     *
     * @var string[]
     */
    protected $_groups = array();

    /**
     * The sortings the query should use.
     *
     * @var mixed[]
     */
    protected $_sorts = array();

    /**
     * The limitOffset, which the query should use.
     *
     * @var int|null
     */
    protected $_limitOffset = null;

    /**
     * The limit rowCount, which the query should use.
     *
     * @var int|null
     */
    protected $_limitRowCount = null;

    /**
     * The last generated alias of the query generator.
     *
     * Used to replace alias placeholders with this one.
     *
     * @var string
     */
    protected $_lastAlias = '';

    /**
     * Sets the base table for the query.
     *
     * Overwrites previous set table, if the function gets called multiple times.
     *
     * @param string $name
     * @param string $alias
     * @return string The generated alias.
     */
    public function setBaseTable($name, $alias = '')
    {
        if($alias == '')
        {
            $alias = $this->_generatedAlias($name, $name.$alias);
        }
        else
        {
            $this->_lastAlias = $alias;
        }

        $this->_tables['baseTable'] = array
        (
            'name' => $name,
            'alias' => $alias
        );

        return $alias;
    }

    /**
     * Adds a table to the query using a join.
     *
     * @param string $name
     * @param string $joinOn
     * @param string $joinType
     * @param string $alias
     * @return string The generated alias.
     */
    public function addTable($name, $joinOn, $joinType = 'INNER', $alias = '')
    {
        if($alias == '')
        {
            $alias = $this->_generatedAlias($name, $name.$joinOn.$joinType.$alias);
        }
        else
        {
            $this->_lastAlias = $alias;
        }

        $joinOn = $this->_replaceAlias($joinOn);
        $this->_tables[$alias] = array
        (
            'name' => $name,
            'joinOn' => $joinOn,
            'joinType' => $joinType
        );

        return $alias;
    }

    /**
     * Adds a field for the query.
     *
     * Overwrites field with the same name.
     *
     * @param string $name
     * @param string $alias
     */
    public function addField($name, $alias = '')
    {
        $name = $this->_replaceAlias($name);

        $this->_fields[$name] = $alias;
    }

    /**
     * Adds a where clause to the query.
     *
     * @example "users.id = 1"
     * @param string $where
     */
    public function addWhere($where)
    {
        $where = $this->_replaceAlias($where);
        $this->_wheres[] = $where;
    }

    /**
     * Adds a field for the "GROUP BY" clause of the query.
     *
     * @param string $groupBy
     */
    public function addGroupBy($groupBy)
    {
        $groupBy = $this->_replaceAlias($groupBy);
        $this->_groups[] = $groupBy;
    }

    /**
     * Adds a sorting to the query.
     *
     * @param string $field
     * @param string $direction
     */
    public function addSort($field, $direction)
    {
        $field = $this->_replaceAlias($field);
        $this->_sorts[$field] = $direction;
    }

    /**
     * Sets a limit clause for the query.
     *
     * Only accepts int values.
     *
     * @param int $offset
     * @param int $rowCount
     */
    public function setLimit($offset, $rowCount)
    {
        if(is_int($offset))
        {
            $this->_limitOffset = $offset;
        }

        if(is_int($rowCount))
        {
            $this->_limitRowCount = $rowCount;
        }
    }

    /**
     * Generates and returns the sql query for the set variables.
     *
     * @return string
     */
    public function getSQL()
    {
        $sql = 'SELECT';

        /*
         * If no field was provided, output all.
         */
        if(empty($this->_fields))
        {
            $this->_fields['*'] = '';
        }

        /*
         * Adding each field to the query.
         */
        foreach($this->_fields as $colName => $colAlias)
        {
            $sql .= ' '.$colName;

            if($colAlias !== '')
            {
                $sql .= ' AS '.$colAlias;
            }

            $sql .= ',';
        }

        /*
         * Cutting of the last comma of the query.
         */
        $sql = trim($sql, ',');

        /*
         * Adding the base table.
         */
        $baseTable = $this->_tables['baseTable'];
        $sql .= ' FROM '.$baseTable['name'];

        if($baseTable['alias'] !== '')
        {
            $sql .= ' AS '.$baseTable['alias'];
        }

        unset($this->_tables['baseTable']);

        /*
         * Adding the other tables.
         */
        foreach($this->_tables as $alias => $table)
        {
            $sql .= ' '.$table['joinType'].' JOIN '.$table['name'].' AS '.$alias.' ON '.$table['joinOn'];
        }

        if(!empty($this->_wheres))
        {
            /*
             * Adding where clauses.
             */
            $sql .= ' WHERE';

            foreach($this->_wheres as $where)
            {
                $sql .= ' '.$where.' AND';
            }

            /*
             * Cutting of the last " AND" of the query.
             */
            $sql = substr($sql, 0, -4);
        }

        if(!empty($this->_groups))
        {
            /*
             * Adding GROUP BY clauses.
             */
            $sql .= ' GROUP BY';

            foreach($this->_groups as $groupBy)
            {
                $sql .= ' '.$groupBy.',';
            }

            /*
             * Cutting of the last comma of the query.
             */
            $sql = trim($sql, ',');
        }

        if(!empty($this->_sorts))
        {
            /*
             * Adding ORDER BY clauses.
             */
            $sql .= ' ORDER BY';

            foreach($this->_sorts as $field => $direction)
            {
                $sql .= ' '.$field.' '.$direction.',';
            }

            /*
             * Cutting of the last comma of the query.
             */
            $sql = trim($sql, ',');
        }

        /*
         * Adding a limit clause.
         */
        if(isset($this->_limitOffset, $this->_limitRowCount))
        {
            $sql .= ' LIMIT '.$this->_limitOffset.', '.$this->_limitRowCount;
        }
        elseif(isset($this->_limitRowCount))
        {
            $sql .= ' LIMIT '.$this->_limitRowCount;
        }

        return $sql;
    }

    /**
     * Returns the generated alias for the given table and base string for the crc-suffix and sets _lastAlias.
     *
     * @param string $tblName
     * @param string $crcBase
     * @return string
     */
    protected function _generatedAlias($tblName, $crcBase)
    {
        $alias = $tblName.crc32($crcBase);
        $this->_lastAlias = $alias;

        return $alias;
    }

    /**
     * Replaces the alias placeholder with the last generated alias and returns the resulting string.
     *
     * @param string $string
     * @return string
     */
    protected function _replaceAlias($string)
    {
        return str_replace('{alias}', $this->_lastAlias, $string);
    }

    /**
     * Returns the last generated alias of the querygenerator.
     *
     * @return string
     */
    public function getLastAlias()
    {
        return $this->_lastAlias;
    }
}