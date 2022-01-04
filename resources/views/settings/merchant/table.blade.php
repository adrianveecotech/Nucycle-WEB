@if (\Session::has('successMsg'))
    <div class="alert alert-success">
        <ul>
            <li>{!! \Session::get('successMsg') !!}</li>
        </ul>
    </div>
    @endif
    <div class="row justify-content-center">
        <div class="col">
            <div id="wrapper">
                <div class="tbl-header">
                    <table class="table table-striped" id="tableMain">

                        <thead>
                            <tr>
                                <th><span>ID</span></th>
                                <th><span>Name</span></th>
                                <th><span>Address</span></th>
                                <th><span>Postcode</span></th>
                                <th><span>State</span></th>
                                <th><span>City</span></th>
                                <th><span>Phone Number</span></th>
                                <th><span>Email</span></th>
                                <th><span>URL</span></th>
                                <th><span>Active?</span></th>
                                <th><span>Actions</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($merchants as $merchant)
                            <tr>
                                <td class="lalign">{{$merchant->id}}</td>
                                <td>{{$merchant->name}}</td>
                                <td>{{$merchant->address}}</td>
                                <td>{{$merchant->postcode}}</td>
                                <td>{{$merchant->state->name}}</td>
                                <td>{{$merchant->city->name}}</td>
                                <td>{{$merchant->phone_number}}</td>
                                <td>{{$merchant->email}}</td>
                                <td>{{$merchant->url}}</td>
                                <td>{{$merchant->is_active == 1 ? 'Active' : "Inactive"}}</td>
                                <td>
                                    <a href="{{route('settings.merchant.view', ['id' => $merchant->id])}}" class="btn btn-xs btn-success button-float-right"><i class="nav-icon fa fa-eye"></i></a>
                                    <a href="{{route('settings.merchant.edit', ['id' => $merchant->id])}}" class="btn btn-xs btn-success button-float-right"><i class="nav-icon fa fa-pencil"></i></a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>