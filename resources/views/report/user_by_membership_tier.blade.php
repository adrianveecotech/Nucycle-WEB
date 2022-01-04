@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col">
            <div class="card">
                <div class="card-header">{{ __('Total Number of User by Membership Tiers') }}</div>
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
    var data = <?php echo json_encode($data); ?>;
    var label = <?php echo json_encode($label); ?>;
    var ctx = document.getElementById('myChart');
    var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: label,
            datasets: [{
                label: 'Number of Users',
                data: data,
                backgroundColor: [
                    'rgba(99, 99, 99, 0.2)',
                    'rgba(99, 99, 99, 0.2)',
                ],
                borderColor: [
                    'rgba(99, 99, 99, 1)',
                    'rgba(99, 99, 99, 1)',
                ],
                borderWidth: 1
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
                    }
                }]
            }
        }
    });
</script>
@endsection