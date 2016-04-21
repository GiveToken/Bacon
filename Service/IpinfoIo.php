<?php
namespace Sizzle\Bacon\Service;

/**
 * This class is for interacting with ipinfo.io
 */
class IpinfoIo
{
    /**
     * Queries inpinfo.io with an IP and returns information or false on failure
     *
     * @param string $ipAddress - the IP address to get infor for
     *
     * @return mixed - an array of information or false on failure
     */
    public function getInfo(string $ipAddress)
    {
        $url = "http://ipinfo.io/{$ipAddress}";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $response = curl_exec($ch);
        $return = false;
        if (200 == curl_getinfo($ch, CURLINFO_HTTP_CODE)) {
            $locale = json_decode($response);
            if ('' != $locale) {
                $return = $locale;
            }
        }
        return $return;
    }
}
