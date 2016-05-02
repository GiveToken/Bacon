<?php
namespace Sizzle\Bacon\Tests\Traits;

use Sizzle\Bacon\Database\City;

/**
 * Functions to create & tear down test cities
 */
trait City
{
    protected $cities = array();

    /**
     * Create a city for testing
     *
     * @param string $name - optional name of the city
     *
     * @return City - the new city
     */
    protected function createCity(string $name = null)
    {
        // create a city for testing
        $city = new City();
        $city->name = $name ?? "Test City ".rand();
        $city->population = rand(10000, 10000000);
        $city->longitude = rand(0, 100);
        $city->latitude = rand(0, 100);
        $city->county = "County " . rand(0, 100);
        $city->country = "CT";
        $city->timezone = "Awesome Standard Time";
        $city->temp_hi_spring = rand(0, 100);
        $city->temp_lo_spring = rand(0, 100);
        $city->temp_avg_spring = rand(0, 100);
        $city->temp_hi_summer = rand(0, 100);
        $city->temp_lo_summer = rand(0, 100);
        $city->temp_avg_summer = rand(0, 100);
        $city->temp_hi_fall = rand(0, 100);
        $city->temp_lo_fall = rand(0, 100);
        $city->temp_avg_fall = rand(0, 100);
        $city->temp_hi_winter = rand(0, 100);
        $city->temp_lo_winter = rand(0, 100);
        $city->temp_avg_winter = rand(0, 100);
        $city->save();
        $this->cities[] = $city->id;
        return $city;
    }

    /**
     * Deletes cities created for testing
     */
    protected function deleteCities()
    {
        foreach ($this->cities as $id) {
            $sql = "DELETE FROM city WHERE id = '$id'";
            (new City())->execute_query($sql);
        }
    }
}
