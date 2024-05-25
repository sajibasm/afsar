/**
 * Created by sajib on 6/20/2015.
 */


$(function () {


    function salesPie() {
        //Store Wise Sales
        am4core.useTheme(am4themes_dataviz);
        am4core.useTheme(am4themes_animated);
        var chart = am4core.create("chartdiv5", am4charts.PieChart3D);
        chart.hiddenState.properties.opacity = 0;
        chart.legend = new am4charts.Legend();
        var series = chart.series.push(new am4charts.PieSeries3D());
        series.dataFields.value = "value";
        series.dataFields.category = "property";
        chart.dataSource.reloadFrequency = 30000;
        chart.dataSource.url = dashboardUrl + 'sales-pie?outlet='+defaultOutlet;
    }

    function expensePie() {
        //Store Wise Sales
        am4core.useTheme(am4themes_dataviz);
        am4core.useTheme(am4themes_animated);
        var chart = am4core.create("chartdiv6", am4charts.PieChart3D);
        chart.hiddenState.properties.opacity = 0;
        chart.legend = new am4charts.Legend();
        var series = chart.series.push(new am4charts.PieSeries3D());
        series.dataFields.value = "value";
        series.dataFields.category = "property";
        chart.dataSource.url = dashboardUrl + 'expense-pie?outlet='+defaultOutlet;
        chart.dataSource.reloadFrequency = 30000;
    }

    function analyticsStore() {
        //Store Wise Sales
        am4core.useTheme(am4themes_dataviz);
        am4core.useTheme(am4themes_animated);
        var chart = am4core.create("chartdiv4", am4charts.PieChart3D);
        chart.hiddenState.properties.opacity = 0;
        chart.legend = new am4charts.Legend();
        var series = chart.series.push(new am4charts.PieSeries3D());
        series.dataFields.value = "value";
        series.dataFields.category = "property";
        chart.dataSource.url = dashboardUrl + 'store';
        chart.dataSource.reloadFrequency = 30000;
    }

    function analyticsCash() {
        //SaleGrowth
        am4core.useTheme(am4themes_material);
        am4core.useTheme(am4themes_animated);
        var chart = am4core.create("chartdiv2", am4charts.XYChart3D);

        let categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
        categoryAxis.dataFields.category = "property";
        categoryAxis.renderer.labels.template.rotation = 270;
        categoryAxis.renderer.labels.template.hideOversized = false;
        categoryAxis.renderer.minGridDistance = 20;
        categoryAxis.renderer.labels.template.horizontalCenter = "right";
        categoryAxis.renderer.labels.template.verticalCenter = "middle";
        categoryAxis.tooltip.label.rotation = 270;
        categoryAxis.tooltip.label.horizontalCenter = "right";
        categoryAxis.tooltip.label.verticalCenter = "middle";

        let valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
        valueAxis.title.text = "Analytics";
        valueAxis.title.fontWeight = "bold";

        // Create series
        var series = chart.series.push(new am4charts.ColumnSeries3D());
        series.dataFields.valueY = "numbers";
        series.dataFields.categoryX = "property";
        series.name = "numbers";
        series.tooltipText = "{categoryX}: [bold]{valueY}[/]";
        series.columns.template.fillOpacity = .6;

        var columnTemplate = series.columns.template;
        columnTemplate.strokeWidth = 2;
        columnTemplate.strokeOpacity = 1;
        columnTemplate.stroke = am4core.color("#FFFFFF");

        columnTemplate.adapter.add("fill", function(fill, target) {
            return chart.colors.getIndex(target.dataItem.index);
        })

        columnTemplate.adapter.add("stroke", function(stroke, target) {
            return chart.colors.getIndex(target.dataItem.index);
        })

        chart.cursor = new am4charts.XYCursor();
        chart.cursor.lineX.strokeOpacity = 0;
        chart.cursor.lineY.strokeOpacity = 0;
        chart.dataSource.url = dashboardUrl + 'analytics?outlet=' + defaultOutlet+"&type=cash";
        chart.dataSource.reloadFrequency = 30000;
    }

    function analyticsBank() {
        //SaleGrowth
        am4core.useTheme(am4themes_material);
        am4core.useTheme(am4themes_animated);
        var chart = am4core.create("chartdiv3", am4charts.XYChart3D);

        let categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
        categoryAxis.dataFields.category = "property";
        categoryAxis.renderer.labels.template.rotation = 270;
        categoryAxis.renderer.labels.template.hideOversized = false;
        categoryAxis.renderer.minGridDistance = 20;
        categoryAxis.renderer.labels.template.horizontalCenter = "right";
        categoryAxis.renderer.labels.template.verticalCenter = "middle";
        categoryAxis.tooltip.label.rotation = 270;
        categoryAxis.tooltip.label.horizontalCenter = "right";
        categoryAxis.tooltip.label.verticalCenter = "middle";

        let valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
        valueAxis.title.text = "Analytics";
        valueAxis.title.fontWeight = "bold";

        // Create series
        var series = chart.series.push(new am4charts.ColumnSeries3D());
        series.dataFields.valueY = "numbers";
        series.dataFields.categoryX = "property";
        series.name = "numbers";
        series.tooltipText = "{categoryX}: [bold]{valueY}[/]";
        series.columns.template.fillOpacity = .6;

        var columnTemplate = series.columns.template;
        columnTemplate.strokeWidth = 2;
        columnTemplate.strokeOpacity = 1;
        columnTemplate.stroke = am4core.color("#FFFFFF");

        columnTemplate.adapter.add("fill", function(fill, target) {
            return chart.colors.getIndex(target.dataItem.index);
        })

        columnTemplate.adapter.add("stroke", function(stroke, target) {
            return chart.colors.getIndex(target.dataItem.index);
        })

        chart.cursor = new am4charts.XYCursor();
        chart.cursor.lineX.strokeOpacity = 0;
        chart.cursor.lineY.strokeOpacity = 0;
        chart.dataSource.url = dashboardUrl + 'analytics?outlet=' + defaultOutlet+"&type=bank";
        chart.dataSource.reloadFrequency = 30000;
    }

    function salesGrowth() {
        //SaleGrowth
        am4core.useTheme(am4themes_material);
        //am4core.useTheme(am4themes_dataviz);
        am4core.useTheme(am4themes_animated);
        var chart = am4core.create("chartdiv", am4charts.XYChart);
        chart.colors.step = 4;
        //chart.data = generateChartData();
        var dateAxis = chart.xAxes.push(new am4charts.DateAxis());
        dateAxis.renderer.minGridDistance = 40;
        function createAxisAndSeries(field, name, opposite, bullet) {
            var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
            if (chart.yAxes.indexOf(valueAxis) != 0) {
                valueAxis.syncWithAxis = chart.yAxes.getIndex(0);
            }

            var series = chart.series.push(new am4charts.LineSeries());
            series.dataFields.valueY = field;
            series.dataFields.dateX = "date";
            series.strokeWidth = 2;
            series.yAxis = valueAxis;
            series.name = name;
            series.tooltipText = "{name}: [bold]{valueY}[/]";
            series.tensionX = 0.8;
            series.showOnInit = true;

            var interfaceColors = new am4core.InterfaceColorSet();

            switch (bullet) {
                case "triangle":
                    var bullet = series.bullets.push(new am4charts.Bullet());
                    bullet.width = 12;
                    bullet.height = 12;
                    bullet.horizontalCenter = "middle";
                    bullet.verticalCenter = "middle";

                    var triangle = bullet.createChild(am4core.Triangle);
                    triangle.stroke = interfaceColors.getFor("background");
                    triangle.strokeWidth = 2;
                    triangle.direction = "top";
                    triangle.width = 12;
                    triangle.height = 12;
                    break;
                case "rectangle":
                    var bullet = series.bullets.push(new am4charts.Bullet());
                    bullet.width = 10;
                    bullet.height = 10;
                    bullet.horizontalCenter = "middle";
                    bullet.verticalCenter = "middle";

                    var rectangle = bullet.createChild(am4core.Rectangle);
                    rectangle.stroke = interfaceColors.getFor("background");
                    rectangle.strokeWidth = 2;
                    rectangle.width = 10;
                    rectangle.height = 10;
                    break;
                default:
                    var bullet = series.bullets.push(new am4charts.CircleBullet());
                    bullet.circle.stroke = interfaceColors.getFor("background");
                    bullet.circle.strokeWidth = 2;
                    break;
            }

            valueAxis.renderer.line.strokeOpacity = 1;
            valueAxis.renderer.line.strokeWidth = 2;
            valueAxis.renderer.line.stroke = series.stroke;
            valueAxis.renderer.labels.template.fill = series.stroke;
            valueAxis.renderer.opposite = opposite;
        }
        createAxisAndSeries("sales", "Sales", false, "circle");
        createAxisAndSeries("paid", "Paid", true, "triangle");
        createAxisAndSeries("due", "Due", true, "rectangle");
        chart.legend = new am4charts.Legend();
        chart.cursor = new am4charts.XYCursor();
        chart.dataSource.url = dashboardUrl + 'sales-growth?outlet=' + defaultOutlet;
        chart.dataSource.reloadFrequency = 30000;
    }

    function dailySalesUpdate() {

        var request = $.ajax({
            url: dailySummeryUrl + '?id=' + defaultOutlet,
            type: "GET"
        });

        // Callback handler that will be called on success
        request.done(function (response, textStatus, jqXHR) {
            // Log a message to the console
            // Add data

            $('#dailySales').empty();
            $('#dailySales').append(response.sales);

            $('#dailyDues').empty();
            $('#dailyDues').append(response.salesDue);

            $('#dailySalesCash').empty();
            $('#dailySalesCash').append(response.salesPaid);

            $('#dailySalesReturn').empty();
            $('#dailySalesReturn').append(response.salesReturn);

            $('#dailyCashHand').empty();
            $('#dailyCashHand').append(response.cashHand);

            $('#dailyDueCollection').empty();
            $('#dailyDueCollection').append(response.dueReceived);

            $('#dailyWithdraw').empty();
            $('#dailyWithdraw').append(response.withdraw);

            $('#dailyExpense').empty();
            $('#dailyExpense').append(response.expense);

            $('#dailyCashIn').empty();
            $('#dailyCashIn').append(response.cashIn);

            $('#dailyCashOut').empty();
            $('#dailyCashOut').append(response.cashOut);

            $('#dailyBankIn').empty();
            $('#dailyBankIn').append(response.bankIn);

            $('#dailyBankOut').empty();
            $('#dailyBankOut').append(response.bankOut);

        });

        // Callback handler that will be called on failure
        request.fail(function (jqXHR, textStatus, errorThrown) {
            // Log the error to the console
            console.error("The following error occurred: " + textStatus, errorThrown);
        });
    }

    salesPie();
    expensePie();
    dailySalesUpdate();
    salesGrowth();
    analyticsCash();
    analyticsBank();
    analyticsStore();

    setInterval(function () {
        dailySalesUpdate();
    }, 30000);


    $(document).on('click', '.outlet_menu', function () {
        defaultOutlet = $(this).data("id");
        salesPie();
        expensePie();
        dailySalesUpdate();
        salesGrowth();
        analyticsCash();
        analyticsBank();
        analyticsStore();
        $("#dashboardUrlList li").removeClass('active');
        $('#' + $(this).attr('id')).addClass('active');
    });
});