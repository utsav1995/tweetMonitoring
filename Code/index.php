<html>
<!------------------------------------------ HEAD Starts here! ------------------------------------------>
<head>
    <title>Real Time Tweet Monitoring</title>
    <!------------------------- Imports! ------------------------->
	<link rel="stylesheet" type="text/css" href="style.css">
	<link rel="stylesheet" href="https://unpkg.com/leaflet@1.3.1/dist/leaflet.css" />
	<script src="https://unpkg.com/leaflet@1.3.1/dist/leaflet.js" type="text/javascript"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js" type="text/javascript"></script>

	<!------------------------- Javascript ------------------------->
	<script type="text/javascript">
	
	    //For controlling Start/Stop
		var x = 0; 
		
		//Function for STOP button
		function stopAll(){
		    x = 1;
		    map.eachLayer(function (layer) {
                            if (layer != osm)
                            {
                                map.removeLayer(layer);
                                console.log("Old data removed successfully.");
                            }
                        });
		    console.log("Stopped all!!");
		    document.getElementById("start").style.display = 'block';
		    document.getElementById("stop").style.display = 'none';
		    document.getElementById("hashtaginput").disabled = false;
		}
		
		//Function for START button
        function startTweeping() {
            
            //Passing the hashtag value to TwitterExtract.PHP!
            var hashtag = document.getElementById("hashtaginput").value;
            var url = "https://pepfy.com/twitter/TwitterExtract.php?hashtag="+hashtag;
            xmlReq=new XMLHttpRequest();
            xmlReq.open("GET",url,true);
            xmlReq.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xmlReq.send();
            console.log("Sent request with hashtag = " + hashtag);
            
            //Hiding START Button & showing STOP button!
            document.getElementById("start").style.display = 'none';
            document.getElementById("stop").style.display = 'block';
            document.getElementById("hashtaginput").disabled = true;
            
            //Recalling the function every 5 seconds!
            if (x == 0){
                setTimeout(startTweeping,5000);
            }
        }
        
        //Enter key to start searching
        window.onload = function(){
            document.getElementById("stop").style.display = 'none';
            document.getElementById('hashtaginput').onkeydown = function(e){
               if(e.keyCode == 13){
                 return !!(startTweeping() & startVisualizing());
               }
            };
        }
	</script>
</head>
<!------------------------------------------ BODY Starts here! ------------------------------------------>
<body>
    <!-- Page Header -->
	<div id="topdiv">
	</div>
	
	<!-- Left Menu Bar -->
	<div id="leftdiv">
		<center><img src="images/logo.jpg" width=70%><br><br>
		<div id="Title">Real Time <br>Tweet Monitoring</div><br><br>
		<form autocomplete="off" onkeypress="return event.keyCode != 13;">
			<input id="hashtaginput" type="text" placeholder="Enter #Hashtag"><br><br>
			<button type="button" id="start" onclick="return !!(startTweeping() & startVisualizing());">Search</button>
			<button type="button" id="stop" onclick="stopAll()">Stop</button>
		</form>
		<br>
		<div id="count"></div>
		</center>
	</div>
	
	<!-- Right Leflet Map -->
	<div id="rightdiv">
	</div>
	
	<!-- Page Footer-->
	<div id="bottomdiv">
	<br>Copyright © 2018 AKKU
	</div>
	
	<!-- Javascript -->
	<script>
	
	    //Initialize map
		var map = L.map('rightdiv',{center: [10, 30], zoom: 2});

        //Add OSM Base layer
		var osm = L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'});
        osm.addTo(map);
        
        //Function to count all layers in map
        function countPoints(){
                var count = 0;
                map.eachLayer(function(){ count += 1; });
                
                console.log('Map has', count, 'layers.');
                if(count>1)
                {
                    document.getElementById("count").innerHTML = "Total " + count + " tweets plotted!";
                }
                setTimeout(countPoints,5000);
        }
        
        //Function to plot tweets on map
        function startVisualizing() {
                    
                //JQuery to read JSON file 
                $.getJSON("results.json", function(data) {
                    
                    //Function to add feature properties as Popup
                    function onEachFeature(feature, layer) {
                        layer.bindPopup("<b>Name: </b>" + feature.properties.username + 
                                        "<br>" + "<b>Tweet: </b>" + feature.properties.tweet + 
                                        "<br>" + "<b>Location: </b>" + feature.properties.location + 
                                        "<br>" + "<b>Date/Time: </b>" + feature.properties.datetime);
                    }   
                    var geojson = L.geoJson(data, {
                      onEachFeature: onEachFeature
                    });
                    
                    //Removing all layers except basemap
                    map.eachLayer(function (layer) {
                        if (layer != osm)
                        {
                            map.removeLayer(layer);
                            console.log("Old data removed successfully.");
                        }
                    });
                    
                    //Adding all layers
                    geojson.addTo(map);
                    
                  });
                  
                console.log("Twitter data successfully loaded.");
                
                //call function every 5 seconds
                setTimeout(startVisualizing,5000);
        }
        countPoints();
        
	</script>
	

	
</body>
</html>