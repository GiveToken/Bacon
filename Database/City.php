<?php
namespace Sizzle\Bacon\Database;

/**
 * This class is for interacting with the city table.
 */
class City extends \Sizzle\Bacon\DatabaseEntity
{
    protected $name;
    protected $population;
    protected $longitude;
    protected $latitude;
    protected $county;
    protected $country;
    protected $timezone;
    protected $temp_hi_spring;
    protected $temp_lo_spring;
    protected $temp_avg_spring;
    protected $temp_hi_summer;
    protected $temp_lo_summer;
    protected $temp_avg_summer;
    protected $temp_hi_fall;
    protected $temp_lo_fall;
    protected $temp_avg_fall;
    protected $temp_hi_winter;
    protected $temp_lo_winter;
    protected $temp_avg_winter;

    /**
     * Gets the city id given the city name
     *
     * @param string $name - the name of the city to pull from the database
     *
     * @return int - the id of the named city
     */
    public function getIdFromName(string $name)
    {
        $sql = "SELECT id FROM city WHERE name = '$name'";
        $result = $this->execute_query($sql);
        $object = is_object($result) ? $result->fetch_object() : null;
        $id = is_object($object) ? $object->id : null;
        return $id;
    }

    /**
     * Inserts or updates a city in the database
     *
     * @return boolean - success of update or insert
     */
    public function save()
    {
        if (!$this->id) {
            return $this->insert();
        } else {
            return $this->update();
        }
    }

    /**
     * Inserts a city into the database if all required fields are set
     *
     * @return boolean - success of insert
     */
    protected function insert()
    {
        $success = true;

        // check for required columns
        foreach (get_object_vars($this) as $key => $value) {
            if (!in_array($key, $this->readOnly)) {
                $success = $success && isset($value);
            }
        }
        if ($success) {
            parent::insertRow();
            if ($success = $success && ((int) $this->id > 0)) {
                $sql = "SELECT created
                      FROM {$this->tableName()}
                      WHERE id = $this->id";
                $this->created = $this->execute_query($sql)->fetch_object()->created;
            }
        }

        // return results
        return $success;
    }

    /**
     * Updates a city in the database
     *
     * @return boolean - success of update
     */
    protected function update()
    {
        $update = isset($this->name);
        $update = $update && isset($this->population);
        $update = $update && isset( $this->longitude);
        $update = $update && isset( $this->latitude);
        $update = $update && isset( $this->county);
        $update = $update && isset( $this->country);
        $update = $update && isset( $this->timezone);
        $update = $update && isset( $this->temp_hi_spring);
        $update = $update && isset( $this->temp_lo_spring);
        $update = $update && isset( $this->temp_avg_spring);
        $update = $update && isset( $this->temp_hi_summer);
        $update = $update && isset( $this->temp_lo_summer);
        $update = $update && isset( $this->temp_avg_summer);
        $update = $update && isset( $this->temp_hi_fall);
        $update = $update && isset( $this->temp_lo_fall);
        $update = $update && isset( $this->temp_avg_fall);
        $update = $update && isset( $this->temp_hi_winter);
        $update = $update && isset( $this->temp_lo_winter);
        $update = $update && isset( $this->temp_avg_winter);
        if ($update) {
            parent::updateRow();
        }
        return $update;
    }

    /**
     * Gets a list of possible cities from first part.
     *
     * @param string $part - partially typed city name
     *
     * @return array - 10 or fewer matches; none if there's more
     */
    public function match10(string $part)
    {
        $part = $this->escape_string($part);
        $cities = $this->execute_query(
            "SELECT * FROM city
             WHERE name LIKE '$part%'
             ORDER BY name"
        )->fetch_all(MYSQLI_ASSOC);
        if (count($cities) < 11) {
            return $cities;
        } else {
            return [];
        }
    }

    /**
     * Deletes from the database using $this->id
     */
    public function delete()
    {
        $table_name = substr(get_class(), strrpos(get_class(), '\\')+1);
        $sql = "DELETE FROM {$this->tableName()} WHERE id = '$this->id'";
        execute($sql);
    }

    /**
     * Gets CityImages corresponding to current City.
     *
     * @return array - city image information
     */
    public function getCityImages()
    {
        $sql = "SELECT id FROM city_image WHERE city_id = '$this->id'";
        $results = $this->execute_query($sql)->fetch_all(MYSQLI_ASSOC);
        $return = array();
        foreach ($results as $row) {
          $return[] = new CityImage($row['id']);
        }
        return $return;
    }
}
