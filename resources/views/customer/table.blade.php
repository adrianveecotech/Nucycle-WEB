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
                            <th><span>Name</span></th>
                            <th><span>Email</span></th>
                            <th><span>Phone</span></th>
                            <th><span>City</span></th>
                            <th><span>State</span></th>
                            <th><span>Individual or Company</span></th>
                            <th><span>Created At</span></th>
                            <th><span>Actions</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($customers as $customer)
                        <tr>
                            <td class="lalign">{{$customer->name}}</td>
                            <td>{{$customer->email}}</td>
                            <td>{{$customer->phone}}</td>
                            <td>{{$customer->city ? $customer->City->name : ''}}</td>
                            <td>{{$customer->State  ? $customer->State->name : ''}}</td>
                            <td>{{$customer->isIndividual == 1 ? 'Individual' : 'Company'}}</td>
                            <td>{{$customer->created_at}}</td>
                            <td>
                                <a href="{{route('customer.view', ['id' => $customer->id])}}" class="btn btn-xs btn-success"><i class="nav-icon fa fa-eye"></i></a>
                                <a href="{{route('customer.edit', ['id' => $customer->id])}}" class="btn btn-xs btn-success"><i class="nav-icon fa fa-pencil"></i></a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>