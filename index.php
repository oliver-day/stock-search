<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>Stock Search</title>
        <base target="_blank">
    <style>
        a {
            color: #3745ec;
            text-decoration: none;
            font-family: Arial, Helvetica, sans-serif;
            font-weight: 500;
        }
        a:hover {
            color: black;
        }
        .arrow {
            height: 15px;
            padding-left: 2px;
        }
        #arrow-img {
            display: block;
            margin-left: auto;
            margin-right: auto;
            margin-top: -1.75em;
            padding-bottom: 0.5em;
            width: 4%;
        }
        #article-button-container {
            background-color: #ffffff;
            margin-left: auto;
            margin-right: auto;
            width: 60%;
        }
        #article-button-text {
            color: #bfbfbf;
            font-family: Arial, Helvetica, sans-serif;
            text-align: center;
        }        
        .button-container {
            padding-top: 5px;
            padding-bottom: 5px;
        }
        #chart-container {
            background-color: #FFFFFF;
            border-color: black;
            border-style: solid;
            border-width: thin;
            margin-left: auto;
            margin-right: auto;
            margin-bottom: 10px;
            padding: 1px;
        }
        .highcharts-container {
            width:100% !important;
            height:100% !important;
        }
        fieldset {
            border-color: #FFFFFF;
        }
        .form-container {
            background-color: #f5f5f5;
            top: 0%;
            left: 50%;
            margin-top: 0;
            margin-left: auto;
            margin-right: auto;
            text-align: left;
            max-width: 500px;
            width: 30%;
        }
        #form-title {
            margin-top: -25px;
            font-style: italic;
            text-align: center;
        }
        #form-title h1 {
            border-bottom: 1px solid #d7d7d7;
            border-bottom-width: thin;
        }
        .indicators {
            color: #3745ec;
            cursor: pointer; 
            cursor: hand;
            font-family: Arial, Helvetica, sans-serif;
            font-weight: 500;
            padding-left: .5em;
            padding-right: .5em;
        }
        .indicators:hover {
            color: blue;
        }
        input[type=submit], input[type=reset] {
            width: 60px;
        }
        .tblColLeft {
            /* background-color: #f5f5f5; */
            border-color: black;
            border-style: solid;
            border-width: 1px;
            font-family: Arial, Helvetica, sans-serif;
            font-weight: 600;
            padding-top: 0.25em;
            padding-bottom: 0.25em;
            text-align: left;
        }
        .tblColRight {
            /* background-color: #fbfbfb; */
            border-color: black;
            border-style: solid;
            border-width: 1px;
            font-family: Arial, Helvetica, sans-serif;
            font-weight: 400;
            padding-top: 0.25em;
            padding-bottom: 0.25em;
            text-align: center;
        }
        #tbl-article {
            display: none;
        }
    </style>

    <!-- Bootstap 4 CDN -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <!-- Script for HighChart API -->
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script type="text/javascript">
        "use strict";

        function clearResults() {

            var symbolField = document.querySelector('#symbol-field');
            symbolField.value = "";

            var tblData = document.querySelector('#tbl-stock-data');
            if(tblData) {
                tblData.parentNode.removeChild(tblData);
            }

            var chart = document.querySelector('#chart-container');
            if(chart) {
                chart.parentNode.removeChild(chart);
            }

            var artBut = document.querySelector('#article-button-container');
            if(artBut) {
                artBut.parentNode.removeChild(artBut);
            }

            var tblArt = document.querySelector('#tbl-article');
            if(tblArt) {
                tblArt.parentNode.removeChild(tblArt);
            }
            return true;
        }

        function formatTitleDate(rawDateStr) {

            var formattedDate = rawDateStr.substr(5, 2) + "/" + rawDateStr.substr(8, 2) + "/" + rawDateStr.substr(0, 4);
            return formattedDate;
        }

        function formatXAxisDate(dateArray) {

            var xAxisDates = dateArray.map(function(origDateStr) {
                
                var xDate = origDateStr.substr(5, 2) + "/" + origDateStr.substr(8, 2);
                return xDate;
            });
            return xAxisDates;
        }
        function convertStrAryToFloatAry(strAry) {
            
            var floatAry = strAry.map(function(str) {
                return parseFloat(str, 10);
            });
            return floatAry;
        }

        function requestJSON(api, callback) {
            
            var request = new XMLHttpRequest();
            request.open("GET", api, true);
            request.onload = function(){
                callback(request.responseText);
            }
            request.send();
        }

        function buildPriceChart(symbolData, dateData, priceData, volumeData) {

            var xAxisDates = formatXAxisDate(dateData);
            var priceDataFloat = convertStrAryToFloatAry(priceData);
            var volumeDataFloat = convertStrAryToFloatAry(volumeData);

            Highcharts.chart('chart-container', {
                chart: {
                    zoomType: 'xy',
                    height: (9 / 16 * 100) + '%' // 16:9 ratio
                },
                title: {
                    text: 'Stock Price (' + formatTitleDate(dateData[dateData.length-1]) + ')'
                },
                subtitle: {
                    useHTML: true,
                    text: '<a href="https://www.alphavantage.co/" target="_blank">Source: Alpha Village</a>'
                },
                xAxis: [{
                    categories: xAxisDates,
                    crosshair: true,
                    tickInterval: 7
                }],
                yAxis: [{ // Primary yAxis
                    labels: {
                        format: '{value}',
                        style: {
                            color: Highcharts.getOptions().colors[1]
                        }
                    },
                    title: {
                        text: 'Stock Price',
                        style: {
                            color: Highcharts.getOptions().colors[1]
                        }
                    },
                    tickInterval: 5,
                    min: Math.min(...priceDataFloat) - 2
                }, { // Secondary yAxis
                    title: {
                        text: 'Volume',
                        style: {
                            color: Highcharts.getOptions().colors[1]
                        }
                    },
                    labels: {
                        style: {
                            color: Highcharts.getOptions().colors[1]
                        }
                    },
                    max: Math.max(...volumeDataFloat) * 5.25,
                    opposite: true
                }],
                tooltip: {
                    enabled: true
                },
                legend: {
                    layout: 'vertical',
                    align: 'right',
                    verticalAlign: 'middle',
                    backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'
                },
                series: [{
                    name: symbolData,
                    type: 'area',
                    yAxis: 0,
                    data: priceDataFloat,
                    marker: {
                        enabled: false
                    },
                    tooltip: {
                        valueSuffix: '',
                        pointFormat: "{point.y:.2f}"
                    },
                    color: '#e91c04',
                    fillOpacity: 0.6
                },
                {
                    name: symbolData + ' Volume',
                    type: 'column',
                    yAxis: 1,
                    data: volumeDataFloat,
                    tooltip: {
                        valueSuffix: 'M'
                    },
                    color: '#bfd8ff'
                }],

                responsive: {
                    rules: [{
                        condition: {
                            maxWidth: 1000
                        },
                        chartOptions: {
                            legend: {
                                align: 'center',
                                verticalAlign: 'bottom',
                                layout: 'horizontal'
                            }
                        }
                    },
                    {
                        condition: {
                            maxWidth: 500
                        },
                        // Make the labels less space demanding on mobile
                        chartOptions: {
                            yAxis: [{
                                labels: {
                                    align: 'left',
                                    x: 0,
                                    y: -2
                                },
                                title: {
                                    text: ''
                                }
                            },
                            {
                                labels: {
                                    align: 'right',
                                    x: 0,
                                    y: -2
                                },
                                title: {
                                    text: ''
                                }
                            }]
                        }
                    }
                ]
                } 
            });
        }

        function buildSMAChart(symbolData) {

            var smaAPI = "https://www.alphavantage.co/query?function=SMA&symbol=" + symbolData + "&interval=daily&time_period=10&series_type=close&apikey=4W0M5ZYP8HVJJ1KD";
            requestJSON(smaAPI, function(response) {
                
                let smaObj = JSON.parse(response);
                // console.log(smaObj);

                var allDateKeys = Object.keys(smaObj['Technical Analysis: SMA']);
                var subsetOfDateKeys = allDateKeys.splice(0, 134);
                subsetOfDateKeys.reverse();
                let adjSubsetOfDateKeys = formatXAxisDate(subsetOfDateKeys);
                
                var smaStrData = [];
                for(var date of subsetOfDateKeys) {
                    smaStrData.push(smaObj['Technical Analysis: SMA'][date]['SMA']);
                }
                var smaData = convertStrAryToFloatAry(smaStrData);

                // console.log(adjSubsetOfDateKeys);
                // console.log(smaObj);
                
                Highcharts.chart('chart-container', {
                    chart: {
                        zoomType: 'xy',
                        height: (9 / 16 * 100) + '%' // 16:9 ratio
                    },
                    title: {
                        text: 'Simple Moving Average (SMA)'
                    },
                    subtitle: {
                        useHTML: true,
                        text: '<a href="https://www.alphavantage.co/" target="_blank">Source: Alpha Village</a>'
                    },
                    xAxis: {
                        categories: adjSubsetOfDateKeys,
                        tickInterval: 7
                    },
                    yAxis: {
                        title: {
                            text: 'SMA',
                        },
                        style: {
                            color: Highcharts.getOptions().colors[1]
                        }
                    },
                    legend: {
                        layout: 'vertical',
                        align: 'right',
                        verticalAlign: 'middle',
                        backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'
                    },
                    series: [{
                        name: symbolData,
                        data: smaData,
                        color: '#FA8072',
                        marker: {
                            enabled: true,
                            radius: 2.5,
                            lineWidth: 1,
                            fillColor:'red'
                        }
                    }],

                    responsive: {
                        rules: [{
                            condition: {
                                maxWidth: 1000
                            },
                            chartOptions: {
                                legend: {
                                    align: 'center',
                                    verticalAlign: 'bottom',
                                    layout: 'horizontal'
                                }
                            }
                        }]
                    }
                });
            });
        }

        function buildEMAChart(symbolData) {

            var emaAPI = "https://www.alphavantage.co/query?function=EMA&symbol=" + symbolData + "&interval=daily&time_period=10&series_type=close&apikey=4W0M5ZYP8HVJJ1KD";
            requestJSON(emaAPI, function(response) {
                
                let emaObj = JSON.parse(response);
                // console.log(smaObj);

                var allDateKeys = Object.keys(emaObj['Technical Analysis: EMA']);
                var subsetOfDateKeys = allDateKeys.splice(0, 134);
                subsetOfDateKeys.reverse();
                let adjSubsetOfDateKeys = formatXAxisDate(subsetOfDateKeys);
                
                var emaStrData = [];
                for(var date of subsetOfDateKeys) {
                    emaStrData.push(emaObj['Technical Analysis: EMA'][date]['EMA']);
                }
                var emaData = convertStrAryToFloatAry(emaStrData);

                // console.log(adjSubsetOfDateKeys);
                // console.log(emaObj);
                
                Highcharts.chart('chart-container', {
                    chart: {
                        zoomType: 'xy',
                        height: (9 / 16 * 100) + '%' // 16:9 ratio
                    },
                    title: {
                        text: 'Exponential Moving Average (EMA)'
                    },
                    subtitle: {
                        useHTML: true,
                        text: '<a href="https://www.alphavantage.co/" target="_blank">Source: Alpha Village</a>'
                    },
                    xAxis: {
                        categories: adjSubsetOfDateKeys,
                        tickInterval: 7
                    },
                    yAxis: {
                        title: {
                            text: 'EMA',
                        },
                        style: {
                            color: Highcharts.getOptions().colors[1]
                        }
                    },
                    legend: {
                        layout: 'vertical',
                        align: 'right',
                        verticalAlign: 'middle',
                        backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'
                    },
                    series: [{
                        name: symbolData,
                        data: emaData,
                        color: '#FA8072',
                        marker: {
                            enabled: true,
                            radius: 2.5,
                            lineWidth: 1,
                            fillColor:'red'
                        }
                    }],
                    responsive: {
                        rules: [{
                            condition: {
                                maxWidth: 1000
                            },
                            chartOptions: {
                                legend: {
                                    align: 'center',
                                    verticalAlign: 'bottom',
                                    layout: 'horizontal'
                                }
                            }
                        }]
                    }
                });
            });
        }

        function buildSTOCHChart(symbolData) {
            
            var stochAPI = "https://www.alphavantage.co/query?function=STOCH&symbol=" + symbolData + "&interval=daily&slowkmatype=1&slowdmatype=1&apikey=4W0M5ZYP8HVJJ1KD";
            requestJSON(stochAPI, function(response) {
                
                let stochObj = JSON.parse(response);
                // console.log(smaObj);

                var allDateKeys = Object.keys(stochObj['Technical Analysis: STOCH']);
                var subsetOfDateKeys = allDateKeys.splice(0, 134);
                subsetOfDateKeys.reverse();
                let adjSubsetOfDateKeys = formatXAxisDate(subsetOfDateKeys);
                
                // slowD
                var slowDStrData = [];
                for(var date of subsetOfDateKeys) {
                    slowDStrData.push(stochObj['Technical Analysis: STOCH'][date]['SlowD']);
                }
                var slowDData = convertStrAryToFloatAry(slowDStrData);


                // slowK
                var slowKStrData = [];
                for(var date of subsetOfDateKeys) {
                    slowKStrData.push(stochObj['Technical Analysis: STOCH'][date]['SlowK']);
                }
                var slowKData = convertStrAryToFloatAry(slowKStrData);

                // console.log(adjSubsetOfDateKeys);
                // console.log(stochObj);
                
                Highcharts.chart('chart-container', {
                    chart: {
                        zoomType: 'xy',
                        height: (9 / 16 * 100) + '%' // 16:9 ratio
                    },
                    title: {
                        text: 'Stochastic Oscillator (STOCH)'
                    },
                    subtitle: {
                        useHTML: true,
                        text: '<a href="https://www.alphavantage.co/" target="_blank">Source: Alpha Village</a>'
                    },
                    xAxis: {
                        categories: adjSubsetOfDateKeys,
                        tickInterval: 7
                    },
                    yAxis: {
                        title: {
                            text: 'STOCH',
                        },
                        style: {
                            color: Highcharts.getOptions().colors[1]
                        }
                    },
                    legend: {
                        layout: 'vertical',
                        align: 'right',
                        verticalAlign: 'middle',
                        backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'
                    },
                    series: [{
                        name: symbolData + ' SlowK',
                        data: slowKData,
                        color: '#FA8072',
                        marker: {
                            enabled: true,
                            radius: 2.5,
                            lineWidth: 1,
                            fillColor:'red'
                        }
                    },
                    {
                        name: symbolData + ' SlowD',
                        data: slowDData,
                        color: '#7dbbe7',
                        marker: {
                            enabled: true,
                            radius: 2.5,
                            lineWidth: 1,
                            fillColor:'blue'
                        }
                    }],
                    responsive: {
                        rules: [{
                            condition: {
                                maxWidth: 1000
                            },
                            chartOptions: {
                                legend: {
                                    align: 'center',
                                    verticalAlign: 'bottom',
                                    layout: 'horizontal'
                                }
                            }
                        }]
                    }
                });
            });
        }

        function buildRSIChart(symbolData) {
            
            var rsiAPI = "https://www.alphavantage.co/query?function=RSI&symbol=" + symbolData + "&interval=daily&time_period=10&series_type=close&apikey=4W0M5ZYP8HVJJ1KD";
            requestJSON(rsiAPI, function(response) {
                
                let rsiObj = JSON.parse(response);
                // console.log(smaObj);

                var allDateKeys = Object.keys(rsiObj['Technical Analysis: RSI']);
                var subsetOfDateKeys = allDateKeys.splice(0, 134);
                subsetOfDateKeys.reverse();
                let adjSubsetOfDateKeys = formatXAxisDate(subsetOfDateKeys);
                
                var rsiStrData = [];
                for(var date of subsetOfDateKeys) {
                    rsiStrData.push(rsiObj['Technical Analysis: RSI'][date]['RSI']);
                }
                var rsiData = convertStrAryToFloatAry(rsiStrData);

                // console.log(adjSubsetOfDateKeys);
                // console.log(rsiObj);
                
                Highcharts.chart('chart-container', {
                    chart: {
                        zoomType: 'xy',
                        height: (9 / 16 * 100) + '%' // 16:9 ratio
                    },
                    title: {
                        text: 'Relative Strength Index (RSI)'
                    },
                    subtitle: {
                        useHTML: true,
                        text: '<a href="https://www.alphavantage.co/" target="_blank">Source: Alpha Village</a>'
                    },
                    xAxis: {
                        categories: adjSubsetOfDateKeys,
                        tickInterval: 7
                    },
                    yAxis: {
                        title: {
                            text: 'RSI',
                        },
                        style: {
                            color: Highcharts.getOptions().colors[1]
                        }
                    },
                    legend: {
                        layout: 'vertical',
                        align: 'right',
                        verticalAlign: 'middle',
                        backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'
                    },
                    series: [{
                        name: symbolData,
                        data: rsiData,
                        color: '#FA8072',
                        marker: {
                            enabled: true,
                            radius: 2.5,
                            lineWidth: 1,
                            fillColor:'red'
                        }
                    }],
                    responsive: {
                        rules: [{
                            condition: {
                                maxWidth: 1000
                            },
                            chartOptions: {
                                legend: {
                                    align: 'center',
                                    verticalAlign: 'bottom',
                                    layout: 'horizontal'
                                }
                            }
                        }]
                    }
                });
            });
        }

        function buildADXChart(symbolData) {
    
            var adxAPI = "https://www.alphavantage.co/query?function=ADX&symbol=" + symbolData + "&interval=daily&time_period=10&series_type=close&apikey=4W0M5ZYP8HVJJ1KD";
            requestJSON(adxAPI, function(response) {
                
                let adxObj = JSON.parse(response);
                // console.log(smaObj);

                var allDateKeys = Object.keys(adxObj['Technical Analysis: ADX']);
                var subsetOfDateKeys = allDateKeys.splice(0, 134);
                subsetOfDateKeys.reverse();
                let adjSubsetOfDateKeys = formatXAxisDate(subsetOfDateKeys);
                
                var adxStrData = [];
                for(var date of subsetOfDateKeys) {
                    adxStrData.push(adxObj['Technical Analysis: ADX'][date]['ADX']);
                }
                var adxData = convertStrAryToFloatAry(adxStrData);

                // console.log(adjSubsetOfDateKeys);
                // console.log(adxObj);
                
                Highcharts.chart('chart-container', {
                    chart: {
                        zoomType: 'xy',
                        height: (9 / 16 * 100) + '%' // 16:9 ratio
                    },
                    title: {
                        text: 'Average Directional movement indeX (ADX)'
                    },
                    subtitle: {
                        useHTML: true,
                        text: '<a href="https://www.alphavantage.co/" target="_blank">Source: Alpha Village</a>'
                    },
                    xAxis: {
                        categories: adjSubsetOfDateKeys,
                        tickInterval: 7
                    },
                    yAxis: {
                        title: {
                            text: 'ADX',
                        },
                        style: {
                            color: Highcharts.getOptions().colors[1]
                        }
                    },
                    legend: {
                        layout: 'vertical',
                        align: 'right',
                        verticalAlign: 'middle',
                        backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'
                    },
                    series: [{
                        name: symbolData,
                        data: adxData,
                        color: '#FA8072',
                        marker: {
                            enabled: true,
                            radius: 2.5,
                            lineWidth: 1,
                            fillColor:'red'
                        }
                    }],
                    responsive: {
                        rules: [{
                            condition: {
                                maxWidth: 1000
                            },
                            chartOptions: {
                                legend: {
                                    align: 'center',
                                    verticalAlign: 'bottom',
                                    layout: 'horizontal'
                                }
                            }
                        }]
                    }
                });
            });                    
        } 

        function buildCCIChart(symbolData) {
            
            var cciAPI = "https://www.alphavantage.co/query?function=CCI&symbol=" + symbolData + "&interval=daily&time_period=10&series_type=close&apikey=4W0M5ZYP8HVJJ1KD";
            requestJSON(cciAPI, function(response) {
                
                let cciObj = JSON.parse(response);
                // console.log(smaObj);

                var allDateKeys = Object.keys(cciObj['Technical Analysis: CCI']);
                var subsetOfDateKeys = allDateKeys.splice(0, 134);
                subsetOfDateKeys.reverse();
                let adjSubsetOfDateKeys = formatXAxisDate(subsetOfDateKeys);
                
                var cciStrData = [];
                for(var date of subsetOfDateKeys) {
                    cciStrData.push(cciObj['Technical Analysis: CCI'][date]['CCI']);
                }
                var cciData = convertStrAryToFloatAry(cciStrData);

                // console.log(adjSubsetOfDateKeys);
                // console.log(cciObj);
                
                Highcharts.chart('chart-container', {
                    chart: {
                        zoomType: 'xy',
                        height: (9 / 16 * 100) + '%' // 16:9 ratio
                    },
                    title: {
                        text: 'Commodity Channel Index (CCI)'
                    },
                    subtitle: {
                        useHTML: true,
                        text: '<a href="https://www.alphavantage.co/" target="_blank">Source: Alpha Village</a>'
                    },
                    xAxis: {
                        categories: adjSubsetOfDateKeys,
                        tickInterval: 7
                    },
                    yAxis: {
                        title: {
                            text: 'CCI',
                        },
                        style: {
                            color: Highcharts.getOptions().colors[1]
                        }
                    },
                    legend: {
                        layout: 'vertical',
                        align: 'right',
                        verticalAlign: 'middle',
                        backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'
                    },
                    series: [{
                        name: symbolData,
                        data: cciData,
                        color: '#FA8072',
                        marker: {
                            enabled: true,
                            radius: 2.5,
                            lineWidth: 1,
                            fillColor:'red'
                        }
                    }],
                    responsive: {
                        rules: [{
                            condition: {
                                maxWidth: 1000
                            },
                            chartOptions: {
                                legend: {
                                    align: 'center',
                                    verticalAlign: 'bottom',
                                    layout: 'horizontal'
                                }
                            }
                        }]
                    }
                });
            });  
        }

        function buildBBANDSChart(symbolData) {
            
            var bbandsAPI = "https://www.alphavantage.co/query?function=BBANDS&symbol=" + symbolData + "&interval=daily&time_period=5&series_type=close&nbdevup=3&nbdevdn=3&apikey=4W0M5ZYP8HVJJ1KD";
            requestJSON(bbandsAPI, function(response) {
                
                let bbandsObj = JSON.parse(response);
                // console.log(smaObj);

                var allDateKeys = Object.keys(bbandsObj['Technical Analysis: BBANDS']);
                var subsetOfDateKeys = allDateKeys.splice(0, 134);
                subsetOfDateKeys.reverse();
                let adjSubsetOfDateKeys = formatXAxisDate(subsetOfDateKeys);
                
                // Low Band
                var lowBandStrData = [];
                for(var date of subsetOfDateKeys) {
                    lowBandStrData.push(bbandsObj['Technical Analysis: BBANDS'][date]['Real Lower Band']);
                }
                var lowBandData = convertStrAryToFloatAry(lowBandStrData);

                // Middle Band
                var midBandStrData = [];
                for(var date of subsetOfDateKeys) {
                    midBandStrData.push(bbandsObj['Technical Analysis: BBANDS'][date]['Real Middle Band']);
                }
                var midBandData = convertStrAryToFloatAry(midBandStrData);

                // Upper Band
                var upBandStrData = [];
                for(var date of subsetOfDateKeys) {
                    upBandStrData.push(bbandsObj['Technical Analysis: BBANDS'][date]['Real Upper Band']);
                }
                var upBandData = convertStrAryToFloatAry(upBandStrData);

                // console.log(adjSubsetOfDateKeys);
                // console.log(bbandsObj);
                
                Highcharts.chart('chart-container', {
                    chart: {
                        zoomType: 'xy',
                        height: (9 / 16 * 100) + '%' // 16:9 ratio
                    },
                    title: {
                        text: 'Bollinger Bands (BBANDS)'
                    },
                    subtitle: {
                        useHTML: true,
                        text: '<a href="https://www.alphavantage.co/" target="_blank">Source: Alpha Village</a>'
                    },
                    xAxis: {
                        categories: adjSubsetOfDateKeys,
                        tickInterval: 7
                    },
                    yAxis: {
                        title: {
                            text: 'BBANDS',
                        },
                        style: {
                            color: Highcharts.getOptions().colors[1]
                        }
                    },
                    legend: {
                        layout: 'vertical',
                        align: 'right',
                        verticalAlign: 'middle',
                        backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'
                    },
                    series: [{
                        name: symbolData + ' Real Middle Band',
                        data: midBandData,
                        color: '#FA8072',
                        marker: {
                            enabled: true,
                            radius: 2.5,
                            lineWidth: 1,
                            fillColor:'red'
                        }
                    },
                    {
                        name: symbolData + ' Real Upper Band',
                        data: upBandData,
                        color: 'black',
                        marker: {
                            enabled: true,
                            radius: 2.5,
                            lineWidth: 1,
                            fillColor:'black'
                        }
                    },
                    {
                        name: symbolData + ' Real Lower Band',
                        data: lowBandData,
                        color: '#94e888',
                        marker: {
                            enabled: true,
                            radius: 2.5,
                            lineWidth: 1,
                            fillColor:'#94e888'
                        }
                    }],
                    responsive: {
                        rules: [{
                            condition: {
                                maxWidth: 1000
                            },
                            chartOptions: {
                                legend: {
                                    align: 'center',
                                    verticalAlign: 'bottom',
                                    layout: 'horizontal'
                                }
                            }
                        }]
                    }
                });
            });
        }

        function buildMACDChart(symbolData) {
            
            var macdAPI = "https://www.alphavantage.co/query?function=MACD&symbol=" + symbolData + "&interval=daily&series_type=close&apikey=4W0M5ZYP8HVJJ1KD";
            requestJSON(macdAPI, function(response) {
                
                let macdObj = JSON.parse(response);
                // console.log(smaObj);

                var allDateKeys = Object.keys(macdObj['Technical Analysis: MACD']);
                var subsetOfDateKeys = allDateKeys.splice(0, 134);
                subsetOfDateKeys.reverse();
                let adjSubsetOfDateKeys = formatXAxisDate(subsetOfDateKeys);
                
                // MACD_Signal
                var macdSigStrData = [];
                for(var date of subsetOfDateKeys) {
                    macdSigStrData.push(macdObj['Technical Analysis: MACD'][date]['MACD_Signal']);
                }
                var macdSigData = convertStrAryToFloatAry(macdSigStrData);

                // MACD_Hist
                var macdHistStrData = [];
                for(var date of subsetOfDateKeys) {
                    macdHistStrData.push(macdObj['Technical Analysis: MACD'][date]['MACD_Hist']);
                }
                var macdHistData = convertStrAryToFloatAry(macdHistStrData);

                // Upper Band
                var macdStrData = [];
                for(var date of subsetOfDateKeys) {
                    macdStrData.push(macdObj['Technical Analysis: MACD'][date]['MACD']);
                }
                var macdData = convertStrAryToFloatAry(macdStrData);

                // console.log(adjSubsetOfDateKeys);
                // console.log(macdObj);
                
                Highcharts.chart('chart-container', {
                    chart: {
                        zoomType: 'xy',
                        height: (9 / 16 * 100) + '%' // 16:9 ratio
                    },
                    title: {
                        text: 'Moving Average Convergence/Divergence (MACD)'
                    },
                    subtitle: {
                        useHTML: true,
                        text: '<a href="https://www.alphavantage.co/" target="_blank">Source: Alpha Village</a>'
                    },
                    xAxis: {
                        categories: adjSubsetOfDateKeys,
                        tickInterval: 7
                    },
                    yAxis: {
                        title: {
                            text: 'MACD',
                        },
                        style: {
                            color: Highcharts.getOptions().colors[1]
                        }
                    },
                    legend: {
                        layout: 'vertical',
                        align: 'right',
                        verticalAlign: 'middle',
                        backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'
                    },
                    series: [{
                        name: symbolData,
                        data: macdData,
                        color: '#FA8072',
                        marker: {
                            enabled: true,
                            radius: 2.5,
                            lineWidth: 1,
                            fillColor:'red'
                        }
                    },
                    {
                        name: symbolData + '_Hist',
                        data: macdHistData,
                        color: '#ecba8c',
                        marker: {
                            enabled: true,
                            radius: 2.5,
                            lineWidth: 1,
                            fillColor:'#ecba8c'
                        }
                    },
                    {
                        name: symbolData + '_Signal',
                        data: macdSigData,
                        color: '#7dbbe7',
                        marker: {
                            enabled: true,
                            radius: 2.5,
                            lineWidth: 1,
                            fillColor:'blue'
                        }
                    }],
                    responsive: {
                        rules: [{
                            condition: {
                                maxWidth: 1000
                            },
                            chartOptions: {
                                legend: {
                                    align: 'center',
                                    verticalAlign: 'bottom',
                                    layout: 'horizontal'
                                }
                            }
                        }]
                    }
                });
            });
        }

        function buildArticleTable(json) {

            var len = json['0']['channel']['item'].length;
            var articleCount = 0;
            var index = 0;

            var links = [];
            var titles = [];
            var pubDates = [];
            while (index < len && articleCount < 5) {

                if("Article" == json['0']['channel']['item'][index]['guid'].substring(25, 32)) {
                    titles.push(json['0']['channel']['item'][index]['title']);
                    links.push(json['0']['channel']['item'][index]['link']);
                    pubDates.push(json['0']['channel']['item'][index]['pubDate'].substring(0, 25));
                    articleCount++;
                }
                index++;    
            }

            var articleDivContainer = document.createElement('div');
            articleDivContainer.setAttribute('class', 'container');
            document.body.appendChild(articleDivContainer);

            var articleDivRow = document.createElement('div');
            articleDivRow.setAttribute('class', 'row');
            articleDivContainer.appendChild(articleDivRow);

            var articleDivCol = document.createElement('div');
            articleDivCol.setAttribute('class', 'col p-0');
            articleDivRow.appendChild(articleDivCol);


            var articleTable = document.createElement("table");
            articleTable.setAttribute('class', 'table table-hover');
            articleTable.setAttribute('id', 'tbl-article');
            articleDivCol.appendChild(articleTable);
            
            var tableHead = document.createElement('thead');
            tableHead.setAttribute('class', 'thead-dark');
            articleTable.appendChild(tableHead);

            var tableHeadRow = document.createElement('tr');
            tableHead.appendChild(tableHeadRow);

            var articleHeader = document.createElement('th');
            articleHeader.setAttribute('scope', 'col');
            articleHeader.innerHTML = 'Article Name';
            
            var pubDateHeader = document.createElement('th');
            pubDateHeader.setAttribute('scope', 'col');
            pubDateHeader.innerHTML = 'Publication Date';

            tableHeadRow.appendChild(articleHeader);
            tableHeadRow.appendChild(pubDateHeader);

            var tableBody = document.createElement('tbody');
            articleTable.append(tableBody);

            for(var i = 0; i < 5; i++) {

                var row = document.createElement("tr");
                var articleCell = document.createElement("td");
                // cell.setAttribute('class', 'tblColArt');
                articleCell.innerHTML = "<a href=\"" + links[i] + "\ target=\"_blank\">" + titles[i] + "</a>";

                var pubDateCell = document.createElement("td");
                pubDateCell.innerHTML = pubDates[i];

                row.appendChild(articleCell);
                row.appendChild(pubDateCell);
                tableBody.appendChild(row);
            }
        }

        function toggleTbl() {

            var text = document.querySelector('#article-button-text');
            var arrow = document.querySelector('#arrow-img');
            if(text.innerHTML == "click to show stock news") {
                text.innerHTML = "click to hide stock news";
                arrow.outerHTML = "<img id=\"arrow-img\" src=\"./imgs/gray_arrow_up.png\" />";
                document.getElementById("tbl-article").style.display = "table";
            }
            else {
                text.innerHTML = "click to show stock news";
                arrow.outerHTML = "<img id=\"arrow-img\" src=\"./imgs/gray_arrow_down.png\" />";
                document.getElementById("tbl-article").style.display = "none";
            }
        }

    </script>
    </head>

    <!--  HTML-Body --> 
    <body>
        <div class="container">
            <div class="row">
                <div class="col p-0">
                    <section class="jumbotron mb-0 pb-3">
                        <h1 class="jumbotron-heading">Stock Search</h1>
                        <p class="lead text-muted">Enter a valid stock ticker symbol to utilize Alpha Vantage's realtime and historical data on stocks.
                        </p>
                        <hr>

                        <form action="index.php" method="POST" class="mt-4" id="search-form" accept-charset="utf-8" target="_self">
                            <div class="form-group">
                                <label for="symbol-field">Stock Ticker Symbol: </label>
                                <input id="symbol-field" class="form-control" type="text" name="symbol" autofocus required>
                            </div>
                            <div class="row">
                                <div class="col pr-0">
                                    <input class="btn btn-primary btn-block" id="search" type="submit" value="Search"/>
                                </div>
                                <div class="col pl-0">
                                    <input class="btn btn-secondary btn-block" id="clear" type="reset" value="Clear" onclick="return clearResults();"/>
                                </div>
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>

        <!-- PHP Logic -->
        <?php
            error_reporting(-1);
            $symbolFromForm = isset($_POST['symbol']) ? $_POST['symbol'] : "";
            echo "<script type=\"text/javascript\"> document.querySelector('#symbol-field').value = \"" . $symbolFromForm . "\"; </script>";

            function buildDataTable($jsonObj) {

                if(array_key_exists('Meta Data', $jsonObj)) {

                    $dateKeys = array_keys($jsonObj['Time Series (Daily)']);
                    $curDate  = $dateKeys[0];
                    $prevDate = $dateKeys[1];
                    $curClose = $jsonObj['Time Series (Daily)'][$curDate]['4. close'];
                    $prevClose = $jsonObj['Time Series (Daily)'][$prevDate]['4. close'];
                    $change = $curClose - $prevClose;
                    $changePercent = ($change / $prevClose) * 100;
                    $arrowSrc = "";

                    if($change > 0) {
                        $arrowSrc = "./imgs/green_arrow_up.png";
                    }
                    else {
                        $arrowSrc = "./imgs/red_arrow_down.png";
                    }

                    echo "<br>";
                    echo "<div class=\"container\">";
                    echo "<div class=\"row\">";
                    echo "<div class=\"col p-0\">";
                    echo "<table class=\"table table-striped\" id=\"tbl-stock-data\">";
                    echo "<thead scope=\"col\" class=\"thead-dark\"><tr class=\"text-center\"><th class=\"tblColRight\" colspan=\"2\">" . $jsonObj['Meta Data']['2. Symbol'] . " Overview</th></tr></thead>";
                    echo "<tbody>";
                    echo "<tr>  <td class=\"tblColLeft\">Stock Tracker Symbol</td>  <td class=\"tblColRight\">" . $jsonObj['Meta Data']['2. Symbol'] . "</td>  </tr>";
                    echo "<tr>  <td class=\"tblColLeft\">Close</td>                 <td class=\"tblColRight\">" . $curClose . "</td>  </tr>";
                    echo "<tr>  <td class=\"tblColLeft\">Open</td>                  <td class=\"tblColRight\">" . $jsonObj['Time Series (Daily)'][$curDate]['1. open']  
                                                                                                                . "</td>  </tr>";
                    echo "<tr>  <td class=\"tblColLeft\">Previous Close</td>        <td class=\"tblColRight\">" . $prevClose . "</td>  </tr>";
                    echo "<tr>  <td class=\"tblColLeft\">Change</td>                <td class=\"tblColRight\">" . number_format($change, 2, '.', ',') 
                                                                                                                . "<img class=\"arrow\" src=\"" . $arrowSrc . "\">" . "</td>  </tr>";
                    echo "<tr>  <td class=\"tblColLeft\">Change Percent</td>        <td class=\"tblColRight\">" . number_format($changePercent, 2, '.', ',') . "%" 
                                                                                                                . "<img class=\"arrow\" src=\"" . $arrowSrc . "\">" . "</td>  </tr>";
                    echo "<tr>  <td class=\"tblColLeft\">Day's Range</td>           <td class=\"tblColRight\">" . $jsonObj['Time Series (Daily)'][$curDate]['3. low']  
                                                                                                                . "-" 
                                                                                                                . $jsonObj['Time Series (Daily)'][$curDate]['2. high'] 
                                                                                                                . "</td>  </tr>";
                    echo "<tr>  <td class=\"tblColLeft\">Volume</td>                <td class=\"tblColRight\">" . 
                                                                                                    number_format($jsonObj['Time Series (Daily)'][$curDate]['5. volume'], 0, '.', ',')
                                                                                                                . "</td>  </tr>";
                    echo "<tr>  <td class=\"tblColLeft\">Timestamp</td>             <td class=\"tblColRight\">" . $curDate . "</td>  </tr>";
                    echo "<tr>  <td class=\"tblColLeft\">Indicators</td>            <td class=\"tblColRight\"> 
                    <a class=\"indicators\" id=\"price\" onclick=\"buildPriceChart(symbolData, dateData, priceData, volumeData);\">Price</a>
                    <a class=\"indicators\" id=\"sma\"   onclick=\"buildSMAChart(symbolData);\">SMA</a>
                    <a class=\"indicators\" id=\"ema\"   onclick=\"buildEMAChart(symbolData);\">EMA</a>
                    <a class=\"indicators\" id=\"stoch\" onclick=\"buildSTOCHChart(symbolData);\">STOCH</a>
                    <a class=\"indicators\" id=\"rsi\"   onclick=\"buildRSIChart(symbolData);\">RSI</a>
                    <a class=\"indicators\" id=\"adx\"   onclick=\"buildADXChart(symbolData);\">ADX</a>
                    <a class=\"indicators\" id=\"cci\".  onclick=\"buildCCIChart(symbolData);\">CCI</a>
                    <a class=\"indicators\" id=\"bbands\"onclick=\"buildBBANDSChart(symbolData);\">BBANDS</a>
                    <a class=\"indicators\" id=\"MACD\"  onclick=\"buildMACDChart(symbolData);\">MACD</a> 
                    </td>  </tr>";
                    echo "</tbody>";
                    echo "</table>";
                    echo "</div>";
                    echo "</div>";
                    echo "</div>";
                    return true;
                }
                else if(array_key_exists('Error Message', $jsonObj)) {
                    echo "<br>";
                    echo "<p class=\"text-center text-danger\" id=\"tbl-stock-data\">Error: No record has been found, please enter a valid symbol</p>";
                    return false;
                }
            } // end buildTable()

            function buildChart($jsonObj) {

                $origDateKeys = array_keys($jsonObj['Time Series (Daily)']);
                $len = 134;
                $dateKeys = array();
                for($i = 0; $i < $len; $i++) {
                    array_push($dateKeys, $origDateKeys[$i]);
                }
                echo "<br>";
                echo "<div class=\"container\">";
                echo "<div class=\"row\">";
                echo "<div class=\"col\" id=\"chart-container\"></div>";
                echo "</div>";
                echo "</div>";
                echo "<script type=\"text/javascript\"> var symbolData = \"" . $jsonObj['Meta Data']['2. Symbol'] . "\"; var dateData = []; var priceData = []; var volumeData = [];";

                foreach($dateKeys as &$date) {
                    echo "dateData.push(\"" . $date . "\");";
                }
                
                foreach($dateKeys as &$date) {
                    echo "priceData.push(\"" . $jsonObj['Time Series (Daily)'][$date]['4. close'] . "\");";
                }                

                foreach($dateKeys as &$date) {
                    echo "volumeData.push(\"" . $jsonObj['Time Series (Daily)'][$date]['5. volume'] . "\");";
                }

                echo "dateData.reverse();";
                echo "priceData.reverse();";
                echo "volumeData.reverse();";
                echo "buildPriceChart(symbolData, dateData, priceData, volumeData);";
                echo "</script>";
            } // end buildChart()

            function buildArtTable() {

                $articleURL = "https://seekingalpha.com/api/sa/combined/" . $_POST['symbol'] . ".xml";
                $xmlString = file_get_contents($articleURL);    // reads file into a string
                $xml = simplexml_load_string($xmlString); // interpret a string of xml into an object
                $json = json_encode($xml); // convert xml to json

                echo "<br>";
                echo "<div id=\"article-button-container\" onclick=\"toggleTbl();\"> <p id=\"article-button-text\">click to show stock news</p> <br> <img id=\"arrow-img\" src=\"./imgs/gray_arrow_down.png\"></div>";
                echo "<br>";
                echo "<script type=\"text/javascript\"> var jsonArticle = [" . $json . "]; buildArticleTable(jsonArticle); </script>";
            }

            if(isset($_POST['symbol']) && $_POST['symbol'] != "") {

                $stockURL = "https://www.alphavantage.co/query?";
                $stockFunction = "function=TIME_SERIES_DAILY";
                $stockSymbol = "&symbol=" . urlencode($_POST['symbol']);
                $stockOutputSize = "&outputsize=full";
                $stockAPIKey = "&apikey=" . urlencode("4W0M5ZYP8HVJJ1KD");

                $query = $stockURL . $stockFunction . $stockSymbol . $stockOutputSize . $stockAPIKey;                
                $jsonStr = file_get_contents($query);
                $jsonObj = json_decode($jsonStr, true);

                if(buildDataTable($jsonObj)) {
                    buildChart($jsonObj);
                    buildArtTable();    
                }
            }
            else if(isset($_POST['symbol']) && $_POST['symbol'] == "") {
                echo "<script type=\"text/javascript\"> alert(\"Please enter a symbol!\"); </script>";
            }
        ?>

        <!-- Bootstrap 4 scripts -->
        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    </body>
</html>
