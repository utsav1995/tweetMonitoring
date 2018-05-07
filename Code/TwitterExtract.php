<?php
require_once('TwitterAPIExchange.php'); 
 
/** Set Authorization tokens**/
$settings = array(
    'oauth_access_token' => "xxxx",
    'oauth_access_token_secret' => "xxxx",
    'consumer_key' => "xxxx",
    'consumer_secret' => "xxxx"
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
