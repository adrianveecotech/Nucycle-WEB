@if (\Session::has('successMsg'))
    <div class="alert alert-success">
        <ul>
            <li>{!! \Session::get('successMsg') !!}</li>
        </ul>
    </div>
    @endif
    <div class="row justify-content-center mb-4 mt-3">
        <select id="hub" name="hub">
            <option value='Select'>Select a hub</option>
            @foreach ($hubs as $hub)
            <option value='{{$hub["id"]}}'>{{$hub["hub_name"]}}</option>
            @endforeach
        </select>
    </div>
    <div class="row">
        <div class="col">
            <div id="wrapper">
                <div class="tbl-header">
                    <table class="table table-striped" id="hubRecycle">
                        <thead>
                            <tr>
                                <th><span>Recycle Item</span></th>
                                <th><span>Point</span></th>
                                <th><span>Start Date</span></th>
                                <th><span>End Date</span></th>
                                <th><span>Active</span></th>
                                <th><span>Action</span></th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>

                </div>

            </div>
        </div>
    </div>