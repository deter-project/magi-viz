var chart;
var graphConfigFile;
var dbHost, dbPort, dbName, collectionName;
var lastTimestamp, recordsLimit;
var title, xLabel, yLabel;
var options;

options = decodeQueryData();
graphConfigFile = getDefault(options, 'graphConfigFile', 'magi_graph.conf');

function parseOptions(config){
	
	var graph_config, db_config;
	
	if(typeof config != 'undefined') {
		graph_config = config['graph'];
    	db_config = config['db'];
	}
	
	dbHost = setDefault(options, 'dbHost', 
			getDefault(db_config, 'dbHost', 'localhost'));
	dbPort = setDefault(options, 'dbPort', 
			getDefault(db_config, 'dbPort', 27017));
	dbName = setDefault(options, 'dbName', 
			getDefault(db_config, 'dbName', 'magi'));
	collectionName = setDefault(options, 'collectionName', 
			getDefault(db_config, 'collectionName', 'experiment_data'));
	
	lastTimestamp = setDefault(options, 'lastTimestamp', 
			getDefault(db_config, 'lastTimestamp', -1));
	recordsLimit = setDefault(options, 'recordsLimit', 
			getDefault(db_config, 'recordsLimit', 0));
	
	title = setDefault(options, 'title', 
			getDefault(graph_config, 'title', 'MAGI Graph'));
	xLabel = setDefault(options, 'xLabel', 
			getDefault(graph_config, 'xLabel', 'xLabel'));
	yLabel = setDefault(options, 'yLabel', 
			getDefault(graph_config, 'yLabel', 'yLabel'));
	
	console.log(options);
}

function createSSHTunnel(host, port, callback) {
	//alert('createSSHTunnel');
	$.ajax({
		type: 'GET',
		url: '/magi-viz/util/tunnel.php',
		data: "host=" + host +
			  "&port=" + port,
		cache: false
	}).done(function (data, textStatus, jqXHR) {
		//alert(result);
		dbHost = 'localhost';
		dbPort = data;
		callback();
	}).fail(function(jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        alert('Error creating tunnel: ' + jqXHR.responseText)
        //alert(jqXHR.responseText);
        //alert("Status: " + textStatus); alert("Error: " + errorThrown); 
    });
}

function plot(localDBName, recordFetchLimit) {
	parseOptions();
	if(dbHost != 'localhost'){
		createSSHTunnel(dbHost, dbPort, requestData);
	}else{
		//Support for legacy code
		if(typeof localDBName != 'undefined')
			dbName = localDBName;
		recordsLimit = typeof recordFetchLimit !== 'undefined' ? recordFetchLimit : 1;
		
		requestData();
	}
}

function setDefault(dict, key, defaultValue){
	if(typeof dict == 'undefined')
		dict = {};
	if (!(key in dict)){
		dict[key] = defaultValue;
	}
	return dict[key];
}

function getDefault(dict, key, defaultValue){
	if(typeof dict != 'undefined'){
		if (key in dict){
			return dict[key];
		}
	}
	return defaultValue;
}

function get(name){
   if(name=(new RegExp('[?&]'+encodeURIComponent(name)+'=([^&]*)')).exec(location.search))
      return decodeURIComponent(name[1]);
}

function decodeQueryData(uri){
	if(typeof uri == 'undefined')
		uri = location.search.substring(1);
	var data = uri?JSON.parse('{"' + uri.replace(/&/g, '","').replace(/=/g,'":"') + '"}',
                 function(key, value) { return key===""?value:decodeURIComponent(value) }):{}
    return data;
}
          
function encodeQueryData(data) {
    return Object.keys(data).map(function(key) {
        return [key, data[key]].map(encodeURIComponent).join("=");
    }).join("&");
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