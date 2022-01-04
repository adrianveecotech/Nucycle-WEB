@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col">
            <div class="card">
                <div class="card-header">App Performance - Demography</div>
                <div class="card-body">
                    <div class="row hide-print">
                        <div class="col-lg-6 col-4">
                            <a href="{{route('report.app_performance.demography')}}" class="small-box bg-info p-4">
                                <div class="inner">
                                    <h4>Demography</h4>

                                </div>
                            </a>
                        </div>
                        <div class="col-lg-6 col-4">
                            <a href="{{route('report.app_performance.growth_and_population')}}" class="small-box bg-danger p-4">
                                <div class="inner">
                                    <h4>Growth and Population</h4>

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

                    <h5 class="text-center mt-3">Total Registered Users</h5>
                    <canvas id="canvas1" width="300"></canvas>

                    <br>
                    <br>
                    <h5 id="chart2" class="text-center mt-3">Total Registered Users Across States</h5>
                    <canvas id="canvas2" width="300"></canvas>

                    <br>
                    <br>
                    <label for="state">State:</label>

                    <select class="ml-2" name="state" id="state">
                        <option value="">Select a state</option>
                        @foreach ($allStates as $value)
                        <option value="{{$value['id']}}">{{$value['name']}}</option>
                        @endforeach
                    </select>
                    <h5 id="chart3" class="text-center mt-3">Total Registered Users Across District</h5>
                    <canvas id="canvas3" width="300"></canvas>


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
    var format = 'Month';
    var labels = [];
    var datasets = [];
    var dataset = [];
    var state = '';
    var dataset2 = [];
    var date_from = '';
    var date_to = '';

    function exportCsv() {
        state = $('#state').find('option:selected').text();
        labelsArr = [];
        labelsArr.push(labels);

        let csvContent = "data:text/csv;charset=utf-8,";
        csvContent += "App Performance Demography - " + format + "\r\n";

        if (date_from != '' && date_to != '') {
            csvContent += date_from + ' to ' + date_to + "\r\n";
        }

        labelsArr.forEach(function(rowArray) {
            let row = rowArray.join(",");
            csvContent += ' ,' + row + "\r\n";
        });

        datasets.forEach(function(rowArray) {
            let row = rowArray.label + ',' + rowArray.data.join(",");
            csvContent += row + "\r\n";
        });

        csvContent += "\r\n";
        dataset.forEach(function(rowArray) {
            let row = rowArray.label + ',' + rowArray.data.join(",");
            csvContent += row + "\r\n";
        });

        if (state != 'Select a state') {
            csvContent += "\r\n";
            csvContent += state + "\r\n";
            dataset2.forEach(function(rowArray) {
                let row = rowArray.label + ',' + rowArray.data.join(",");
                csvContent += row + "\r\n";
            });

        }
        var encodedUri = encodeURI(csvContent);
        var link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "app_performance_demography.csv");
        document.body.appendChild(link);
        link.click()
    }

    function showData() {
        var chartType = '';
        date_from = $('#date_from').val();
        date_to = $('#date_to').val();
        jQuery.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        jQuery.ajax({
            url: '{{ route("report.app_performance.get_registered_user") }}',
            method: 'post',
            data: {
                "_token": "{{ csrf_token() }}",
                format: format,
                date_from: date_from,
                date_to: date_to
            },
            success: function(data) {
                var state_id = $('#state').find(":selected").val();
                if (state_id != '')
                    showStateData();
                var ctx = document.getElementById('canvas1');
                datasets = [];

                label = data[0];
                var individual = data[1];
                var company = data[2];
                var total = data[3];
                var dataAllStates = data[4];
                var allStatesName = data[5];
                var colors = data[6];

                chartType = 'line';
                datasets = [{
                        data: company,
                        label: 'B2B',
                        borderColor: '#ff7605',
                        fill: 'transparent',
                        pointBackgroundColor: '#ff7605',
                        backgroundColor: '#ff7605',
                        hoverOffset: 10,
                    },
                    {
                        data: individual,
                        label: 'B2C',
                        fill: 'transparent',
                        pointBackgroundColor: '#035AA6',
                        backgroundColor: '#035AA6',
                        borderColor: '#035AA6',
                        hoverOffset: 10,
                    },
                    {
                        data: total,
                        label: 'Total',
                        fill: 'transparent',
                        pointBackgroundColor: '#bdbdbd',
                        backgroundColor: '#bdbdbd',
                        borderColor: '#bdbdbd',
                        hoverOffset: 10,
                    },
                ];

                dataset = [];
                for (var i = 0; i < dataAllStates.length; i++) {
                    dataset.push({
                        label: allStatesName[i],
                        borderColor: colors[i],
                        pointBackgroundColor: colors[i],
                        backgroundColor: colors[i],
                        data: Object.values(dataAllStates[i]),
                        fill: 'transparent',


                    })
                }
                dataset.push({
                    label: 'Total',
                    borderColor: '#bdbdbd',
                    pointBackgroundColor: '#bdbdbd',
                    backgroundColor: '#bdbdbd',
                    data: total,
                    fill: 'transparent',


                })


                if (myChart) myChart.destroy();
                myChart = new Chart(ctx, {
                    type: chartType,
                    data: {
                        labels: label,
                        datasets: datasets
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

                var ctx1 = document.getElementById('canvas2');
                if (myChart1) myChart1.destroy();
                myChart1 = new Chart(ctx1, {
                    type: chartType,
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
        changeType();
    })


    jQuery('#format').change(function() {
        format = $('#format').find(":selected").val();
    });

    jQuery('#state').change(function() {
        showStateData();
    });

    function showStateData() {
        date_from = $('#date_from').val();
        date_to = $('#date_to').val();
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
            url: '{{ route("report.app_performance.get_registered_user_district") }}',
            method: 'post',
            data: {
                "_token": "{{ csrf_token() }}",
                state_id: state_id,
                format: format,
                date_from: date_from,
                date_to: date_to
            },
            success: function(data) {
                var city = data[0];
                var colors = data[1];
                var cityData = data[2];
                var labels = data[3];
                var allData = data[4];
                dataset2 = [];
                for (var i = 0; i < cityData.length; i++) {
                    dataset2.push({
                        label: city[i],
                        borderColor: colors[i],
                        pointBackgroundColor: colors[i],
                        backgroundColor: colors[i],
                        data: Object.values(cityData[i]),
                        fill: 'transparent',
                        minBarLength: 7,
                    })
                }
                dataset2.push({
                    label: 'Total',
                    borderColor: '#bdbdbd',
                    pointBackgroundColor: '#bdbdbd',
                    backgroundColor: '#bdbdbd',
                    data: allData,
                    fill: 'transparent',
                    minBarLength: 7,
                })
                var ctx1 = document.getElementById('canvas3');
                if (myChart2) myChart2.destroy();

                myChart2 = new Chart(ctx1, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: dataset2
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
    };

    function changeType() {
        showData();
        $("#state").val('');
        if (myChart2) myChart2.destroy();
    }

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