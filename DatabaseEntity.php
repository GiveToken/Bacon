<?php
namespace Sizzle\Bacon;

/**
 * This class implements basic database access functionality.
 */
class DatabaseEntity
implements \JsonSerializable
{
    use \Sizzle\Traits\CamelToUnderscore;

    protected $id;
    protected $created;
    protected $readOnly = ['id','readOnly','created'];

    /**
     * Constructs the class
     *
     * @param int $id - the id of the record to pull from the database
     */
    public function __construct($id = null)
    {
        if ($id !== null && strlen($id) > 0) {
            $id = escape_string($id);
            $sql = "SELECT * FROM {$this->tableName()} WHERE id = '$id'";
            $object = execute_query($sql)->fetch_object(get_class($this));
            if (isset($object)) {
                foreach (get_object_vars($object) as $key => $value) {
                    $this->$key = $value;
                }
            }
        }
    }

    /**
     * This function gets a protected property
     *
     * @param string $property - the class property desired
     *
     * @return mixed - the class property
     */
    public function __get(string $property)
    {
        if (isset($this->$property)) {
            return $this->$property;
        }
    }

    /**
     * This function sets a protected property if it's not read only or the class
     * doing it extends this class
     *
     * @param string $property - the class property to set
     * @param mixed  $value    - the value to set the property to
     */
    public function __set(string $property, $value)
    {
        if (!in_array($property, $this->readOnly) || is_subclass_of($this, 'Sizzle\Bacon\DatabaseEntity')) {
            $this->$property = $value;
        }
    }

    /**
     * This function checks if a protected property is set
     *
     * @param string $property - the class property to check
     *
     * @return bool - if the property is set
     */
    public function __isset(string $property)
    {
        return isset($this->$property);
    }

    /**
     * Returns the class name without the namespace as the table name
     */
    protected function tableName()
    {
        return $this->fromCamelCase(substr(get_class($this), strrpos(get_class(), 'Database')+9));
    }

    /**
     * Appends to the list of read only columns
     */
    protected function addReadOnly(string $column)
    {
        array_push($this->readOnly, $column);
    }

    /**
     * Inserts into the database, setting $this->id
     */
    protected function insertRow()
    {
        $comma = '';
        $columns = '';
        $values = '';
        foreach (get_object_vars($this) as $key => $value) {
            if ($key !== 'readOnly' && !in_array($key, $this->readOnly) && isset($value)) {
                $columns .= $comma.'`'.$key.'`';
                $values .= $comma."'".escape_string($value)."'";
                $comma = ', ';
            }
        }
        $sql = "INSERT INTO {$this->tableName()} ($columns) VALUES ($values)";
        $this->id = insert($sql);
    }

    /**
     * Updates the database using $this->id
     */
    protected function updateRow()
    {
        $comma = null;
        $sql = "UPDATE {$this->tableName()} SET ";
        foreach (get_object_vars($this) as $key => $value) {
            if ($key !== 'readOnly' && !in_array($key, $this->readOnly)) {
                $sql .= $comma.'`'.$key.'`'." = ";
                if (strlen($value) > 0) {
                    $sql .= "'".escape_string($value)."'";
                } else {
                    $sql .= 'NULL';
                }
                $comma = ', ';
            }
        }
        $sql .= " WHERE id = '$this->id'";
        execute($sql);
    }

    /**
     * Inserts into or updates the database
     */
    public function save()
    {
        if (!$this->id) {
            $this->insertRow();
        } else {
            $this->updateRow();
        }
    }

    /**
     * Specifies json_encode behavior with magic methods
     */
    public function jsonSerialize()
    {
        return (object) get_object_vars($this);
    }

    /**
     * nsets all the class vars
     */
    public function unsetAll()
    {
        foreach (get_object_vars($this) as $key=>$value) {
            if ('readOnly' != $key) {
                unset($this->$key);
            }
        }
    }
}
