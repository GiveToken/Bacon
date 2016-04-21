<?php
namespace Sizzle\Bacon\Database;

class CityImage extends \Sizzle\Bacon\DatabaseEntity
{
    protected $city_id;
    protected $image_file;
    protected $created;

    /**
     * Gets all the images for a city sorted by filename
     *
     * @return array(success - did the query succeed, data - image urls) -
     */
    public static function getAllImageUrlsForCity($city_id)
    {
        $city_id = (int) $city_id;
        $imageUrls = array();
        $results = execute_query("SELECT * FROM city_image WHERE city_id='$city_id' ORDER BY id");
        if ($results) {
            while ($object = $results->fetch_object()) {
                $imageUrls[count($imageUrls)] = $object;
            }
            $results->free();
        }
        return $imageUrls;
    }
}
