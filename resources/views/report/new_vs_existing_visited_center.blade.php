@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col">
            <div class="card">
                <div class="card-header">{{ __('Total number of new users vs existing users visited collection center') }}</div>
                <div class="card-body">
                    <canvas id="myChart" width="300" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
    var label = <?php echo json_encode($label); ?>;
    var newUser = <?php echo json_encode($newUser); ?>;
    var existingUser = <?php echo json_encode($existingUser); ?>;
    var ctx = document.getElementById('myChart');
    var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: label,
            datasets: [{
                    label: "Existing",
                    backgroundColor: "blue",
                    data: existingUser
                },
                {
                    label: "New",
                    backgroundColor: "red",
                    data: newUser
                },
            ]
        },
        options: {
            plugins: {
                labels: false
            },
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            }
        }
    });
</script>
@endsection