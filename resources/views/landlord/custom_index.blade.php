<!doctype html>
<html lang="en">

  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="msapplication-TileColor" content="#0E0E0E">
    <meta name="template-color" content="#0E0E0E">
    <link rel="manifest" href="manifest.json" crossorigin>
    <meta name="msapplication-config" content="browserconfig.xml">
    <meta name="description" content="Index page">
    <meta name="keywords" content="index, page">
    <meta name="author" content="">
    <link rel="shortcut icon" type="image/x-icon" href="landlord/custom/assets/imgs/template/favicon.png">
    <title>{{$general_setting->site_title}}</title>
    <script defer="defer" src="landlord/custom/assets/js/app.40ee121.bundle.js"></script>
    <link href="landlord/custom/assets/css/app.2afad0c.bundle.css" rel="stylesheet">
  </head>

  <body>
    
    <header class="header sticky-bar">
      <div class="container">
        <div class="main-header">
          <div class="header-left">
            <div class="header-logo"><a class="d-flex" href="https://neroon.co/"><img alt="NEROON" src="landlord/custom/assets/imgs/template/logo.png"></a></div>
            <div class="header-nav">
              <nav class="nav-main-menu d-none d-xl-block">
               
              </nav>
              <div class="burger-icon burger-icon-white"><span class="burger-icon-top"></span><span class="burger-icon-mid"></span><span class="burger-icon-bottom"></span></div>
            </div>
          </div>
          <div class="header-right">
            <div class="block-signin"><a class="btn btn-default hover-up icon-arrow-right" href="page-signup.html">14 JOURS D'ESSAI</a></div>
          </div>
        </div>
      </div>
    </header>
   
       
            
          </div>
        </div>
      </div>
    </div>
    <main class="main">
      <section class="section-box mt-70">
        <div class="container mt-50">
          <h3 class="text-heading-1 text-center mb-10">Choisissez la meilleure<br class="d-lg-block d-none">formule pour votre<br/> entreprise</h3>
        </div>
        <div class="container mt-20">
          <div class="text-center block-bill-2 mt-10"><span class="text-lg text-billed">Billed Monthly</span><label class="switch ml-20 mr-20"><input id="cb_billed_type" type="checkbox" name="billed_type" onchange="checkBilled()"><span class="slider round"></span></label><span class="text-lg text-billed">Bill Annually</span></div>
          <div class="block-pricing block-pricing-2 mt-70">
            <div class="row">
              <div class="col-xl-12 col-lg-12">
                <div class="row">
                  @foreach($packages as $package)
                  <?php
                      $features = json_decode($package->features);
                  ?>
                  <div class="col-xl-4 col-lg-6 col-md-6 wow animate__animated animate__fadeIn" data-wow-delay=".1s">
                    <div class="box-pricing-item hover-up">
                      <div class="box-info-price"><span class="text-heading-3 for-month display-month">{{$general_setting->currency}} {{$package->monthly_fee}}</span><span class="text-heading-3 for-year">{{$general_setting->currency}} {{$package->yearly_fee}}</span><span class="text-month for-month text-body-small color-gray-400">/month</span><span class="text-month for-year text-body-small color-gray-400">/year</span></div>
                      <div class="line-bd-bottom">
                        <h4 class="text-heading-5 mb-15">{{$package->name}}</h4>
                        <p class="text-body-small color-gray-400">All the basics for businesses that are just getting started.</p>
                      </div>
                      <ul class="list-package-feature">
                        @if(in_array("product_and_categories", $features))
                          <li>Product and Categories</li>
                        @endif
                        @if(in_array("purchase_and_sale", $features))
                          <li>Sale and Purchase</li>
                        @endif
                        @if(in_array("sale_return", $features))
                          <li>Sale Return</li>
                        @endif
                        @if(in_array("purchase_return", $features))
                          <li>Purchase Return</li>
                        @endif
                        @if(in_array("expense", $features))
                          <li>Expenses</li>
                        @endif
                        @if(in_array("transfer", $features))
                          <li>Stock Transfer</li>
                        @endif
                        @if(in_array("quotation", $features))
                          <li>Quotation</li>
                        @endif
                        @if(in_array("delivery", $features))
                          <li>Product Delivery</li>
                        @endif
                        @if(in_array("stock_count_and_adjustment", $features))
                          <li>Stock Count and Adjustment</li>
                        @endif
                        @if(in_array("Reports", $features))
                          <li>Reports</li>
                        @endif
                        @if(in_array("hrm", $features))
                          <li>HRM</li>
                        @endif
                        @if(in_array("accounting", $features))
                          <li>Accounting</li>
                        @endif
                      </ul>
                      <div><a class="btn btn-black text-body-lead icon-arrow-right-white signup-btn" data-package_id="{{$package->id}}" data-monthly_fee="{{$package->monthly_fee}}" data-yearly_fee="{{$package->yearly_fee}}">@if($package->is_free_trial) 14 JOURS D'ESSAI @else Sign Up @endif</a></div>
                    </div>
                  </div>
                  @endforeach                
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section class="section-box mt-80">
        <div class="container">
          <div class="row">
            <div class="col-lg-3 col-md-12 col-sm-12 mt-50">
              <h2 class="text-heading-1 color-gray-900 mb-30">Pourquoi Neroon ?</h2>
              <p class="text-body-excerpt color-gray-600">Adopter NEROON, C’est donner à vos équipes commerciales, marketing et administratives les moyens d'être efficaces !</p>
            </div>
            <div class="col-lg-3 col-md-12 col-sm-12 mt-50">
              <div class="list-icons">
                <div class="item-icon pl-0">
                  <div class="mb-15"><img src="landlord/custom/assets/imgs/page/homepage2/icon-acquis.svg" alt="Agon"></div>
                  <h4 class="text-heading-4">1. Un outil 100% cloud</h4>
                  <p class="text-body-text color-gray-600 mt-15">Un accès permanent depuis n'importe où et le contrôle total de votre entreprise et de tous vos processus métiers. Fédérez votre équipe.</p>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-md-12 col-sm-12 mt-50">
              <div class="list-icons">
                <div class="item-icon pl-0">
                  <div class="mb-15"><img src="landlord/custom/assets/imgs/page/homepage2/icon-active.svg" alt="Agon"></div>
                  <h4 class="text-heading-4">2. Facilité d’utilisation</h4>
                  <p class="text-body-text color-gray-600 mt-15">Une ergonomie exceptionnelle et intuitive pour une belle expérience utilisateur. Facile à prendre en main. Sécurisé, Flexible et modulable.</p>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-md-12 col-sm-12 mt-50">
              <div class="list-icons">
                <div class="item-icon pl-0">
                  <div class="mb-15"><img src="landlord/custom/assets/imgs/page/homepage2/icon-retent.svg" alt="Agon"></div>
                  <h4 class="text-heading-4">3. Une assistance 7j/7</h4>
                  <p class="text-body-text color-gray-600 mt-15">Nous apportons une assistance permanente. Quelque soit votre domaine d’activité, Neroon s'adapte à vos besoins.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
     
    </main>
    <footer class="footer mt-50">
      <div class="container">
       
        <div class="footer-bottom mt-20">
          <div class="row">
            <div class="col-md-6"><span class="color-gray-400 text-body-lead">&copy; NEROON</span>
              <div class="footer-social"><a class="icon-socials icon-facebook" href="https://www.facebook.com/nneroon" target="_blank"></a><a class="icon-socials icon-twitter" href="https://twitter.com/Neroonn_" target="_blank"></a><a class="icon-socials icon-instagram" href="https://www.instagram.com/neroonn_/" target="_blank"></a><a class="icon-socials icon-linkedin" href="https://www.linkedin.com/company/neroon/" target="_blank"></a></div>
            </div>
          </div>
        </div>
      </div>
    </footer>
    <script src="landlord/custom/assets/js/modernizr-3.6.0.min.js"></script>
    <script src="landlord/custom/assets/js/jquery-3.6.0.min.js"></script>
    <script src="landlord/custom/assets/js/jquery-migrate-3.3.0.min.js"></script>
    <script src="landlord/custom/assets/js/bootstrap.bundle.min.js"></script>
    <script src="landlord/custom/assets/js/waypoints.js"></script>
    <script src="landlord/custom/assets/js/wow.js"></script>
    <script src="landlord/custom/assets/js/magnific-popup.js"></script>
    <script src="landlord/custom/assets/js/perfect-scrollbar.min.js"></script>
    <script src="landlord/custom/assets/js/select2.min.js"></script>
    <script src="landlord/custom/assets/js/isotope.js"></script>
    <script src="landlord/custom/assets/js/scrollup.js"></script>
    <script src="landlord/custom/assets/js/counterup.js"></script>
    <script src="landlord/custom/assets/js/slick.js"></script>
    <script src="landlord/custom/assets/js/jquery.elevatezoom.js"></script>
    <script src="landlord/custom/assets/js/swiper-bundle.min.js"></script>
    <script src="landlord/custom/assets/js/noUISlider.js"></script>
    <script src="landlord/custom/assets/js/slider.js"></script>
    <script src="landlord/custom/assets/js/main.js?v=1.0"></script>

    <script>
        central_domain = <?php echo json_encode(env('CENTRAL_DOMAIN')) ?>;
        $(".signup-btn").on("click", function () {
            if($('input[name=subscription_type]').val() == 'monthly') {
                price = $(this).data('monthly_fee');
                subscription_type = 'monthly';
            } 
            else {
              price = $(this).data('yearly_fee');
              subscription_type = 'yearly';
            }
            window.open(
              'https://'+central_domain+'/sign-up?package_id='+$(this).data('package_id')+'&subscription_type='+subscription_type+'&price='+price,
              '_blank' // <- This is what makes it open in a new window.
            );
        });

        $('input[name=tenant]').on('input', function () {
            var tenant = $(this).val();
            var letters = /^[a-zA-Z0-9]+$/;
            if(!letters.test(tenant)) {
                alert('Tenant name must be alpha numeric(a-z and 0-9)!');
                tenant = tenant.substring(0, tenant.length-1);
                $('input[name=tenant]').val(tenant);
            }
        });

        $(document).on('submit', '.customer-signup-form', function(e) {
            $("#submit-btn").prop('disabled', true);
            $("p#waiting-msg").text("Please wait. It will take some few seconds. System will redirect you to the tenant url automatically.")
        });
    </script>
  </body>

</html>