var width  = 250,
    height = 450,
    colors = d3.scale.category10();

var tree = d3.layout.tree()
    .size([10, height - 50]);

var root = {id: "setup", label: 'Setup'},
    nodes = tree(root);

var diagonal = d3.svg.diagonal();

var svg = d3.select("body").append("svg")
    .attr("width", width)
    .attr("height", height)
    .append("g")
    .attr("transform", "translate(10,10)");

var node = svg.selectAll(".node"),
    link = svg.selectAll("path");
    text = svg.selectAll("text");

addNode(root, root);

updateNode(root, "Setup");

plot("dnsmitm", 1);
var duration = 500;
//    timer = setInterval(populateGraph, duration);

function addNode(n, p, eventType) {
  if (n == p){
	  n.parent = n;
	  n.px = n.x;
	  n.py = n.y;
  }
  else{
	  if (p.children) p.children.push(n); else p.children = [n];
	    nodes.push(n);
  }
    
  // Recompute the layout and data join.
  node = node.data(tree.nodes(root), function(d) { return d.id; });
  link = link.data(tree.links(nodes), function(d) { return d.source.id + "-" + d.target.id; });

  //console.log(nodes);
  
  // Add entering nodes in the parent’s old position.
  
  nodeEnter = node.enter();
  //console.log(nodeEnter);
  
  var rect = nodeEnter.append("rect")
      .attr("class", "node")
      .style('fill', function(d) { return getColor(eventType); })
      .style('stroke', function(d) { return d3.rgb(getColor(eventType)).darker().toString(); })
      .attr("x", function(d) { return d.parent.px; })
      .attr("y", function(d) { return d.parent.py; })
      .attr("rx", "5")
      .attr("ry", "5");
      
  var insertLinebreaks = function (d) {
      var el = d3.select(this);
      //console.log(el)
      var words = d.label.split('\n');
      el.text('');

      for (var i = 0; i < words.length; i++) {
          var tspan = el.append('tspan').text(words[i]);
              tspan.attr('dy', '1em')
              	   .attr('x', d.parent.px)
              	   .attr('dx', 2);
      }
  };

  var textbox = nodeEnter.append('text')
  textbox.attr('x', function(d) { return d.parent.px; })
         .attr('y', function(d) { return d.parent.py; })
  textbox.each(insertLinebreaks);
      
  //console.log(rect);
  //console.log(text);
  //console.log(text[0]);
  
  var textSize = textbox.node().getBBox();
  rect.attr("width", textSize.width + 5)
	  .attr("height", textSize.height + 5);
	
  //console.log(textSize)
  //console.log(textSize.width)
  // Add entering links in the parent’s old position.
  link.enter().insert("path", ".node")
      .attr("class", "link")
      .attr("d", function(d) {
        var o = {x: d.source.px + d.source.label.length * 2, y: d.source.py};
        return diagonal({source: o, target: o});
      })
      .attr("ddx", textSize.width)

  // Transition nodes and links to their new positions.
  var t = svg.transition()
      .duration(duration);

  t.selectAll("path")
      .attr("d", function(d) {
    	var el = d3.select(this);
        var os = {x: d.source.x + d.source.label.length * 2, y: d.source.y };
    	var od = {x: d.target.x + el.node().getAttribute("ddx")/2, y: d.target.y};
        return diagonal({source: os, target: od});
      });

  t.selectAll(".node")
      .attr("x", function(d) { return d.px = d.x; })
      .attr("y", function(d) { return d.py = d.y; });
      
  textbox = t.selectAll("text")
      .attr("x", function(d) { return d.px = d.x; })
      .attr("y", function(d) { return d.py = d.y; });

  t.selectAll("tspan")
  	.attr("x", function(d) { return d.px = d.x; })
  
}

function requestData(){
	//alert('populating')
	$.ajax({
		type: 'GET',
		url: 'getOrchData.php',
		data: "dbhost=localhost" + 
			"&dbPort=" + dbPort + 
			"&dbName=" + dbName +
			"&lastTimestamp=" + lastTimestamp + 
			"&recordsLimit=" + "0",
		success: function(records) {
			//alert(records);
			//alert(lastTimestamp);
			if(records.length != 0)
		    {
		    	for(var i=0; i < records.length; i++)
		     	{
		     		record = records[i];
		     		streamName = record[1];
		     		eventItr = record[2];
		     		eventType = record[3];
		     		eventLabel = record[4];
		     		
		     		if (eventItr == 0)
		     			pid = 'setup';
		     		else
		     			pid = streamName+":"+(eventItr-1)
		     		
		     		var p = findNode(pid)
		     		var nid = streamName+":"+eventItr
		     		var n = {id: nid, label: eventLabel}
		     		
		     		addNode(n, p, eventType)
		     		
			 	}
		     	lastTimestamp = records[records.length - 1][0];
		     	//if (records[records.length - 1][3] == "exit") return clearInterval(timer);
	     		if(records[records.length - 1][3] != "exit")
	     			setTimeout(requestData, duration);
			}else{
				lastTimestamp = lastTimestamp + (duration/1000);
				setTimeout(requestData, duration);
			}
		},
	});
}

function updateNode(n, newLabel, eventType){
	n.label = newLabel;
	svg.selectAll("text")
    	.attr("text", function(d) { return d.label; })
}

function findNode(nodeId){
	for(itr=0; itr<nodes.length; itr++){
		n = nodes[itr];
		if(n.id == nodeId)
			return n;
	}
}

function getColor(eventType){
	if (eventType == 'event')
		return "lightblue"
	else if (eventType == 'trigger')
		return "orange"
	else if (eventType == 'triggerComplete')
		return "green"
	else if (eventType == 'exit')
		return "red"
	else
		return "steelblue"
}
	