<div class="header-bot">
    <div class="container">
        <div class="col-md-6 logo_agile">
            <a href="{{ _url('/') }}"><img src="{{url('public/front/images')}}/logo.png" alt="" /></a>
        </div>
        <div class="col-md-6 phone-w3l">
            <ul>
                <li><a href="{{url($next_lang_code.'/'.substr(Request()->path(), 3))}}"><span class="fa fa-language" aria-hidden="true"></span>{{$next_lang_text}}</a></li>
                @if ($isUser)
                <li><a href="{{_url('customer/dashboard') }}"><span class="fa fa-user-circle" aria-hidden="true"></span>{{_lang('app.profile')}}</a></li>
                @else
                <li><a href="{{_url('login?return='.base64_encode(request()->getPathInfo() . (request()->getQueryString() ? ('?' . request()->getQueryString()) : ''))) }}"><span class="fa fa-sign-in" aria-hidden="true"></span>تسجيل دخول</a></li>
                @endif

            </ul>
        </div>
        <div class="clearfix"></div>
    </div>
</div>
<div class="ban-top">
    <div class="container">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-8">
                    <div class="row">
                        <div class="top_nav_left">
                            <nav class="navbar navbar-default">
                                <div class="container-fluid">
                                    <div class="navbar-header">
                                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                                            <span class="sr-only">Toggle navigation</span>
                                            <span class="icon-bar"></span>
                                            <span class="icon-bar"></span>
                                            <span class="icon-bar"></span>
                                        </button>
                                    </div>
                                    <div class="collapse navbar-collapse menu--shylock" id="bs-example-navbar-collapse-1">
                                        <ul class="nav navbar-nav">
                                            <li class="active menu__item--current"><a class="menu__link" href="{{_url('')}}" id="home">{{_lang('app.home')}} <span class="sr-only">(current)</span></a></li>
                                            <li><a class="menu__link" href="{{isset($categories[0])? $categories[0]->url:'' }}">{{isset($categories[0])? $categories[0]->title:'' }}</a></li>
                                            <li><a class="menu__link" href="{{isset($categories[1])? $categories[1]->url:'' }}">{{isset($categories[1])? $categories[1]->title:'' }}</a></li>
                                            <li class="dropdown">
                                                <a href="#" class="dropdown-toggle menu__link" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">{{_lang('app.categories')}}<span class="caret"></span></a>
                                                <ul class="dropdown-menu multi-column columns-3">
                                                    <div class="agile_inner_drop_nav_info">
                                                        <div class="multi-gd-img">
                                                            <ul class="multi-column-dropdown">

                                                                @foreach ($categories as $key => $category)
                                                                @continue($key == 0 || $key == 1)
                                                                <li class="col-sm-4"><a href="{{ $category->url }}">{{ $category->title }}</a></li>
                                                                @endforeach

                                                            </ul>
                                                        </div>
                                                        <div class="clearfix"></div>
                                                    </div>
                                                </ul>
                                            </li>
                                            <li><a class="menu__link" href="{{_url('about-us')}}" title="{{_lang('app.about_us')}}">{{_lang('app.about_us')}}</a></li>
                                            <li><a class="menu__link" href="{{_url('contact-us')}}" title="{{_lang('app.contact_us')}}">{{_lang('app.contact_us')}}</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </nav>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="search-agileinfo">
                        <h3 class="country" data-toggle="modal" data-target="#searchModal">{{_lang('app.choose_country')}}<i class="fa fa-globe"></i></h3>

                        <div id="searchModal" class="modal" data-backdrop="static" data-keyboard="false">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">

                                    <!-- Modal Header -->
                                    <div class="modal-header">
                                        <h4 class="modal-title">{{_lang('app.choose_country_and_city')}}</h4>
                                    </div>

                                    <!-- Modal body -->
                                    <div class="modal-body">
                                        <form role="form" id="searchForm">
                                            <div class="form-group">
                                                <select class="frm-field required sect form-control" name="country" id="country">
                                                    <option value="">{{_lang('app.choose_country')}}</option>
                                                    @foreach ($countries as $key => $one)
                                                    <option {{$country_id==$one->id?'selected':''}} value="{{$one->id}}">{{$one->title}}</option>
                                                    @endforeach

                                                </select>
                                                <span class="help-block"></span>
                                            </div>
                                            <div class="form-group">
                                                <select class="frm-field sect form-control" name="city" id="city">
                                                    <option value="">{{_lang('app.choose_city')}}</option>
                                                    @if(isset($cities))
                                                    @foreach ($cities as $key => $one)
                                                    <option {{$city_id==$one->id?'selected':''}} value="{{$one->id}}">{{$one->title}}</option>
                                                    @endforeach
                                                    @endif

                                                </select>
                                            </div>
                                        </form>
                                    </div>

                                    <!-- Modal footer -->
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary submit-form" data-dismiss="modal">{{_lang('app.apply')}}</button>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="clearfix"></div>
    </div>
</div>


