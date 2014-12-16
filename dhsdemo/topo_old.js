var width = 900,
    height = 200;
    radius = 20;

var color = d3.scale.category20();

var svg = d3.select("body").append("svg")
    .attr("width", width)
    .attr("height", height)
    .append("g")
    .attr("transform", "translate(10,10)");

var force = d3.layout.force()
    .charge(-500)
    .linkDistance(50)
    .size([width, height]);

svg.append("rect")
    .attr("width", width)
    .attr("height", height)
    .style("stroke", "#000");

//Client Node
x=25, y=125
textbox = svg.append("text")
    .attr("x", x)
    .attr("y", y)
    .text("Client Node");

var textSize = textbox.node().getBBox();

node = svg.append("rect")
    .attr("x", x-5)
    .attr("y", y-45)
    .attr("width", textSize.width + 10)
    .attr("height", textSize.height + 10)
    
//Cache Node
x=325, y=125
textbox = svg.append("text")
    .attr("x", x)
    .attr("y", y)
    .text("Cache");

var textSize = textbox.node().getBBox();

node = svg.append("rect")
    .attr("x", x-5)
    .attr("y", y-45)
    .attr("width", textSize.width + 10)
    .attr("height", textSize.height + 10)
    
//LAN
svg.append("rect")
    .attr("x", 570)
    .attr("y", 50)
    .style("fill", "steelblue")
    .style('stroke', function(d) { return d3.rgb("steelblue").darker().toString(); })
    .style("stroke-width", "2")
    .attr("width", 20)
    .attr("height", 120);  
    
//Auth Node
x=725, y=65
textbox = svg.append("text")
    .attr("x", x)
    .attr("y", y)
    .text("Auth");

var textSize = textbox.node().getBBox();

svg.append("rect")
    .attr("x", x-5)
    .attr("y", y-45)
    .attr("width", textSize.width + 10)
    .attr("height", textSize.height + 10)
    .style("stroke", "green")
    
//Attacker Node
x=725, y=165
textbox = svg.append("text")
    .attr("x", x)
    .attr("y", y)
    .text("Attacker");

var textSize = textbox.node().getBBox();

svg.append("rect")
    .attr("x", x-5)
    .attr("y", y-45)
    .attr("width", textSize.width + 10)
    .attr("height", textSize.height + 10)
    .style("stroke", "red");
    
svg.append("line")
    .attr("id", "path1")
    .attr("x1", 240)
    .attr("y1", 110)
    .attr("x2", 320)
    .attr("y2", 110)

svg.append("line")
    .attr("id", "path2")
    .attr("x1", 445)
    .attr("y1", 110)
    .attr("x2", 570)
    .attr("y2", 110)
    
var diagonal = d3.svg.diagonal();
var s = {x: 590, y: 110};
var d = {x: 720, y: 50}
diag = diagonal({source: s, target: d});

svg.append("path")
    .attr("id", "pathauth")
    .attr("d", diag)

var diagonal = d3.svg.diagonal();
var s = {x: 590, y: 110};
var d = {x: 720, y: 150}
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
	document.getElementById("pathauth").setAttribute("stroke", "lightgrey")
	document.getElementById("pathattack").setAttribute("stroke", "blue")
}, 20000);

setTimeout(function(){
	document.getElementById("pathauth").setAttribute("stroke", "blue")
	document.getElementById("pathattack").setAttribute("stroke", "lightgrey")
}, 40000);

setTimeout(function(){
	document.getElementById("path1").setAttribute("stroke", "lightgrey")
	document.getElementById("path2").setAttribute("stroke", "lightgrey")
	document.getElementById("pathauth").setAttribute("stroke", "lightgrey")
	document.getElementById("pathattack").setAttribute("stroke", "lightgrey")
}, 60000);
