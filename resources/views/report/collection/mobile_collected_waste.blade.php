@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col">
            <div class="card">
                <div class="card-header">Report - Mobile</div>
                <div class="card-body">
                    <div class="row hide-print">
                        <div class="col-lg-4 col-6">
                            <a href="{{route('report.collection.mobile')}}" class="small-box bg-info p-4">
                                <div class="inner">
                                    <h4>Collected Transaction</h4>

                                </div>
                            </a>
                        </div>
                        <div class="col-lg-4 col-6">
                            <a href="{{route('report.collection.mobile_collected_waste')}}" class="small-box bg-danger p-4">
                                <div class="inner">
                                    <h4>Collected Waste</h4>

                                </div>
                            </a>
                        </div>
                        <!-- ./col -->
                        <div class="col-lg-4 col-6">
                            <!-- small box -->
                            <a href="{{route('report.collection.mobile_waste_selling')}}" class="small-box bg-success p-4">
                                <div class="inner">
                                    <h4>Waste Selling</h4>
                                </div>
                            </a>
                        </div>
                    </div>

                    <div class="row most-left hide-print">
                        <label class="mr-2">Date</label>
                        <input type="date" id="date_from" name="date_from">
                        <label class="ml-2 mr-2">to</label>
                        <input type="date" id="date_to" name="date_to">

                        <label class="ml-4" for="format">Format:</label>

                        <select class="ml-2" name="format" id="format">
                            <option value="Month">Month</option>
                            <option value="Week">Week</option>
                        </select>

                        <a href="#" onclick="showData()" id="btn_show" class="btn btn-xs btn-info ml-2">View</a>

                        <a href="#" onclick="printReport()" id="btn_export" class="btn btn-xs btn-success ml-2">PDF</a>
                        <a href="#" onclick="exportCsv()" id="btn_csv" class="btn btn-xs btn-success ml-2">CSV</a>
                    </div>

                    <h5 class="text-center mt-3">Total Weight Collected By Recycling Type</h5>
                    <canvas id="chartTotalCollectedTransaction" width="300"></canvas>

                    <br>
                    <br>

                    <h5 id="chart3" class="text-center mt-3">Total Weight of Different Recycling Category Collected Across Collection Hub Across District</h5>
                    <br>
                    <div>
                        <label for="state">State:</label>
                        <select class="ml-2" name="state" id="state">
                            <option value="">Select a state</option>
                            @foreach ($allStates as $value)
                            <option value="{{$value['id']}}">{{$value['name']}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div id="chartDiv">
                    </div>
                    <!-- <canvas id="chartTotalCollectedTransactionDistrict" width="300"></canvas> -->


                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
    var myChart2 = '';
    var myChart = '';
    var myChart1 = '';
    var type = 'User';
    var format = 'Month';
    var label = [];
    var dataset = [];
    var dataset_total = [];
    var state = '';
    var category = [];
    var date_from = '';
    var date_to = '';

    function exportCsv() {
        state = $('#state').find('option:selected').text();
        labelsArr = [];
        labelsArr.push(label);

        let csvContent = "data:text/csv;charset=utf-8,";
        csvContent += "Mobile Collected Waste- " + type + " - " + format + "\r\n";
        if (date_from != '' && date_to != '') {
            csvContent += date_from + ' to ' + date_to + "\r\n\r\n";
        }

        labelsArr.forEach(function(rowArray) {
            let row = rowArray.join(",");
            csvContent += ' ,' + row + "\r\n";
        });
        dataset.forEach(function(rowArray) {
            let row = rowArray.label + ',' + rowArray.data.join(",");
            csvContent += row + "\r\n";
        });
        if (state != 'Select a state') {
            csvContent += "\r\n" + state + "\r\n";
            dataset_total.forEach(function(rowArray, index) {
                csvContent += "\r\n" + category[index].name + "\r\n";
                rowArray.forEach(function(rowArray1) {
                    let row = rowArray1.label + ',' + rowArray1.data.join(",");
                    csvContent += row + "\r\n";
                });
            });
        }
        var encodedUri = encodeURI(csvContent);
        var link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "mobile_collected_waste.csv");
        document.body.appendChild(link);
        link.click()
    }

    function showData() {
        var chartType = '';
        var date_from = $('#date_from').val();
        var date_to = $('#date_to').val();
        jQuery.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        jQuery.ajax({
            url: '{{ route("report.get_collection_collected_waste_data") }}',
            method: 'post',
            data: {
                "_token": "{{ csrf_token() }}",
                format: format,
                hub_type: 1,
                date_from: date_from,
                date_to: date_to,
            },
            success: function(data) {
                var state_id = $('#state').find(":selected").val();
                if (state_id != '')
                    showStateData();
                label = data[0];
                var dataChart = data[1];
                var categoryLabel = data[2];
                var categoryColor = data[3];
                var dataChartTotal = data[4];
                var totalColor = data[5];
                dataset = [];
                var newArray = [];
                for (let x = 0; x < categoryLabel.length; x++) {
                    var newArray1 = [];
                    for (let y = 0; y < dataChart.length; y++) {
                        newArray1.push(dataChart[y][x]);
                    }
                    newArray.push(newArray1);
                }

                for (let i = 0; i < newArray.length; i++) {
                    dataset.push({
                        label: categoryLabel[i].name,
                        borderColor: categoryColor[i],
                        pointBackgroundColor: categoryColor[i],
                        backgroundColor: categoryColor[i],
                        data: Object.values(newArray[i]),
                        fill: 'transparent',
                        minBarLength: 7,
                    })
                }

                dataset.push({
                    label: 'Total',
                    borderColor: totalColor[0],
                    pointBackgroundColor: totalColor[0],
                    backgroundColor: totalColor[0],
                    data: Object.values(dataChartTotal),
                    fill: 'transparent',
                    minBarLength: 7,
                })

                var ctx = document.getElementById('chartTotalCollectedTransaction');
                if (myChart) myChart.destroy();
                myChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: label,
                        datasets: dataset
                    },
                    options: {
                        plugins: {
                            labels: false
                        },
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true
                                },
                            }],

                        },

                    }
                });
            },
            error: function(data) {
                console.log(data.responseJSON.message);
            }
        });
    }

    $(document).ready(function() {
        showData();
    })


    jQuery('#format').change(function() {
        format = $('#format').find(":selected").val();
    });

    jQuery('#state').change(function() {
        showStateData();
    });

    function showStateData() {
        var date_from = $('#date_from').val();
        var date_to = $('#date_to').val();
        $('#chartDiv').empty();
        var state_id = $('#state').find(":selected").val();
        if (state_id == '') {
            myChart2.destroy();
            return;
        }
        jQuery.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        jQuery.ajax({
            url: '{{ route("report.get_collection_collected_waste_district") }}',
            method: 'post',
            data: {
                "_token": "{{ csrf_token() }}",
                state_id: state_id,
                format: format,
                hub_type: 1,
                date_from: date_from,
                date_to: date_to
            },
            success: function(data) {
                var color = data[0];
                category = data[1];
                var chartData = data[2];
                var hubs = data[3];
                var months = data[4];
                var dataTotal = data[5];
                var colorTotal = data[6];
                var newArray = [];
                var dataset_category = [];
                var newArray2 = [];
                dataset_total = [];
                chartData.forEach((element, index) => {
                    newArray = [];
                    for (let x = 0; x < hubs.length; x++) {
                        var newArray1 = [];
                        for (let y = 0; y < element.length; y++) {
                            newArray1.push(element[y][x]);
                        }
                        newArray.push(newArray1);
                    }
                    newArray2.push(newArray);
                });

                newArray2.forEach((element, index) => {
                    dataset_category = [];
                    for (let i = 0; i < element.length; i++) {
                        dataset_category.push({
                            label: hubs[i].hub_name,
                            borderColor: color[i],
                            pointBackgroundColor: color[i],
                            backgroundColor: color[i],
                            data: Object.values(element[i]),
                            fill: 'transparent',
                            minBarLength: 7,
                        })

                    }
                    dataset_category.push({
                        label: 'Total',
                        borderColor: colorTotal[0],
                        pointBackgroundColor: colorTotal[0],
                        backgroundColor: colorTotal[0],
                        data: Object.values(dataTotal[index]),
                        fill: 'transparent',
                        minBarLength: 7,
                    })
                    dataset_total.push(dataset_category);
                    $('#chartDiv').append('<h6 class="text-center mt-3">' + category[index].name + '</h6>');
                    $('#chartDiv').append(' <canvas id="myChart' + index + '" width="300" height="100"></canvas>');
                    $('#chartDiv').append('<br><br><br>');
                    var ctx = document.getElementById('myChart' + index);
                    var myChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: months,
                            datasets: dataset_category
                        },
                        options: {
                            plugins: {
                                labels: false
                            },
                            scales: {
                                yAxes: [{
                                    ticks: {
                                        beginAtZero: true
                                    },
                                }],

                            },

                        }
                    });
                });


            },
            error: function(data) {
                console.log(data.responseJSON.message);
            }
        });
    };



    function beforePrint() {
        for (const id in Chart.instances) {
            Chart.instances[id].resize()
        }
    }

    if (window.matchMedia) {
        let mediaQueryList = window.matchMedia('print')
        mediaQueryList.addListener((mql) => {
            if (mql.matches) {
                beforePrint()
            }
        })
    }

    window.onbeforeprint = beforePrint;

    function printReport() {
        window.print();
    }
</script>
@endsection