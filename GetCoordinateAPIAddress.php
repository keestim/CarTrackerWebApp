<?php
    REQUIRE './APIKeys.php';

    $longitutde = $_GET['longitutde'];
    $latitude = $_GET['latitude'];

    function getCoordinatesAddress($AzureKey, $Latitude, $Longitude)
    {
        if ($Latitude != "" && $Longitude != "")
        {
            $url = "https://atlas.microsoft.com/search/address/reverse/json?subscription-key=" . $AzureKey . "&api-version=1.0&query=" . $Latitude . "," . $Longitude . "&returnSpeedLimit=true";

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_HTTPGET, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response_json = curl_exec($ch);
            curl_close($ch);
            $response=json_decode($response_json, true);
    
            if (isset($response["addresses"][0]["address"]["freeformAddress"]))
            {
                return $response["addresses"][0]["address"]["freeformAddress"];
            }
        }
    }

    echo getCoordinatesAddress($AzureKey, $latitude, $longitutde);
?>