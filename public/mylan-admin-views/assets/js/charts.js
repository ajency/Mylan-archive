/* Webarch Admin Dashboard 
/* This JS is Only DEMO Purposes 
-----------------------------------------------------------------*/	
$(document).ready(function() {		

	$("#sparkline-pie").sparkline([4,9], {
		type: 'pie',
		width: '100%',
		height: '100%',
		sliceColors: ['#53C1B7','#bbb'],
		offset: 10,
		borderWidth: 0,
		borderColor: '#000000 '
	});
	
	Morris.Line({
	  element: 'line-example',
	  data: [
		{ y: '2006', a: 50, b: 40 },
		{ y: '2007', a: 65,  b: 55 },
		{ y: '2008', a: 50,  b: 40 },
		{ y: '2009', a: 75,  b: 65 },
		{ y: '2010', a: 50,  b: 40 },
		{ y: '2011', a: 75,  b: 65 },
		{ y: '2012', a: 100, b: 90 }
	  ],
	  xkey: 'y',
	  ykeys: ['a', 'b'],
	  labels: ['Series A', 'Series B'],
	  lineColors:['#0aa699','#d1dade'],
	});
	
	//Bar Chart  - Jquert flot
	
    var d1_1 = [
        
        [1328054400000, 20],
        
        [1333238400000, 30]
        
    ];
 
 
    var d1_4 = [
        
        [1328054400000, 30],
        
        [1333238400000, 20]
        
    ];
 
    var data1 = [
        {
            
            data: d1_1,
            bars: {
                show: true,
                barWidth: 40*24*60*60*300,
                fill: true,
                lineWidth:0,
                order: 1,
                fillColor:  "rgba(243, 89, 88, 0.7)"
            },
            color: "rgba(243, 89, 88, 0.7)"
        },
        
        {
           
            data: d1_4,
            bars: {
                    show: true,
                barWidth: 40*24*60*60*300,
                fill: true,
                lineWidth: 0,
                order: 4,
                fillColor:  "rgba(0, 144, 217, 0.7)"
            },
            color: "rgba(0, 144, 217, 0.7)"
        },

    ];
 
    $.plot($("#placeholder-bar-chart"), data1, {
        xaxis: {
            min: (new Date(2011, 11, 15)).getTime(),
            max: (new Date(2012, 04, 18)).getTime(),
            mode: "time",
            timeformat: "%b",
            tickSize: [1, "month"],
            monthNames: ["","","Project Name","","",],
            tickLength: 0, // hide gridlines
            axisLabel: 'Month',
            axisLabelUseCanvas: true,
            axisLabelFontSizePixels: 12,
            axisLabelFontFamily: 'Verdana, Arial, Helvetica, Tahoma, sans-serif',
            axisLabelPadding: 5,
        },
        yaxis: {
            axisLabel: 'Value',
            axisLabelUseCanvas: true,
            axisLabelFontSizePixels: 12,
            axisLabelFontFamily: 'Verdana, Arial, Helvetica, Tahoma, sans-serif',
            axisLabelPadding: 5
        },
        grid: {
            hoverable: true,
            clickable: false,
            borderWidth: 1,
			borderColor:'#f0f0f0',
			labelMargin:8,
        },
        series: {
            shadowSize: 1
        }
    });
 
 
    function getMonthName(newTimestamp) {
        var d = new Date(newTimestamp);
 
        var numericMonth = d.getMonth();
        var monthArray = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
 
        var alphaMonth = monthArray[numericMonth];
 
        return alphaMonth;
    }
 

	 // ORDERED CHART
    var data2 = [
        {
            label: "Product 1",
            data: d1_1,
            bars: {
                show: true,
                barWidth: 12*24*60*60*300*2,
                fill: true,
                lineWidth:0,
                order: 0,
                fillColor:  "rgba(243, 89, 88, 0.7)"
            },
            color: "rgba(243, 89, 88, 0.7)"
        },
        {
            label: "Product 2",
            data: d1_2,
            bars: {
                show: true,
                barWidth: 12*24*60*60*300*2,
                fill: true,
                lineWidth: 0,
                order: 0,
                fillColor:  "rgba(251, 176, 94, 0.7)"
            },
            color: "rgba(251, 176, 94, 0.7)"
        },
        {
            label: "Product 3",
            data: d1_3,
            bars: {
                show: true,
                barWidth: 12*24*60*60*300*2,
                fill: true,
                lineWidth: 0,
                order: 0,
                fillColor:  "rgba(10, 166, 153, 0.7)"
            },
            color: "rgba(10, 166, 153, 0.7)"
        },
        {
            label: "Product 4",
            data: d1_4,
            bars: {
                    show: true,
                barWidth: 12*24*60*60*300*2,
                fill: true,
                lineWidth: 0,
                order: 0,
                fillColor:  "rgba(0, 144, 217, 0.7)"
            },
            color: "rgba(0, 144, 217, 0.7)"
        },

    ];
	//BAR CHART 1
	
 
    var d1_2 = [
        
        [1328054400000, 30],
        [1330560000000, 60],
        [1333238400000, 35]
        
    ];
 
    var d1_3 = [
        
        [1328054400000, 40],
        [1330560000000, 30],
        [1333238400000, 20]
        
    ];
 
 
    var data1 = [
      
        {
            
            data: d1_2,
            bars: {
                show: true,
                barWidth: 25*24*60*60*300,
                fill: true,
                lineWidth: 0,
                order: 2,
                fillColor:  "rgba(251, 176, 94, 0.7)"
            },
            color: "rgba(251, 176, 94, 0.7)"
        },
        {
            
            data: d1_3,
            bars: {
                show: true,
                barWidth: 25*24*60*60*300,
                fill: true,
                lineWidth: 0,
                order: 3,
                fillColor:  "rgba(10, 166, 153, 0.7)"
            },
            color: "rgba(10, 166, 153, 0.7)"
        },
      

    ];
 
    $.plot($("#placeholder-bar-chart1"), data1, {
        xaxis: {
            min: (new Date(2011, 11, 15)).getTime(),
            max: (new Date(2012, 04, 18)).getTime(),
            mode: "time",
            timeformat: "%b",
            tickSize: [1, "month"],
            monthNames: ["","","Project Name","","",],
            tickLength: 0, // hide gridlines
            axisLabel: 'Month',
            axisLabelUseCanvas: true,
            axisLabelFontSizePixels: 12,
            axisLabelFontFamily: 'Verdana, Arial, Helvetica, Tahoma, sans-serif',
            axisLabelPadding: 5,
        },
        yaxis: {
            axisLabel: 'Value',
            axisLabelUseCanvas: true,
            axisLabelFontSizePixels: 12,
            axisLabelFontFamily: 'Verdana, Arial, Helvetica, Tahoma, sans-serif',
            axisLabelPadding: 5
        },
        grid: {
            hoverable: true,
            clickable: false,
            borderWidth: 1,
			borderColor:'#f0f0f0',
			labelMargin:8,
        },
        series: {
            shadowSize: 1
        }
    });
 
 
    function getMonthName(newTimestamp) {
        var d = new Date(newTimestamp);
 
        var numericMonth = d.getMonth();
        var monthArray = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
 
        var alphaMonth = monthArray[numericMonth];
 
        return alphaMonth;
    }
 

	 // ORDERED CHART
    var data2 = [
        {
            label: "Product 1",
            data: d1_1,
            bars: {
                show: true,
                barWidth: 12*24*60*60*300*2,
                fill: true,
                lineWidth:0,
                order: 0,
                fillColor:  "rgba(243, 89, 88, 0.7)"
            },
            color: "rgba(243, 89, 88, 0.7)"
        },
        {
            label: "Product 2",
            data: d1_2,
            bars: {
                show: true,
                barWidth: 12*24*60*60*300*2,
                fill: true,
                lineWidth: 0,
                order: 0,
                fillColor:  "rgba(251, 176, 94, 0.7)"
            },
            color: "rgba(251, 176, 94, 0.7)"
        },
        {
            label: "Product 3",
            data: d1_3,
            bars: {
                show: true,
                barWidth: 12*24*60*60*300*2,
                fill: true,
                lineWidth: 0,
                order: 0,
                fillColor:  "rgba(10, 166, 153, 0.7)"
            },
            color: "rgba(10, 166, 153, 0.7)"
        },
        {
            label: "Product 4",
            data: d1_4,
            bars: {
                    show: true,
                barWidth: 12*24*60*60*300*2,
                fill: true,
                lineWidth: 0,
                order: 0,
                fillColor:  "rgba(0, 144, 217, 0.7)"
            },
            color: "rgba(0, 144, 217, 0.7)"
        },

    ];
	
	// DATA DEFINITION
	function getData() {
		var data = [];

		data.push({
			data: [[0, 1], [1, 4], [2, 2]]
		});

		data.push({
			data: [[0, 5], [1, 3], [2, 1]]
		});

		return data;
	}
	
	
});