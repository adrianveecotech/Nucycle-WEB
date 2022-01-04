
@extends('layouts.app')
@section('content')

<body>

</body>

@endsection

@section('scripts')

<ul class="footer-list-top">
      <h4>Contact Us</h4>
    <li><a href="{{route('contact_us.content.index')}}">Info</a></li>
    <li><a href="{{route('contact_us.enquiry.index')}}">Enquiry</a></li>
  </ul>

@endsection