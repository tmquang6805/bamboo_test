<?php
    function distance($lat1, $lon1, $lat2, $lon2)
    {
        $R = 6371; // Radius of the earth in km
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $R * $c;
    }

    /**
     * @param string $filename
     * @return array
     */
    function getCitiesFromFile(string $filename): array
    {
        $file = fopen($filename, "r");
        $cities = [];
        while (!feof($file)) {
            $line = trim(fgets($file));
            if (!empty($line)) {
                list($name, $lat, $lon) = explode(" ", $line);
                $cities[$name] = ["lat" => floatval($lat), "lon" => floatval($lon)];
            }
        }
        fclose($file);
        return $cities;
    }

    /**
     * @param array $cities
     * @return array
     */
    function distancesBetweenCities(array $cities): array
    {
        $distances = [];
        foreach ($cities as $city1 => $coord1) {
            foreach ($cities as $city2 => $coord2) {
                if ($city1 != $city2) {
                    $distances[$city1][$city2] = distance($coord1["lat"], $coord1["lon"], $coord2["lat"], $coord2["lon"]);
                }
            }
        }
        return $distances;
    }

    function travel(array $distances, int $numOfCity, string $currentCity): array
    {
        $visited = [$currentCity];
        while (count($visited) < $numOfCity) {
            $minDistance = PHP_FLOAT_MAX;
            $nextCity = "";
            foreach ($distances[$currentCity] as $city => $distance) {
                if ($distance < $minDistance && !in_array($city, $visited)) {
                    $minDistance = $distance;
                    $nextCity = $city;
                }
            }
            $currentCity = $nextCity;
            $visited[] = $currentCity;
        }
        return $visited;
    }

    function output(array $visited, int $numOfCity)
    {
        foreach ($visited as $idx => $city) {
            echo $city;
            if ($idx < $numOfCity - 1) {
                echo " => ";
            }
        }
        echo "\n";
    }

    function main()
    {
        $filename = "cities.txt";
        $currentCity = "Beijing";
        $cities = getCitiesFromFile($filename);
        $distances = distancesBetweenCities($cities);

        $numOfCity = count($cities);
        $visited = travel($distances, $numOfCity, $currentCity);

        output($visited, $numOfCity);
    }

    main();