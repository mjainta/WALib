<?php
/*
 * Holds MySQL.
 *
 * @package WALib
 */
namespace WALib\DB;
/*
 * An database adapter for MySQL.
 *
 * @package WALib
 */
class MySQL
{
    /**
     * The mysql db object.
     *
     * @var \mysqli
     */
    protected $_db = null;

    /**
     * Connects to a database.
     *
     * @param string $server
     * @param string $username
     * @param string $password
     * @throws \InvalidArgumentException
     */
    public function __construct($server, $username, $password, $database)
    {
        if(empty($server)
            && empty($username)
            && empty($password)
            && empty($database))
        {
            throw new \InvalidArgumentException('No empty db-parameters allowed. Server: "'.$server.'", User: "'.$username.'", Password: "'.$password.'"');
        }

        $this->_db = new \mysqli($server, $username, $password, $database);
    }

    /**
     * Performs a query and returns the result object.
     *
     * @param string $sql
     * @throws RuntimeException If executing the query returns false.
     * @return \mysqli_result
     */
    public function query($sql)
    {
        $result = $this->_db->query($sql);

        if(!$result)
        {
            throw new \RuntimeException('Executed query throws error: "'.$sql.'"');
        }

        return $result;
    }

    /**
     * Returns the result of a query as an associative array containing an array for each row.
     *
     * @param string $sql
     * @return mixed[]
     */
    public function queryArray($sql)
    {
        $result = $this->query($sql);
        $resultArray = array();

        while($row = $result->fetch_assoc())
        {
            $resultArray[] = $row;
        }

        return $resultArray;
    }

    /**
     * Returns an associative array with the first col being the key and the rest the value.
     *
     * If only two cols were provided, the array will contain col1/col2 pairs.
     * If more than two cols were provided, the array will contain col1/array pairs.
     *
     * @param string $sql
     * @return mixed[]
     */
    public function queryRef($sql)
    {
        $result = $this->query($sql);
        $resultArray = array();

        if($result->field_count == 2)
        {
            while($row = $result->fetch_assoc())
            {
                $resultArray[array_shift($row)] = array_shift($row);
            }
        }
        else
        {
            while($row = $result->fetch_assoc())
            {
                $resultArray[array_shift($row)] = $row;
            }
        }

        return $resultArray;
    }

    /**
     * Returns the first cell for the given query.
     *
     * If more than one cell get found, only the value of the first col of the
     * first row will get returned.
     *
     * @param string $sql
     * @return mixed
     */
    public function queryCell($sql)
    {
        $result = $this->query($sql);
        $row = $result->fetch_row();

        if($row === null)
        {
            return null;
        }
        else
        {
            return array_shift($row);
        }
    }

    /**
     * Returns a mysqli result.
     *
     * @param string[]|string $fields
     * @param string $table
     * @param string[]|string $wheres
     * @param mixed[] $orderBys Field/Direction pairs.
     * @param int $limitOffset
     * @param int $limitRows
     * @return \mysqli_result
     */
    protected function _generateQuery($fields, $table, $wheres = '', $orderBys = array(), $limitOffset = null, $limitRows = null)
    {
        $query = new MySQLQueryGenerator();
        $query->setBaseTable($table);

        if(is_string($fields))
        {
            $query->addField($fields);
        }
        elseif(is_array($fields))
        {
            foreach($fields as $field)
            {
                $query->addField($field);
            }
        }

        if(is_string($wheres))
        {
            $query->addWhere($wheres);
        }
        elseif(is_array($wheres))
        {
            foreach($wheres as $where)
            {
                $query->addWhere($where);
            }
        }

        foreach($orderBys as $field => $direction)
        {
            $query->addSort($field, $direction);
        }

        $query->setLimit($limitOffset, $limitRows);

        return $query->getSQL();
    }

    /**
     * Returns a mysqli result.
     *
     * @param string[]|string $fields
     * @param string $table
     * @param string[]|string $wheres
     * @param mixed[] $orderBys Field/Direction pairs.
     * @param int $limitOffset
     * @param int $limitRows
     * @return \mysqli_result
     */
    public function select($fields, $table, $wheres = array(), $orderBys = array(), $limitOffset = null, $limitRows = null)
    {
        $sql = $this->_generateQuery($fields, $table, $wheres, $orderBys, $limitOffset, $limitRows);

        return $this->query($sql);
    }

    /**
     * Returns an associative array which an array for each found row.
     *
     * @param string[]|string $fields
     * @param string $table
     * @param string[]|string $wheres
     * @param mixed[] $orderBys Field/Direction pairs.
     * @param int $limitOffset
     * @param int $limitRows
     * @return mixed[]
     */
    public function selectArray($fields, $table, $wheres = array(), $orderBys = array(), $limitOffset = null, $limitRows = null)
    {
        $sql = $this->_generateQuery($fields, $table, $wheres, $orderBys, $limitOffset, $limitRows);

        return $this->queryArray($sql);
    }

    /**
     * Returns an associative array with the first col being the key and the rest the value.
     *
     * If only two cols were provided, the array will contain col1/col2 pairs.
     * If more than two cols were provided, the array will contain col1/array pairs.
     *
     * @param string[]|string $fields
     * @param string $table
     * @param string[]|string $wheres
     * @param mixed[] $orderBys Field/Direction pairs.
     * @param int $limitOffset
     * @param int $limitRows
     * @return mixed[]
     */
    public function selectRef($fields, $table, $wheres = array(), $orderBys = array(), $limitOffset = null, $limitRows = null)
    {
        $sql = $this->_generateQuery($fields, $table, $wheres, $orderBys, $limitOffset, $limitRows);

        return $this->queryRef($sql);
    }

    /**
     * Returns the first cell for the given query.
     *
     * If more than one cell get found, only the value of the first col of the
     * first row will get returned.
     *
     * @param string $field
     * @param string $table
     * @param string[]|string $wheres
     * @return mixed
     */
    public function selectCell($field, $table, $wheres = array())
    {
        $sql = $this->_generateQuery($field, $table, $wheres);

        return $this->queryCell($sql);
    }

    /**
     * Updates rows or, if the selector doesnt finds entries, inserts a new row.
     *
     * @param mixed[] $row
     * @param string $table
     * @param string[]|string $selector
     */
    public function upsert($row, $table, $selector = array())
    {
        $sql = $this->_generateQuery('COUNT(*)', $table, $selector);
        $count = $this->queryCell($sql);

        if(!empty($count))
        {
            
        }
    }

    /**
     * Inserts a row into the database.
     *
     * @param mixed[] $row
     * @param string $table
     * @return boolean|integer Returns the new id of the inserted row or false
     * on failure.
     * @throws \InvalidArgumentException Thrown if $row or $table are empty.
     */
    public function insert($row, $table)
    {
        if(empty($row) || $table === '')
        {
            throw new \InvalidArgumentException('MySQL::insert() $row and $table may not be empty.');
        }

        $columns = '';
        $values = '';

        foreach($row as $column => $value)
        {
            if($value !== null)
            {
                /*
                 * Only add the column if its value is not null. If null, use
                 * standard value of the db instead.
                 */
                $columns .= ' `'.mysql_escape_string($column).'`,';
                $values .= ' \''.mysql_escape_string($value).'\',';
            }
        }

        $columns = trim($columns, ',');
        $values = trim($values, ',');
        $sql = 'INSERT INTO '.mysql_escape_string($table).'
                ('.$columns.')
                VALUES
                ('.$values.');';
        $success = $this->query($sql);

        if($success)
        {
            return $this->_db->insert_id;
        }
        else
        {
            return false;
        }
    }

    /**
     * Updates a tables entries selected by the $selector with the values of $row.
     *
     * @param mixed[] $row
     * @param string $table
     * @param string[] $selector
     * @return boolean
     * @throws \InvalidArgumentException
     */
    public function update($row, $table, $selector = array())
    {
        if(empty($row) || $table === '')
        {
            throw new \InvalidArgumentException('MySQL::insert() $row and $table may not be empty.');
        }

        $values = '';

        foreach($row as $column => $value)
        {
            if($value !== null)
            {
                /*
                 * Only add the column if its value is not null. If null, use
                 * standard value of the db instead.
                 */
                $values[] .= '`'.mysql_escape_string($column).'` = \''.mysql_escape_string($value).'\'';
            }
        }

        $sql = 'UPDATE '.mysql_escape_string($table).'
                SET '.implode(' AND ', $values)
                .(!empty($selector) ? 'WHERE '.$this->_concatSelector($selector) : '');
        $success = $this->query($sql);
dumpvar($sql);
        return $success;
    }

    /**
     * Concatenates the selector to a string with an 'AND' between every where clause.
     *
     * @param string[] $selector
     * @return string
     */
    protected function _concatSelector($selector)
    {
        return implode('AND', $selector);
    }
}