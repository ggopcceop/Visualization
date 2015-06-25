function drawHexGrid(svg, height, width, data){
    
    valueMax = d3.max(data, function(d){ return d.value} );

    var quantize = d3.scale.quantile()
        .domain([0, valueMax / 10])
        .range(d3.range(9).map(function(i) { return "q" + i + "-9"; }));

    var domain = new Array();
    for(var i = -40, j = 0; i <= 40; i++, j++){
        domain[j] = i;
    }
    var x = d3.scale.ordinal().rangeBands([0, width], .15)
            //.domain(data.map(function(d) { return  d.x; }));
            .domain(domain.map(function(d) { return d }))

    var y = d3.scale.ordinal().rangeBands([0, height], .15)
            //.domain(data.map(function(d) { return  d.y; }));
            .domain(domain.map(function(d) { return d }))

    var chart = svg.append("g")
        .attr("class", "gridChart")

    chart.selectAll(".bar").data(data).enter().append("rect")
        .attr("class", function(d) { return quantize(d.value); })
        .attr("x", function(d) { return x(d.x); })
        .attr("y", function(d) { return y(d.y); })
        .attr("width", x.rangeBand())
        .attr("height", y.rangeBand())
       

    return chart;
}


function drawHeightChart(svg, height, width, data){
    data = data.reverse();
    data.forEach(function(d){
        d.height = +d.height;
        d.value = +d.value;
    });

    //leave some margin space for displaying number on the left
    var margin_left = 100;
    //leave some margin space for displaying years on the buttom
    var margin_buttom = 50;

    var min = d3.min(data, function(d) {return d.height} )
    var max = d3.max(data, function(d) {return d.height} )

    var heightValue = d3.scale.ordinal()
            .rangeBands([0, height - margin_buttom], .5)
            .domain(data.map(function(d) { return  d.height; }))

    var countX = d3.scale.linear()
            .range([0, width - margin_left])
            .domain([0, d3.max(data, function(d) {return d.value })])

    //y axis to display lines 
    var yAxis = d3.svg.axis()
        .scale(heightValue)
        .tickValues(heightValue.domain().filter(function(d, i) { return !((i+1) % Math.floor(heightValue.domain().length/16)); }))
        .orient("left")
        

    //append a group to draw chart
    var chart = svg.append("g").attr("class", "barChart")
        
        
    //draw the y axis, lines acoress the chart
    chart.append("g")
        .attr("class", "y axis")
        .attr("transform", "translate(99,0)")
        .call(yAxis)
      
    //draw bars
    chart.selectAll(".bar").data(data).enter().append("rect")
        .attr("class", "bar")
        .attr("x", function(d) { return margin_left; })
        .attr("y", function(d) { return heightValue( d.height); })
        .attr("width", function(d) { return countX( d.value); })
        .attr("height", heightValue.rangeBand())


    

    d3.select("#control").append("div")
        .attr("id", "slider")
        .style("height", function(){ return (height - margin_buttom); })

    $("#slider").slider({
        orientation: "vertical",
        range: true,
        min: min,
        max: max,
        values: [min , max],
        slide: function(event, ui){
            var maxv = d3.min([ui.values[1], data.length]);
            var minv = d3.max([ui.values[0], 0]);

            subdata = data.filter(function(d){ return (d.height < maxv && d.height > minv); })

            heightValue.domain(subdata.map(function(d) { return  d.height; }));

            console.log( Math.floor(heightValue.domain().length/16) );

            yAxis.tickValues(heightValue.domain().filter(function(d, i) { return !((i % Math.floor(heightValue.domain().length/16)) + 1); }))


            chart.select(".y.axis").call(yAxis);

            d3.selectAll(".bar").remove();

            chart.selectAll(".bar").data(subdata)
                .enter().append("rect")
                .attr("class", "bar")
                .attr("x", function(d) { return margin_left; })
                .attr("y", function(d) { return heightValue(d.height); })
                .attr("width", function(d) { return countX(d.value); })
                .attr("height", heightValue.rangeBand())

                
        }
    });
       

    return chart;
}

