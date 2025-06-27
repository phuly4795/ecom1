 <div id="newsletter" class="section">
 </div>
 <footer id="footer">
     <!-- top footer -->
     <div class="section">
         <!-- container -->
         <div class="container">
             <!-- row -->
             <div class="row">
                 <div class="col-md-3 col-xs-6">
                     <div class="footer">
                         <h3 class="footer-title">Về chúng tôi</h3>
                         <p>{{ \App\Models\Setting::get('info') }}</p>
                         <ul class="footer-links">
                             <li><a href="#"><i
                                         class="fa fa-map-marker"></i>{{ \App\Models\Setting::get('address') }}</a></li>
                             <li><a href="#"><i
                                         class="fa fa-phone"></i>+{{ \App\Models\Setting::get('phone') }}</a></li>
                             <li><a href="#"><i
                                         class="fa fa-envelope-o"></i>{{ \App\Models\Setting::get('contact_email') }}</a>
                             </li>
                         </ul>
                     </div>
                 </div>

                 <div class="col-md-3 col-xs-6">
                     <div class="footer">
                         <h3 class="footer-title">Danh mục sản phẩm</h3>
                         <ul class="footer-links">
                             @foreach ($footerCategories ?? [] as $category)
                                 <li>
                                     <a href="{{ route('category.show', $category->slug) }}">
                                         {{ $category->name }}
                                     </a>

                                 </li>
                             @endforeach
                         </ul>
                     </div>
                 </div>

                 <div class="clearfix visible-xs"></div>

                 <div class="col-md-3 col-xs-6">
                     <div class="footer">
                         <h3 class="footer-title">Các trang khác</h3>
                         <ul class="footer-links">
                             <li><a href="/lien-he">Liên hệ</a></li>
                             <li><a href="/chinh-sach-bao-hanh">Chính sách bảo hành</a></li>
                             <li><a href="{{ route('category.show', ['slug' => 'khuyen-mai']) }}">Khuyến mãi</a></li>
                         </ul>
                     </div>
                 </div>

                 <div class="col-md-3 col-xs-6">
                     <div class="footer">
                         <h3 class="footer-title">Service</h3>
                         <ul class="footer-links">
                             <li><a href="{{ route('my.account') }}">Hồ sơ cá nhân</a></li>
                             <li><a href="{{ route('cart.show') }}">Giỏ hàng</a></li>
                             <li><a href="{{ route('favorites.index') }}">Sản phẩm yêu thích</a></li>
                             <li><a href="{{ route('my.account', ['tab' => 'orders']) }}">Danh sách đơn hàng</a></li>
                         </ul>
                     </div>
                 </div>
             </div>
             <!-- /row -->
         </div>
         <!-- /container -->
     </div>
     <!-- /top footer -->

     <!-- bottom footer -->
     <div id="bottom-footer" class="section">
         <div class="container">
             <!-- row -->
             <div class="row">
                 <div class="col-md-12 text-center">
                     <ul class="footer-payments">
                         <li><a href="#"><i class="fa fa-cc-visa"></i></a></li>
                         <li><a href="#"><i class="fa fa-credit-card"></i></a></li>
                         <li><a href="#"><i class="fa fa-cc-paypal"></i></a></li>
                         <li><a href="#"><i class="fa fa-cc-mastercard"></i></a></li>
                         <li><a href="#"><i class="fa fa-cc-discover"></i></a></li>
                         <li><a href="#"><i class="fa fa-cc-amex"></i></a></li>
                     </ul>
                     <span class="copyright">
                         <!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
                         Copyright &copy;
                         <script>
                             document.write(new Date().getFullYear());
                         </script> All rights reserved | Được thực hiện bởi Lý Thành Phú - CK23V7K523 <i
                             class="fa fa-heart-o" aria-hidden="true"></i>
                     </span>
                 </div>
             </div>
             <!-- /row -->
         </div>
         <!-- /container -->
     </div>
     <!-- /bottom footer -->
 </footer>
