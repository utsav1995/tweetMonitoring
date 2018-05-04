<?php
require_once('TwitterAPIExchange.php'); 
 
/** Set Authorization tokens**/
$settings = array(
    'oauth_access_token' => "887500575683694592-THHEt5BmZPjPV05SnGnNTR2wLkthttS",
    'oauth_access_token_secret' => "B9fKi1Ds46GGw7JjbQlzba67B0O3sS9pVyUs0fEfZ3GiR",
    'consumer_key' => "2FqreXfY0WhqSfDzA1m20yqGy",
    'consumer_secret' => "Foymcta8cON5gGMbIyfYCPIoJtORgMnfZ0cXISBoPx3w6Duy9z"
);

/* To access Search */ 
$url = "https://api.twitter.com/1.1/search/tweets.json";

$requestMethod = "GET";

$hashtag=$_GET["hashtag"];
 
$getfield = "?q=$hashtag&count=100&lang=en";

$twitter = new TwitterAPIExchange($settings);
$string = json_decode($twitter->setGetfield($getfield)
->buildOauth($url, $requestMethod)
->performRequest(),$assoc = TRUE);

$posts = array();
        
foreach($string['statuses'] as $items)
    {
        
       if ($items['coordinates']['coordinates'][1] != null /*or $items['user']['location'] != null */)
        {
            $posts[] = array("type" => "Feature",
                            "properties" => array('id' => $items['id'], 
                                                'datetime' => $items['created_at'], 
                                                'tweet' => $items['text'],
                                                'username' => $items['user']['screen_name'],
                                                'location' => $items['user']['location']),
                            "geometry" => array("type" => "Point",
                                                "coordinates" => array($items['coordinates']['coordinates'][0], $items['coordinates']['coordinates'][1]))
                                        );
        }
        
    }
    

    if ($posts != null){
        if(file_exists('results.json'))  
        {  
            $current_data = file_get_contents('results.json');  
            $array_data = json_decode($current_data, true);  
            if ($array_data != null)
            {
                $resultarray = array_merge($array_data, $posts);
            }
            else
            {
                $resultarray=$posts;
            }
            $resultarrayunique = array_unique($resultarray, SORT_REGULAR);
            $final_data = json_encode($resultarrayunique);  
            file_put_contents('results.json', $final_data); 
        }  
    }


?>