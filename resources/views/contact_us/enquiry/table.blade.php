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
                                <th><span>Phone Number</span></th>
                                <th><span>Message</span></th>
                                <th><span>Time</span></th>
                                <th><span>Action</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($enquiries as $enquiry)
                            <tr>
                                <td class="lalign">{{$enquiry->name}}</td>
                                <td>{{$enquiry->email}}</td>
                                <td>{{$enquiry->phone_number}}</td>
                                <td>{{$enquiry->message}}</td>
                                <td>{{$enquiry->created_at}}</td>
                                <td>
                                    <a href="{{route('contact_us.enquiry.view', ['id' => $enquiry->id])}}" class="btn btn-xs btn-success"><i class="nav-icon fa fa-eye"></i></a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>