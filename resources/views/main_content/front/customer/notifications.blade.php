@extends('layouts.profile')

@section('pageTitle',_lang('app.notifications'))


@section('js')
<script src=" {{ url('public/front/scripts') }}/contact.js"></script>
@endsection

@section('content')
@foreach($noti as $one)
<div class="block">
    <div class="blog-comment">
        <div class="col-md-10">
            <p>{!!$one->body!!}</p>
            <span>{{$one->created_at}}</span>
        </div>
        <a class="col-md-2" href="{{$one->url}}">
            <img class="img-responsive" src="{{ url('public/front/img') }}/noti.png" alt="">
        </a>
    </div>
</div>

@endforeach
<div class="pager">
    {{ $noti->links() }}
</div>


@endsection