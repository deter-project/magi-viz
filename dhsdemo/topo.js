var width = 590,
    height = 150;
    radius = 20;

var color = d3.scale.category20();

var svg = d3.select("body").append("svg")
    .attr("width", width)
    .attr("height", height)
    .append("g")
    .attr("transform", "translate(5,5)");

var force = d3.layout.force()
    .charge(-500)
    .linkDistance(50)
    .size([width, height]);

svg.append("rect")
    .attr("width", width)
    .attr("height", height)
    .style("stroke", "#000");

//Client Node
x=15, y=82
textbox = svg.append("text")
    .attr("x", x)
    .attr("y", y)
    .text("Client Node");

var textSize = textbox.node().getBBox();

node = svg.append("rect")
    .attr("x", x-5)
    .attr("y", y-27)
    .attr("width", textSize.width + 10)
    .attr("height", textSize.height + 10)

svg.append("line")
    .attr("id", "path1")
    .attr("x1", 147)
    .attr("y1", 75)
    .attr("x2", 220)
    .attr("y2", 75)

//Cache Node
x=225, y=82
textbox = svg.append("text")
    .attr("x", x)
    .attr("y", y)
    .text("Cache");

var textSize = textbox.node().getBBox();

node = svg.append("rect")
    .attr("x", x-5)
    .attr("y", y-27)
    .attr("width", textSize.width + 10)
    .attr("height", textSize.height + 10)

svg.append("line")
    .attr("id", "path2")
    .attr("x1", 300)
    .attr("y1", 75)
    .attr("x2", 375)
    .attr("y2", 75)
    
//LAN
svg.append("rect")
    .attr("x", 375)
    .attr("y", 15)
    .style("fill", "steelblue")
    .style('stroke', function(d) { return d3.rgb("steelblue").darker().toString(); })
    .style("stroke-width", "2")
    .attr("width", 20)
    .attr("height", 120);  
    
//Auth Node
x=480, y=40
textbox = svg.append("text")
    .attr("x", x)
    .attr("y", y)
    .text("Auth");

var textSize = textbox.node().getBBox();

svg.append("rect")
    .attr("x", x-5)
    .attr("y", y-27)
    .attr("width", textSize.width + 10)
    .attr("height", textSize.height + 10)
    .style("stroke", "blue")
    
//Attacker Node
x=480, y=120
textbox = svg.append("text")
    .attr("x", x)
    .attr("y", y)
    .text("Attacker");

var textSize = textbox.node().getBBox();

svg.append("rect")
    .attr("x", x-5)
    .attr("y", y-27)
    .attr("width", textSize.width + 10)
    .attr("height", textSize.height + 10)
    .style("stroke", "red");
    
var diagonal = d3.svg.diagonal();
var s = {x: 395, y: 75};
var d = {x: 475, y: 30}
diag = diagonal({source: s, target: d});

svg.append("path")
    .attr("id", "pathauth")
    .attr("d", diag)

var diagonal = d3.svg.diagonal();
var s = {x: 395, y: 75};
var d = {x: 475, y: 115}
diag = diagonal({source: s, target: d});

svg.append("path")
    .attr("id", "pathattack")
    .attr("d", diag)

setTimeout(function(){
	document.getElementById("path1").setAttribute("stroke", "blue")
	document.getElementById("path2").setAttribute("stroke", "blue")
	document.getElementById("pathauth").setAttribute("stroke", "blue")
	document.getElementById("pathattack").setAttribute("stroke", "lightgrey")
}, 0);

setTimeout(function(){
	document.getElementById("path1").setAttribute("stroke", "red")
	document.getElementById("path2").setAttribute("stroke", "red")
	document.getElementById("pathauth").setAttribute("stroke", "lightgrey")
	document.getElementById("pathattack").setAttribute("stroke", "red")
}, 20000);

setTimeout(function(){
	document.getElementById("path1").setAttribute("stroke", "blue")
	document.getElementById("path2").setAttribute("stroke", "blue")
	document.getElementById("pathauth").setAttribute("stroke", "blue")
	document.getElementById("pathattack").setAttribute("stroke", "lightgrey")
}, 40000);

setTimeout(function(){
	document.getElementById("path1").setAttribute("stroke", "lightgrey")
	document.getElementById("path2").setAttribute("stroke", "lightgrey")
	document.getElementById("pathauth").setAttribute("stroke", "lightgrey")
	document.getElementById("pathattack").setAttribute("stroke", "lightgrey")
}, 60000);
