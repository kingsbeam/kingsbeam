        var lineChartData = {
            labels : [],
            datasets : [
                {
                    axis : 1,
                    label: "Temperature",
                    fillColor : "rgba(176,87,187,0.3)",
                    strokeColor : "rgba(176,87,187,1)",
                    pointColor : "rgba(176,87,187,1)",
                    pointStrokeColor : "#fff",
                    pointHighlightFill : "#fff",
                    pointHighlightStroke : "rgba(176,87,187,1)",
                    scaleShowLabels : true,
                    scaleStartValue: 0,
                    data : []
                },
                {
                    axis : 0,
                    label: "Light",
                    fillColor : "rgba(219,186,52,0.3)",
                    strokeColor : "rgba(219,186,52,1)",
                    pointColor : "rgba(219,186,52,1)",
                    pointStrokeColor : "#fff",
                    pointHighlightFill : "#fff",
                    pointHighlightStroke : "rgba(219,186,52,1)",
                    scaleShowLabels : true,
                    scaleStartValue: 0,
                    data : []
                },
            ]

        }
        function load_ddata(){
            $.ajax({
                url:'_includes/data.php',
                type:'post',
                dataType :'text',
                data:{},
                success:function(ret){
                    ret = ret.trim();
                    if(ret.substr(0,1) == '{'){
                        var res = $.parseJSON(ret);
                        draw_chart(res);
                    }else{
                        alert("Can't load Data correctly");
                    }
                },
                error:function(hdl, sta,er){
                    console.log(sta);
                    console.log(er);
                    alert('errr');
                }
            });
        }
            
        function draw_chart(ret){
            lineChartData.labels = ret.labels;
            lineChartData.datasets[0].data = ret.temper;
            lineChartData.datasets[1].data = ret.light;
            $('#canvas').replaceWith('<canvas id="canvas" width="500" height="300"></canvas>');
            var ctx = document.getElementById("canvas").getContext("2d");
            var Charts = new Chart(ctx);
            window.myLine = Charts.MultiAxisLine(lineChartData, {
                scaleShowGridLines : true,
                scaleGridLineColor : "rgba(255,255,255,.05)",
                scaleGridLineWidth : 1,
                scaleStartValue: 0,
                scaleFontColor : "#fff",
                responsive: true,
                drawScale: [0,1]
            });
            $("#legendDiv").html( window.myLine.generateLegend());
        }
        $(function(){
            load_ddata();
        });