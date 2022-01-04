@if (\Session::has('successMsg'))
<div class="alert alert-success">
    {!! \Session::get('successMsg') !!}
</div>
@endif
<div class="row justify-content-center">
    <div class="col">
        <div id="wrapper">
            <div class="tbl-header">
                <table class="table table-striped" id="tableMain">
                    <thead>
                        <tr>
                            <th><span>Description</span></th>
                            <th><span>Time</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($activities as $activity)
                        <tr>
                            <td class="lalign">{{$activity->description}}</td>
                            <td>{{$activity->created_at}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>