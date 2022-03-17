@if (\Session::has('successMsg'))
    <div class="alert alert-success">
        <ul>
            <li>{!! \Session::get('successMsg') !!}</li>
        </ul>
    </div>
@endif

@if (\Session::has('failMsg'))
    <div class="alert alert-danger">
        <ul>
            <li>{!! \Session::get('failMsg') !!}</li>
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
                                <th><span>Title</span></th>
                                <th><span>Message</span></th>
                                <th><span>Recipient</span></th>
                                <th><span>Status</span></th>
                                <th><span>Time Set</span></th>
                                <th><span>Time Sent</span></th>
                                <th><span>Created At</span></th>
                                <th><span>Action</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($notifications as $notification)
                            <tr>
                                <td class="lalign">{{$notification->title}}</td>
                                <td>{{$notification->message}}</td>
                                <td>{{$notification->user_type}}</td>
                                <td>{{$notification->status}}</td>
                                <td>{{$notification->time_set}}</td>
                                <td>{{$notification->time_sent}}</td>
                                <td>{{$notification->created_at}}</td>
                                <td>@if($notification->status == 'draft')<a href="{{route('notification.cancel', ['id' => $notification->id])}}" class="btn btn-xs btn-danger"><i class="nav-icon fa fa-trash"></i></a>@endif</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>