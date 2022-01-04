@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col">
            <div class="card">
                <div class="card-header">Collection Hub - Collected Transaction</div>
                <div class="card-body">
                    <div class="row hide-print">
                    <div class="col-lg-4 col-6">
                            <a href="{{route('report.collectionhub_collected_transaction')}}" class="small-box bg-info p-4">
                                <div class="inner">
                                    <h4>Collected Transaction</h4>

                                </div>
                            </a>
                        </div>
                        <div class="col-lg-4 col-6">
                            <a href="{{route('report.collectionhub_collected_waste')}}" class="small-box bg-danger p-4">
                                <div class="inner">
                                    <h4>Collected Waste</h4>

                                </div>
                            </a>
                        </div>
                        <div class="col-lg-4 col-6">
                            <a href="{{route('report.collectionhub_waste_selling')}}" class="small-box bg-success p-4">
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

                    <h5 class="text-center mt-3">Total Collected Transaction</h5>
                    <canvas id="chartTotalCollectedTransaction" width="300"></canvas>
                    <br>
                    <br>
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
    var date_from = '';
    var date_to = '';
    var labels = [];
    var datasets = [];

    function exportCsv() {
        labelsArr = [];
        labelsArr.push(labels);

        let csvContent = "data:text/csv;charset=utf-8,";
        csvContent += "Total Collected Transaction - " + format + "\r\n";

        if (date_from != '' && date_to != '') {
            csvContent += date_from + ' to ' + date_to + "\r\n";
        }

        labelsArr.forEach(function(rowArray) {
            let row = ' ,' + rowArray.join(",");
            csvContent += row + "\r\n";
        });
        datasets.forEach(function(rowArray) {
            let row = rowArray.label + ',' + rowArray.data.join(",");
            csvContent += row + "\r\n";
        });

        var encodedUri = encodeURI(csvContent);
        var link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "hub_collected_transaction.csv");
        document.body.appendChild(link);
        link.click()
    }

    function showData() {
        var chartType = '';
        format = $('#format').find(":selected").val();
        date_from = $('#date_from').val();
        date_to = $('#date_to').val();
        jQuery.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        jQuery.ajax({
            url: '{{ route("report.collectionhub_collected_transaction_data") }}',
            method: 'post',
            data: {
                "_token": "{{ csrf_token() }}",
                format: format,
                date_from: date_from,
                date_to: date_to

            },
            success: function(data) {
                labels = data[0];
                var company = data[1];
                var individual = data[2];
                var total = data[3];
                var ctx = document.getElementById('chartTotalCollectedTransaction');
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
                        pointBackgroundColor: '#BDBDBD',
                        backgroundColor: '#BDBDBD',
                        borderColor: '#BDBDBD',
                        hoverOffset: 10,


                    }
                ]
                if (myChart) myChart.destroy();
                myChart = new Chart(ctx, {
                    type: 'line',
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

            },
            error: function(data) {
                console.log(data.responseJSON.message);
            }
        });
    }

    $(document).ready(function() {
        showData();
    })


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