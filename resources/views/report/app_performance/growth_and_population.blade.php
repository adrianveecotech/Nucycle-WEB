@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col">
            <div class="card">
                <div class="card-header">App Performance - Growth and Population</div>
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
                        <div id="divFormat">
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
                        </div>
                        <a href="#" onclick="printReport()" id="btn_export" class="btn btn-xs btn-success ml-2">PDF</a>
                        <a href="#" onclick="exportCsv()" id="btn_csv" class="btn btn-xs btn-success ml-2">CSV</a>
                    </div>

                    <div class="w3-bar hide-print">
                        <button class="w3-bar-item w3-button" id="btnTabNewRegistration" onclick="changeType('NewRegistration')">New Registration</button>
                        <button class="w3-bar-item w3-button" id="btnTabThirtyDaysLogin" onclick="changeType('ThirtyDaysLogin')">Last 30 days Login</button>
                        <button class="w3-bar-item w3-button" id="btnTabActiveTransaction" onclick="changeType('ActiveTransaction')">Active Transaction</button>
                        <button class="w3-bar-item w3-button" id="btnTabMembershipTier" onclick="changeType('MembershipTier')">Membership Tier</button>
                        <button class="w3-bar-item w3-button" id="btnTabUserPreference" onclick="changeType('UserPreference')">User Preference</button>
                    </div>

                    <h5 id="titleCanvas1" class="text-center mt-3">Total New User Registration</h5>
                    <canvas id="canvas1" width="300"></canvas>

                    <br>
                    <br>
                    <div id="divState">
                        <h5 id="titleCanvas2" class="text-center mt-3">Total New User Registration Across States</h5>
                        <canvas id="canvas2" width="300"></canvas>
                    </div>
                    <br>
                    <br>
                    <div id="divDistrict">
                        <label for="state">State:</label>

                        <select class="ml-2" name="state" id="state">
                            <option value="">Select a state</option>
                            @foreach ($allStates as $value)
                            <option value="{{$value['id']}}">{{$value['name']}}</option>
                            @endforeach
                        </select>
                        <h5 id="titleCanvas3" class="text-center mt-3">Total New User Registration Across District</h5>
                        <canvas id="canvas3" width="300"></canvas>

                    </div>
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
    var type = 'NewRegistration';
    var format = 'Month';
    var label = [];
    var datasets = [];
    var dataset = [];
    var state = '';
    var dataset2 = [];
    var tier = [];
    var labelTier = [];
    var number_user_reward_category = [];
    var reward_category = [];
    var tableData = [];
    var date_from = '';
    var date_to = '';

    function exportCsv() {
        state = $('#state').find('option:selected').text();

        var title = '';
        let csvContent = "data:text/csv;charset=utf-8,";

        if (type == "NewRegistration") {
            title = "Total New User Registration";
        } else if (type == "ThirtyDaysLogin") {
            title = "Total Number of Users login at least once for the past 30 days";
        } else if (type == "ActiveTransaction") {
            title = "Total Number of Users performed at least one transaction in a particular month";
        } else if (type == "MembershipTier") {
            title = "Total Number of Users in Membership Tier";
        } else if (type == "UserPreference") {
            title = "Total Number of Users Preferred Reward Category";
        }

        if (type == "MembershipTier") {
            csvContent += title + "\r\n";
            labelsArr = [];
            labelsArr.push(labelTier);
            dataset = [];
            dataset.push(tier);
            labelsArr.forEach(function(rowArray) {
                let row = rowArray.join(",");
                csvContent += ' ,' + row + "\r\n\r\n";
            });
            dataset.forEach(function(rowArray) {
                let row = 'Number of Users' + ',' + rowArray.join(",");
                console.log(row);
                csvContent += row + "\r\n";
            });

            csvContent += "\r\n Email, Level\r\n";
            tableData.forEach(function(rowArray) {
                let row = rowArray.email + ',' + rowArray.name;
                csvContent += row + "\r\n";
            });
        } else if (type == "UserPreference") {
            csvContent += title + "\r\n\r\n Email, Reward Category\r\n";
            tableData.forEach(function(rowArray) {
                let row = rowArray.email + ',' + rowArray.name;
                csvContent += row + "\r\n";
            });
        } else {
            labelsArr = [];
            labelsArr.push(label);

            csvContent += title + " - " + format + "\r\n";

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
            if (type == "NewRegistration") {
                csvContent += "\r\nDate,Email,Type,State,City\r\n";
                tableData.forEach(function(rowArray) {
                    let row = rowArray.date + ',' + rowArray.email + ',' + rowArray.type + ',' + rowArray.state + ',' + rowArray.city;
                    csvContent += row + "\r\n";
                });
            } else if (type == "ThirtyDaysLogin") {
                csvContent += "\r\Active User Email\r\n";
                tableData.forEach(function(rowArray) {
                    let row = rowArray.email;
                    csvContent += row + "\r\n";
                });
            } else if (type == "ActiveTransaction") {
                csvContent += "\r\Email,Type,Date,\r\n";
                tableData.forEach(function(rowArray) {
                    let row = rowArray.email + ',' + rowArray.type + ',' + rowArray.date;
                    csvContent += row + "\r\n";
                });
            }



        }

        var encodedUri = encodeURI(csvContent);
        var link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", title + ".csv");
        document.body.appendChild(link);
        link.click()
    }

    function showData() {
        var chartType = '';
        date_from = $('#date_from').val();
        date_to = $('#date_to').val();
        if (type == 'NewRegistration') {
            url = '{{ route("report.app_performance.get_new_registered_user") }}'
            document.getElementById('titleCanvas1').innerHTML = "Total New User Registration";
            document.getElementById('titleCanvas2').innerHTML = "Total New User Registration Across States";
            document.getElementById('titleCanvas3').innerHTML = "Total New User Registration Across District";
            $('#divState').show();
            $('#divDistrict').show();
            $("#divFormat").show();
            if (myChart2) myChart2.destroy();

        }
        if (type == 'ThirtyDaysLogin') {
            dataset2 = [];
            url = '{{ route("report.app_performance.get_thirty_days_login") }}'
            document.getElementById('titleCanvas1').innerHTML = "Total Number of Users login at least once for the past 30 days";
            document.getElementById('titleCanvas2').innerHTML = "Total Number of Users login at least once for the past 30 days Across States";
            document.getElementById('titleCanvas3').innerHTML = "Total Number of Users login at least once for the past 30 days Across District";
            // $("#divDistrict").hide();
            $('#divDistrict').show();
            $('#divState').show();
            $("#divFormat").show();
            if (myChart2) myChart2.destroy();

        }

        if (type == 'ActiveTransaction') {
            dataset2 = [];
            url = '{{ route("report.app_performance.get_active_transaction") }}'
            document.getElementById('titleCanvas1').innerHTML = "Total Number of Users performed at least one transaction in a particular month";
            document.getElementById('titleCanvas2').innerHTML = "Total Number of Users performed at least one transaction in a particular month Across States";
            document.getElementById('titleCanvas3').innerHTML = "Total Number of Users performed at least one transaction in a particular month Across District";
            $('#divDistrict').show();
            $("#divState").show();
            $("#divFormat").show();
            if (myChart2) myChart2.destroy();

        }
        jQuery.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        jQuery.ajax({
            url: url,
            method: 'post',
            data: {
                "_token": "{{ csrf_token() }}",
                type: type,
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
                tableData = data[7];

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

    function showMembershipTier() {
        document.getElementById('titleCanvas1').innerHTML = "Total Number of Users in Membership Tier";
        $('#divState').hide();
        $('#divDistrict').hide();
        $("#divFormat").hide();
        var chartType = '';
        jQuery.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        jQuery.ajax({
            url: '{{ route("report.app_performance.get_membership_tier") }}',
            method: 'post',
            data: {
                "_token": "{{ csrf_token() }}",
            },
            success: function(data) {
                var ctx = document.getElementById('canvas1');
                tier = data[0];
                labelTier = data[1];
                var colors = data[2];
                tableData = data[3];
                if (myChart) myChart.destroy();
                myChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labelTier,
                        datasets: [{
                            label: 'Number of Users',
                            data: tier,
                            backgroundColor: colors
                        }]
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
                        legend: {
                            display: false
                        },
                        tooltips: {
                            enabled: true
                        }

                    }
                });
            },
            error: function(data) {
                console.log(data.responseJSON.message);
            }
        });
    }

    function showUserPreference() {
        document.getElementById('titleCanvas1').innerHTML = "Total Number of Users Preferred Reward Category";

        $('#divState').hide();
        $('#divDistrict').hide();
        $("#divFormat").hide();
        var chartType = '';
        jQuery.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        jQuery.ajax({
            url: '{{ route("report.app_performance.get_user_preference") }}',
            method: 'post',
            data: {
                "_token": "{{ csrf_token() }}",
            },
            success: function(data) {
                var ctx = document.getElementById('canvas1');
                number_user_reward_category = data[0];
                reward_category = data[1];
                var colors = data[2];
                tableData = data[3];
                if (myChart) myChart.destroy();
                myChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: reward_category,
                        datasets: [{
                            label: 'Number of Users',
                            data: number_user_reward_category,
                            backgroundColor: colors
                        }]
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
                            xAxes: [{
                                maxBarThickness: 40,
                            }]

                        },
                        legend: {
                            display: false
                        },
                        tooltips: {
                            enabled: true
                        }

                    }
                });
            },
            error: function(data) {
                console.log(data.responseJSON.message);
            }
        });
    }

    $(document).ready(function() {
        changeType(type);
    })


    jQuery('#format').change(function() {
        format = $('#format').find(":selected").val();
    });

    jQuery('#state').change(function() {
        showStateData();
    });

    function showStateData() {
        var urlState = '';
        var date_from = $('#date_from').val();
        var date_to = $('#date_to').val();
        var state_id = $('#state').find(":selected").val();
        if (state_id == '') {
            myChart2.destroy();
            return;
        }
        if (type == 'NewRegistration') {
            urlState = '{{ route("report.app_performance.get_new_registered_user_district") }}';
        }
        if (type == 'ThirtyDaysLogin') {
            urlState = '{{ route("report.app_performance.get_thirty_days_login_district") }}';
        }
        if (type == 'ActiveTransaction') {
            urlState = '{{ route("report.app_performance.get_active_transaction_district") }}';
        }
        jQuery.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        jQuery.ajax({
            url: urlState,
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
                chartType = 'line'
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
        if (type == "NewRegistration" || type == "ThirtyDaysLogin" || type == "ActiveTransaction") {
            showData();
        }

        if (type == "MembershipTier") {
            showMembershipTier();
        }
        if (type == "UserPreference") {
            showUserPreference();
        }

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