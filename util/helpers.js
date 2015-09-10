
var chart;
var dbPort = 27017;
var dbName = "magi";
var lastTimestamp = -1;
var recordsLimit = 0;

function get(name){
   if(name=(new RegExp('[?&]'+encodeURIComponent(name)+'=([^&]*)')).exec(location.search))
      return decodeURIComponent(name[1]);
}

function createSSHTunnel(host) {
	//alert("createSSHTunnel");
	$.ajax({
		type: 'GET',
		url: '/magi-viz/util/tunnel.php',
		data: "host=" + host,
		success: function(result) {
			//alert(result);
			if (result == "Connection failed"){
				alert('Not able to connect to the expriment');
			}
			else{
				dbPort = result;
				requestData();
			}
		},
		cache: false
	});
}

function plot(localDBName, recordFetchLimit) {
	var dbHost = get("host");
	//alert(dbHost);
	if(dbHost != null){
		createSSHTunnel(dbHost);
	}else{
		recordFetchLimit = typeof recordFetchLimit !== 'undefined' ? recordFetchLimit : 1;
		dbName = localDBName;
		recordsLimit = recordFetchLimit;
		requestData();
	}
}

//First, checks if it isn't implemented yet.
if (!String.prototype.format) {
  String.prototype.format = function() {
    var args = arguments;
    return this.replace(/{(\d+)}/g, function(match, number) { 
      return typeof args[number] != 'undefined'
        ? args[number]
        : match
      ;
    });
  };
}