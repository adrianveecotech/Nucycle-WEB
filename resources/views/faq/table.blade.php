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
                                <th><span>Question</span></th>
                                <th><span>Answer</span></th>
                                <th><span>Action</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($faqs as $faq)
                            <tr>
                                <td class="lalign">{{$faq->question}}</td>
                                <td>{{$faq->answer}}</td>
                                <td>
                                    <a href="{{route('faq.view', ['id' => $faq->id])}}" class="btn btn-xs btn-success"><i class="nav-icon fa fa-eye"></i></a>
                                    <a href="{{route('faq.edit', ['id' => $faq->id])}}" class="btn btn-xs btn-success"><i class="nav-icon fa fa-pencil"></i></a>
                                    <a href="{{route('faq.delete', ['id' => $faq->id])}}" class="btn btn-xs btn-danger"><i class="nav-icon fa fa-trash"></i></a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>