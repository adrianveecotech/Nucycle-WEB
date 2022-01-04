@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col">
            <div class="card">
                <div class="card-header">Report - On Site</div>
                <div class="card-body">
                    <div class="row hide-print">
                        <div class="col-lg-4 col-6">
                            <a href="{{route('report.collection.on_site')}}" class="small-box bg-info p-4">
                                <div class="inner">
                                    <h4>Collected Transaction</h4>

                                </div>
                            </a>
                        </div>
                        <div class="col-lg-4 col-6">
                            <a href="{{route('report.collection.on_site_collected_waste')}}" class="small-box bg-danger p-4">
                                <div class="inner">
                                    <h4>Collected Waste</h4>

                                </div>
                            </a>
                        </div>
                        <!-- ./col -->
                        <div class="col-lg-4 col-6">
                            <!-- small box -->
                            <a href="{{route('report.collection.on_site_waste_selling')}}" class="small-box bg-success p-4">
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

                    <div class="w3-bar hide-print">
                        <button class="w3-bar-item w3-button" id="btnTabUser" onclick="changeType('User')">User</button>
                        <button class="w3-bar-item w3-button" id="btnTabHub" onclick="changeType('Hub')">Hub</button>
                    </div>

                    <h5 class="text-center mt-3">Total Collected Transaction</h5>
                    <canvas id="chartTotalCollectedTransaction" width="300"></canvas>

                    <br>
                    <br>
                    <h5 id="chart2" class="text-center mt-3">Total Collected Transaction Across States</h5>
                    <canvas id="chartTotalCollectedTransactionState" width="300"></canvas>

                    <br>
                    <br>
                    <label for="state">State:</label>

                    <select class="ml-2" name="state" id="state">
                        <option value="">Select a state</option>
                        @foreach ($allStates as $value)
                        <option value="{{$value['id']}}">{{$value['name']}}</option>
                        @endforeach
                    </select>
                    <h5 id="chart3" class="text-center mt-3">Total Collected Transaction Across District</h5>
                    <canvas id="chartTotalCollectedTransactionDistrict" width="300"></canvas>


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
    var labels = [];
    var datasets = [];
    var dataset = [];
    var state = '';
    var dataset2 = [];
    var date_from = '';
    var date_to = '';
    var tableData = [];

    function exportCsv() {
        state = $('#state').find('option:selected').text();
        labelsArr = [];
        labelsArr.push(labels);

        let csvContent = "data:text/csv;charset=utf-8,";
        csvContent += "On Site Collected Transaction - " + type + " - " + format + "\r\n";

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
        csvContent += "\r\nUser with number of collected transaction\r\nUser,Type, Number of transaction\r\n";
        tableData.forEach(function(rowArray) {
            let row = rowArray.email + ',' + rowArray.type + ',' + rowArray.total;
            csvContent += row + "\r\n";
        });

        var encodedUri = encodeURI(csvContent);
        var link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "on_site_collected_transaction.csv");
        document.body.appendChild(link);
        link.click()
    }

    function showData() {
        format = $('#format').find(":selected").val();
        var chartType = '';
        date_from = $('#date_from').val();
        date_to = $('#date_to').val();
        jQuery.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        jQuery.ajax({
            url: '{{ route("report.get_collection_collected_transaction_data") }}',
            method: 'post',
            data: {
                "_token": "{{ csrf_token() }}",
                type: type,
                format: format,
                hub_type: 0,
                date_from: date_from,
                date_to: date_to
            },
            success: function(data) {
                var state_id = $('#state').find(":selected").val();
                if (state_id != '')
                    showStateData();
                var ctx = document.getElementById('chartTotalCollectedTransaction');
                if (type == 'Hub') {
                    var companyMonth = data[0];
                    var individualMonth = data[1];
                    labels = data[2];
                    var dataAllStatesCompany = data[3];
                    var allStatesName = data[4];
                    var colors = data[5];
                    var dataAllStatesIndividual = data[6];
                    tableData = data[7];
                    datasets = [];
                    dataset = [];
                    chartType = 'bar';
                    datasets = [{
                            data: companyMonth,
                            label: 'B2B',
                            borderColor: '#ff7605',
                            fill: 'transparent',
                            pointBackgroundColor: '#ff7605',
                            backgroundColor: '#ff7605',
                            hoverOffset: 10,
                            minBarLength: 7,
                        },
                        {
                            data: individualMonth,
                            label: 'B2C',
                            fill: 'transparent',
                            pointBackgroundColor: '#035AA6',
                            backgroundColor: '#035AA6',
                            borderColor: '#035AA6',
                            hoverOffset: 10,
                            minBarLength: 7,
                        },
                    ];

                    for (var i = 0; i < dataAllStatesCompany.length; i++) {
                        dataset.push({
                            label: allStatesName[i] + ' - B2B',
                            borderColor: colors[i],
                            pointBackgroundColor: colors[i],
                            backgroundColor: colors[i],
                            data: Object.values(dataAllStatesCompany[i]),
                            fill: 'transparent',
                            minBarLength: 7,


                        })
                    }

                    for (var i = 0; i < dataAllStatesIndividual.length; i++) {
                        dataset.push({
                            label: allStatesName[i] + ' - B2C',
                            borderColor: colors[i + allStatesName.length],
                            pointBackgroundColor: colors[i],
                            backgroundColor: colors[i],
                            data: Object.values(dataAllStatesIndividual[i]),
                            fill: 'transparent',
                            minBarLength: 7,
                        })
                    }
                } else if (type == 'User') {
                    var companyMonth = data[0];
                    var individualMonth = data[1];
                    var userMonth = data[2];
                    labels = data[3];
                    var dataAllStates = data[4];
                    var allStatesName = data[5];
                    var colors = data[6];
                    tableData = data[7];
                    datasets = [];
                    dataset = [];
                    chartType = 'line'
                    datasets = [{
                            data: companyMonth,
                            label: 'B2B',
                            borderColor: '#ff7605',
                            fill: 'transparent',
                            pointBackgroundColor: '#ff7605',
                            backgroundColor: '#ff7605',
                            hoverOffset: 10,


                        },
                        {
                            data: individualMonth,
                            label: 'B2C',
                            fill: 'transparent',
                            pointBackgroundColor: '#035AA6',
                            backgroundColor: '#035AA6',
                            borderColor: '#035AA6',
                            hoverOffset: 10,


                        },
                        {
                            data: userMonth,
                            label: 'Total',
                            fill: 'transparent',
                            pointBackgroundColor: '#BDBDBD',
                            backgroundColor: '#BDBDBD',
                            borderColor: '#BDBDBD',
                            hoverOffset: 10,


                        }
                    ]

                    for (var i = 0; i < dataAllStates.length; i++) {
                        dataset.push({
                            label: allStatesName[i],
                            borderColor: colors[i],
                            pointBackgroundColor: colors[i],
                            backgroundColor: colors[i],
                            data: Object.values(dataAllStates[i]),
                            fill: 'transparent',
                            minBarLength: 7,


                        })
                    }
                    dataset.push({
                        label: 'Total',
                        borderColor: '#bdbdbd',
                        pointBackgroundColor: '#bdbdbd',
                        backgroundColor: '#bdbdbd',
                        data: userMonth,
                        fill: 'transparent',
                        minBarLength: 7,


                    })
                }

                if (myChart) myChart.destroy();
                myChart = new Chart(ctx, {
                    type: chartType,
                    data: {
                        labels: labels,
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

                var ctx1 = document.getElementById('chartTotalCollectedTransactionState');
                if (myChart1) myChart1.destroy();
                myChart1 = new Chart(ctx1, {
                    type: chartType,
                    data: {
                        labels: labels,
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
        changeType('User');
    })

    jQuery('#format').change(function() {
        format = $('#format').find(":selected").val();
    });

    jQuery('#state').change(function() {
        showStateData();
    });

    function showStateData() {
        format = $('#format').find(":selected").val();
        date_from = $('#date_from').val();
        date_to = $('#date_to').val();
        var state_id = $('#state').find(":selected").val();
        if (state_id == '') {
            if (myChart2)
                myChart2.destroy();
            return;
        }
        jQuery.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        jQuery.ajax({
            url: '{{ route("report.get_collection_collected_transaction_district") }}',
            method: 'post',
            data: {
                "_token": "{{ csrf_token() }}",
                state_id: state_id,
                type: type,
                format: format,
                hub_type: 0,
                date_from: date_from,
                date_to: date_to
            },
            success: function(data) {
                if (type == "User") {
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
                }
                if (type == "Hub") {
                    var hubs = data[0];
                    var colors = data[1];
                    var dataCompany = data[2];
                    var labels = data[3];
                    var dataIndividual = data[4];
                    dataset2 = [];
                    for (var i = 0; i < dataCompany.length; i++) {
                        dataset2.push({
                            label: hubs[i].hub_name + " - B2B",
                            borderColor: colors[i],
                            pointBackgroundColor: colors[i],
                            backgroundColor: colors[i],
                            data: Object.values(dataCompany[i]),
                            fill: 'transparent',
                            minBarLength: 7,
                        })
                    }
                    for (var i = 0; i < dataIndividual.length; i++) {
                        dataset2.push({
                            label: hubs[i].hub_name + " - B2C",
                            borderColor: colors[i + hubs.length],
                            pointBackgroundColor: colors[i + hubs.length],
                            backgroundColor: colors[i + hubs.length],
                            data: Object.values(dataIndividual[i]),
                            fill: 'transparent',
                            minBarLength: 7,
                        })
                    }
                }
                var ctx1 = document.getElementById('chartTotalCollectedTransactionDistrict');
                if (myChart2) myChart2.destroy();
                if (type == 'User') {
                    chartType = 'line'
                } else if (type == 'Hub') {
                    chartType = 'bar'
                }
                myChart2 = new Chart(ctx1, {
                    type: chartType,
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

    function changeType(tabName) {
        $("button[id*='btnTab']").each(function() {
            ($(this).get(0).style.backgroundColor = '#fff');
        });
        type = tabName;
        if (type == "User") {
            document.getElementById("chart2").innerHTML = "Total Collected Transaction Across States";
            document.getElementById("chart3").innerHTML = "Total Collected Transaction Across District";
        } else if (type == "Hub") {
            document.getElementById("chart2").innerHTML = "Total Collected Transaction By Collection Hub Across States";
            document.getElementById("chart3").innerHTML = "Total Collected Transaction by Collection Hub Across District";
        }
        showData();
        if (myChart2) myChart2.destroy();
        document.getElementById('btnTab' + tabName).style.display = "block";
        document.getElementById('btnTab' + tabName).style.backgroundColor = "#e0e0d1";
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