       

<img class="mypic" src="{{$User->image?url('public/uploads/users/'.$User->image):url('public/uploads/users/default.png')}}" alt="">
<a href="{{_url('customer/dashboard')}}" class="btn btn-profile bg">{{_lang('app.profile')}}</a>
<a href="{{_url('customer/user/edit')}}" class="btn btn-profile bg">{{_lang('app.edit_profile')}}</a>
<a href="{{_url('customer/wishlist')}}" class="btn btn-profile bg">{{_lang('app.wishlist')}}</a>
<a href="{{_url('customer/ads')}}" class="btn btn-profile bg">{{_lang('app.my_ads')}}</a>
<a href="{{_url('customer/ads/create')}}" class="btn btn-profile bg">{{_lang('app.new_ad')}}</a>   
<a href="{{ route('logout') }}" class="btn btn-profile bg"
   onclick="event.preventDefault();
           document.getElementById('logout-form').submit();">{{ _lang('app.logout') }}</a>
<form id="logout-form" action="{{ _url('logout') }}" method="POST" style="display: none;">
    {{ csrf_field() }}
</form>
