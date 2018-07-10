@extends('layouts.front')

@section('pageTitle',_lang('app.Alshamel'))

@section('js')

@endsection

@section('content')

    <div id="myCarousel" class="carousel slide" data-ride="carousel">
        <ol class="carousel-indicators">
            <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
            <li data-target="#myCarousel" data-slide-to="1" class=""></li>
            <li data-target="#myCarousel" data-slide-to="2" class=""></li>
            <li data-target="#myCarousel" data-slide-to="3" class=""></li>
            <li data-target="#myCarousel" data-slide-to="4" class=""></li> 
        </ol>
        <div class="carousel-inner" role="listbox">

            @foreach ($slider as $key => $item)
                 <div class="item {{ $key == 0 ? 'active' : 'item'.$key }}"> 
                    <img src="{{url('public/uploads/slider/'.$item->image)}}" alt="{{ $item->title }}">
                    <div class="container">
                        <div class="carousel-caption">
                            <div class="carousel-details">
                                <h3>{{ $item->title }}</h3>
                                    <a class="hvr-outline-out button2 " href="{{ $item->url }}">تفاصيل العرض</a>
                                </div>
                         </div>
                    </div>
                 </div>
            @endforeach
           
            
           
        </div>
        <a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev">
            <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="right carousel-control" href="#myCarousel" role="button" data-slide="next">
            <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
        </div> 
        
        
        <div class="agile_last_double_sectionw3ls">
            <div class="container">
                  <div class="col-md-6 multi-gd-img multi-gd-text ">
                      <a href="realestate.php"><img src="{{$categories[0]->image}}" alt=" "><h4>{{ $categories[0]->title }}</h4></a>
                  </div>
                  <div class="col-md-6 multi-gd-img multi-gd-text ">
                                 <a href="cars.php"><img src="{{$categories[1]->image}}" alt=" "><h4>{{ $categories[1]->title }}</h4></a>
                 </div>
             </div>
             <div class="clearfix"></div>
        </div>
        <div class="banner-bootom-w3-agileits">
            <div class="container">
                    <h3 class="wthree_text_info">التصنيفات</h3>
                    <div class="col-md-4 bb-grids bb-middle-agileits-w3layouts">
                        @if (isset($categories[2]))
                            <a href="department.php">
                               <div class="bb-middle-agileits-w3layouts grid">
                                    <figure class="effect-roxy">
                                        <img src="{{$categories[2]->image}}" alt=" " class="img-responsive" />
                                        <figcaption>
                                                <h3>{{ $categories[2]->title }}</h3>
                                                <p>مشاهدة المنتجات</p>
                                        </figcaption>           
                                     </figure>
                                </div>
                            </a>
                        @endif
                        @if (isset($categories[3]))
                        <a href="department.php">
                          <div class="bb-middle-agileits-w3layouts forth grid">
                                <figure class="effect-roxy">
                                    <img src="{{$categories[3]->image}}" alt=" " class="img-responsive">
                                    <figcaption>
                                            <h3>{{ $categories[3]->title }}</h3>
                                            <p>مشاهدة المنتجات</p>
                                    </figcaption>       
                                </figure>
                            </div>
                        </a>
                    </div>
                    @endif
                    @if (isset($categories[4]))
                    <div class="col-md-4 bb-grids bb-left-agileits-w3layouts">
                        <a href="department.php">
                           <div class="bb-left-agileits-w3layouts-inner grid">
                                <figure class="effect-roxy">
                                        <img src="{{$categories[4]->image}}" alt=" " class="img-responsive center" />
                                        <figcaption>
                                                <h3>{{ $categories[4]->title }}</h3>
                                                <p>مشاهدة المنتجات</p>
                                        </figcaption>           
                                </figure>
                            </div>
                        </a>
                    </div>
                    @endif
                    @if (isset($categories[5]))
                    <div class="col-md-4 bb-grids bb-middle-agileits-w3layouts">
                        <a href="department.php">
                           <div class="bb-middle-agileits-w3layouts grid">
                                <figure class="effect-roxy">
                                        <img src="{{$categories[5]->image}}" alt=" " class="img-responsive" />
                                        <figcaption>
                                                <h3>{{ $categories[5]->title }}</h3>
                                                <p>مشاهدة المنتجات</p>
                                        </figcaption>           
                                </figure>
                            </div>
                        </a>
                        @endif
                        @if (isset($categories[6]))
                        <a href="department.php">
                          <div class="bb-middle-agileits-w3layouts forth grid">
                                <figure class="effect-roxy">
                                        <img src="{{$categories[6]->image}}" alt=" " class="img-responsive">
                                        <figcaption>
                                                <h3>{{ $categories[6]->title }}</h3>
                                                <p>مشاهدة المنتجات</p>
                                        </figcaption>       
                                </figure>
                            </div>
                        </a>
                        @endif
                        <div class="clearfix"></div>
                    </div>
                    
            </div>
        </div>
        
        <div class="offers">
            <div class="container">
                <h3 class="wthree_text_info">أميز العروض</h3>
                <div id="owl-demo2" class="owl-carousel margin-bottom">
                   <div class="item">
                        <img src="{{url('public/front/images')}}/bottom222.jpg" alt="">
                        <a href="product-details.php">
                            <div class="details">
                                 <h4>اسم المنتج</h4>
                                 <i class="fa fa-star gray-star" aria-hidden="true"></i>
                                 <i class="fa fa-star gray-star" aria-hidden="true"></i>
                                 <i class="fa fa-star yellow-star" aria-hidden="true"></i>
                                 <i class="fa fa-star yellow-star" aria-hidden="true"></i>
                                 <i class="fa fa-star yellow-star" aria-hidden="true"></i>
                                 <div class="details-icon">
                                     <h5><i class="fa fa-calendar date"></i> 1مايو</h5>
                                     <h5><i class="fa fa-map-marker road"></i>1 كجم</h5>
                                 </div>
                             </div>
                        </a>
                   </div>
                   <div class="item">
                        <img src="{{url('public/front/images')}}/bottom1.jpg" alt="">
                        <a href="product-details.php">
                            <div class="details">
                                 <h4>اسم المنتج</h4>
                                 <i class="fa fa-star gray-star" aria-hidden="true"></i>
                                 <i class="fa fa-star gray-star" aria-hidden="true"></i>
                                 <i class="fa fa-star yellow-star" aria-hidden="true"></i>
                                 <i class="fa fa-star yellow-star" aria-hidden="true"></i>
                                 <i class="fa fa-star yellow-star" aria-hidden="true"></i>
                                 <div class="details-icon">
                                     <h5><i class="fa fa-calendar date"></i> 1مايو</h5>
                                     <h5><i class="fa fa-map-marker road"></i>1 كجم</h5>
                                 </div>
                             </div>
                        </a>
                   </div>
                    <div class="item">
                        <img src="{{url('public/front/images')}}/p41.jpg" alt="">
                        <a href="product-details.php">
                            <div class="details">
                                 <h4>اسم المنتج</h4>
                                 <i class="fa fa-star gray-star" aria-hidden="true"></i>
                                 <i class="fa fa-star gray-star" aria-hidden="true"></i>
                                 <i class="fa fa-star yellow-star" aria-hidden="true"></i>
                                 <i class="fa fa-star yellow-star" aria-hidden="true"></i>
                                 <i class="fa fa-star yellow-star" aria-hidden="true"></i>
                                 <div class="details-icon">
                                     <h5><i class="fa fa-calendar date"></i> 1مايو</h5>
                                     <h5><i class="fa fa-map-marker road"></i>1 كجم</h5>
                                 </div>
                             </div>
                        </a>
                   </div>
                    <div class="item">
                        <img src="{{url('public/front/images')}}/top22.jpg" alt="">
                        <a href="product-details.php">
                            <div class="details">
                                 <h4>اسم المنتج</h4>
                                 <i class="fa fa-star gray-star" aria-hidden="true"></i>
                                 <i class="fa fa-star gray-star" aria-hidden="true"></i>
                                 <i class="fa fa-star yellow-star" aria-hidden="true"></i>
                                 <i class="fa fa-star yellow-star" aria-hidden="true"></i>
                                 <i class="fa fa-star yellow-star" aria-hidden="true"></i>
                                 <div class="details-icon">
                                     <h5><i class="fa fa-calendar date"></i> 1مايو</h5>
                                     <h5><i class="fa fa-map-marker road"></i>1 كجم</h5>
                                 </div>
                             </div>
                        </a>
                   </div>
                    <div class="item">
                        <img src="{{url('public/front/images')}}/books.jpg" alt="">
                        <a href="product-details.php">
                            <div class="details">
                                 <h4>اسم المنتج</h4>
                                 <i class="fa fa-star gray-star" aria-hidden="true"></i>
                                 <i class="fa fa-star gray-star" aria-hidden="true"></i>
                                 <i class="fa fa-star yellow-star" aria-hidden="true"></i>
                                 <i class="fa fa-star yellow-star" aria-hidden="true"></i>
                                 <i class="fa fa-star yellow-star" aria-hidden="true"></i>
                                 <div class="details-icon">
                                     <h5><i class="fa fa-calendar date"></i> 1مايو</h5>
                                     <h5><i class="fa fa-map-marker road"></i>1 كجم</h5>
                                 </div>
                             </div>
                        </a>
                   </div>
                    <div class="item">
                        <img src="{{url('public/front/images')}}/bottom212.jpg" alt="">
                        <a href="product-details.php">
                            <div class="details">
                                 <h4>اسم المنتج</h4>
                                 <i class="fa fa-star gray-star" aria-hidden="true"></i>
                                 <i class="fa fa-star gray-star" aria-hidden="true"></i>
                                 <i class="fa fa-star yellow-star" aria-hidden="true"></i>
                                 <i class="fa fa-star yellow-star" aria-hidden="true"></i>
                                 <i class="fa fa-star yellow-star" aria-hidden="true"></i>
                                 <div class="details-icon">
                                     <h5><i class="fa fa-calendar date"></i> 1مايو</h5>
                                     <h5><i class="fa fa-map-marker road"></i>1 كجم</h5>
                                 </div>
                             </div>
                        </a>
                   </div>
                    <div class="item">
                        <img src="{{url('public/front/images')}}/p41.jpg" alt="">
                        <a href="product-details.php">
                            <div class="details">
                                 <h4>اسم المنتج</h4>
                                 <i class="fa fa-star gray-star" aria-hidden="true"></i>
                                 <i class="fa fa-star gray-star" aria-hidden="true"></i>
                                 <i class="fa fa-star yellow-star" aria-hidden="true"></i>
                                 <i class="fa fa-star yellow-star" aria-hidden="true"></i>
                                 <i class="fa fa-star yellow-star" aria-hidden="true"></i>
                                 <div class="details-icon">
                                     <h5><i class="fa fa-calendar date"></i> 1مايو</h5>
                                     <h5><i class="fa fa-map-marker road"></i>1 كجم</h5>
                                 </div>
                             </div>
                        </a>
                   </div>
                </div>
            </div>   
        </div>
        <div class="sale-w3ls">
                <div class="container">
                    <div class="col-md-9">
                        <div class="sales-details">
                            <h5>عن الشامل</h5>
                            <p>{{ $settings_translations->about_us }}</p>

                            <h6>حمل الآن تطبيق الشامل على هاتفك</h6>
                            <a href="#" class="bg"><img src="{{url('public/front/images')}}/apple.png" alt="" >حمل من Apple store</a>
                            <a href="#" class="bg"><img src="{{url('public/front/images')}}/google.png" alt="" >حمل من Google play</a>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="sales-img">
                            <img src="{{url('public/front/images')}}/mob.png" alt="" >
                        </div>
                    </div>
                </div>
        </div>



    



@endsection