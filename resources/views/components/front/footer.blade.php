
<div class="footer">
    <div class="container">
        <div class="col-md-3 footer-grid sign-gd">
            <h4>القائمة</h4>
            <ul>
                <li><a href="index.php">الرئيسية</a></li>
                <li><a href="about.php">عن الشامل</a></li>
                <li><a href="realestate.php">عقارات</a></li>
                <li><a href="cars.php">سيارات</a></li>
            </ul>
        </div>
        <div class="col-md-3 footer-grid sign-gd">
            <h4>التصنيفات</h4> 
            <ul>
                <li><a href="department.php">ازياء رجالى</a></li>
                <li><a href="department.php">ازياء حريمي</a></li>
                <li><a href="department.php">عطور</a></li>
                <li><a href="department.php">احذية</a></li>
            </ul>
        </div>
        <div class="col-md-3 sign-gd footer-grid">
            <h4>صفحات تهمك</h4>
            <ul>
                <li><a href="policy.php">سياسة الاستخدام</a></li>
                <li><a href="contact.php">اتصل بنا</a></li>
            </ul>
        </div>
        <div class="col-md-3 footer-grid sign-gd-two">
            <h4>تواصل معنا</h4>
            <div class="w3-address">
                <div class="w3-address-grid">
                    <p>0096123456789<i class="fa fa-phone"></i></p>
                </div>
                <div class="w3-address-grid">
                    <p>المملكة العربية السعودية<i class="fa fa-map-marker"></i></p>
                </div>

                <div class="w3-address-grid">
                    <p>info@elshamel.com <i class="fa fa-envelope"></i></p>
                </div>
            </div>
            <ul class="social-nav model-3d-0 footer-social w3_agile_social two">
                <li><a href="{{ $settings['social_media']->facebook ? $settings['social_media']->facebook : '#'  }}" class="facebook">
                        <div class="front"><i class="fa fa-facebook" aria-hidden="true"></i></div>
                        <div class="back"><i class="fa fa-facebook" aria-hidden="true"></i></div></a></li>
                <li><a href="{{ $settings['social_media']->twitter ? $settings['social_media']->twitter : '#'  }}" class="twitter"> 
                        <div class="front"><i class="fa fa-twitter" aria-hidden="true"></i></div>
                        <div class="back"><i class="fa fa-twitter" aria-hidden="true"></i></div></a></li>
                <li><a href="{{ $settings['social_media']->google ? $settings['social_media']->google : '#'  }}" class="google-plus">
                        <div class="front"><i class="fa fa-google-plus" aria-hidden="true"></i></div>
                        <div class="back"><i class="fa fa-google-plus" aria-hidden="true"></i></div></a></li>
                <li><a href="{{ $settings['social_media']->youtube ? $settings['social_media']->youtube : '#' }}" class="youtube">
                        <div class="front"><i class="fa fa-youtube" aria-hidden="true"></i></div>
                        <div class="back"><i class="fa fa-youtube" aria-hidden="true"></i></div></a></li>
            </ul>
        </div>

        <div class="clearfix"></div>
        <p class="copy-right"> © جميع الحقوق محفوظة الشامل -<a href="http://atiafco.com/" target="_blank"> تصميم وبرمجة شركة اطياف للحلول المتكاملة  	</a></p>
    </div>
</div>

<a href="#home" class="scroll" id="toTop" style="display: block;"> <span id="toTopHover" style="opacity: 1;"> </span></a>