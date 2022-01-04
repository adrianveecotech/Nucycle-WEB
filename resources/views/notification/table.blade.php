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
                                <th><span>Title</span></th>
                                <th><span>Message</span></th>
                                <th><span>Recipient</span></th>
                                <th><span>Status</span></th>
                                <th><span>Time Set</span></th>
                                <th><span>Time Sent</span></th>
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
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>