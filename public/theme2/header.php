<html>
    <head>
        <link rel="stylesheet" href="css/stylesheet.css">
        <link rel="stylesheet" href="css/responsive.css">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
        <script src="https://kit.fontawesome.com/d222f8242c.js" crossorigin="anonymous"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>	
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body>
        <div class="nav">
            <div class="container">
                <div class="inside-nav">
                    <div class="marquee">
                       <p>Welcome To AyanStore.PK | we offer Free Delivery over purchase of Rs. 5000 all over Pakistan.</p>
                    </div>
                    <div class="nav-col-right">
                        <ul>
                            <li> <a href="#"> <i class="fa-brands fa-whatsapp"></i> 03225386000 </a> </li>
                            <li> <a href="#"> Track Order </a> </li>
                            <li> <a href="#"> About Us </a> </li>
                            <li> <a href="#"> Contact Us </a> </li>
                        </ul>
                    </div>
                </div><!--inside-nav-->
            </div><!--container-->
        </div><!--nav-->

        <!--header-->        

        <div class="header">
            <div class="container">
                <div class="inside-header">
                    <div class="header-logo">
                        <a href="index.php"> <img src="img/head-logo.jpg"></a>
                    </div><!--header-logo-->
                    <div class="header-search">
                        <form>
                            <input type="text" name="search" placeholder="search your products">
                        </form>
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </div><!--inside-search-->
                    <div class="header-login-section">
                        <ul>
                            <li> <a href="#"> Sign In or Sign Up <i class="fa-solid fa-chevron-down"></i> <img src="img/reshot-icon-user-QLCUYJBKM3.svg"></a> </li>
                            <li> <a href="#" class="openCart"> My Cart <img src="img/reshot-icon-shopping-cart-WFDT3CVZMJ.svg"></a> </li>
                        </ul>
                    </div>
                </div><!--inside-header-->
            </div><!--container-->
        </div><!--header-->
         
        <!--mobile-header-->

        <div class="mob-header">
            <div class="container">
                <div class="inside-mob-header">
                    <div class="header-mob-menu">
                        <i id="openMenuMobile" class="fa-solid fa-bars"></i>
                    </div><!--header-logo-->
                    <div class="header-mob-logo">
                        <a href="index.php"> <img src="img/head-logo.jpg"> </a>
                    </div><!--header-logo-->
                    <div class="header-mob-cart">             
                        <a href="#" class="openCart"> <img src="img/cart-mob.svg"></a>
                    </div><!--inside-search-->
                    <div class="header-mob-search">                
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </div><!--inside-search-->
                </div><!--inside-mob-header-->
            </div><!--container-->
        </div><!--mob-header-->

        
        <div id="cartSidebar" class="cart-sidebar">
            <div class="cart-header">
                <h3>Shopping Cart</h3>
                <span id="closeCart">&times;</span>
            </div>
            <div class="cart-content">
                <img src="img/cart-cut-icon.svg">
                <p>No Products In The Cart.</p>
                <button> Return To Shop </button>
                <!-- You can add cart items dynamically here -->
            </div>
        </div><!--cart-sidebar-->
        <div id="cartOverlay"></div>


        <!--category-section-top-->

        <div class="category-section-top">
            <div class="container">
                <div class="inside-cat-sec-top">
                    <div class="category-button-top">
                        <button id="openMenuDesktop"><i class="fa-solid fa-align-left"></i> All Categories</button>
                    </div><!--category-button-top-->
                    <div class="cat-sec-menu">
                        <ul>
                            <li> <a href="#"> OUR SHOP </a> </li>
                            <li> <a href="#"> BRANDS </a> </li>
                            <li> <a href="#"> ALL CATEGORIES </a> </li>
                            <li> <a href="#"> CONTACT US </a> </li>
                            <li> <a href="#"> PRIVACY POLICY </a> </li>
                            <li> <a href="#"> PAYMENT METHOD </a> </li>
                        </ul>
                    </div><!--cat-sec-menu-->
                </div><!--inside-cat-sec-top-->
            </div><!--container-->
        </div><!--category-section-top-->
        
        <!--sidebar-->

        <div id="sidebar" class="sidebar">
            <div class="sidebar-header">
                <h3>OUR CATEGORIES</h3>
                <span id="closeMenu">&times;</span>
            </div>
            <ul>
                <li class="has-submenu">
                    <a href="#"> Beauty and Personal Care </a>
                    <i class="fa-solid fa-caret-down toggle-submenu"></i>
                    <ul class="submenu">
                        <li><a href="#">Skin Care</a></li>
                        <li><a href="#">Facial Cleanser</a></li>
                        <li><a href="#">Face Serum</a></li>
                    </ul>
                </li>
                <li class="has-submenu">
                    <a href="#"> Fitness And Exercise </a>
                    <i class="fa-solid fa-caret-down toggle-submenu"></i>
                    <ul class="submenu">
                        <li><a href="#">Skin Care</a></li>
                        <li><a href="#">Facial Cleanser</a></li>
                        <li><a href="#">Face Serum</a></li>
                    </ul>
                </li>
                <li><a href="#"> Hair Care </a><i class="fa-solid fa-caret-down"></i></li>
                <li><a href="#"> Fragrance </a><i class="fa-solid fa-caret-down"></i></li>
                <li><a href="#"> Health And Wellness </a><i class="fa-solid fa-caret-down"></i></li>
                <li><a href="#"> For Men </a><i class="fa-solid fa-caret-down"></i></li>
                <li><a href="#"> For Women </a><i class="fa-solid fa-caret-down"></i></li>
                <li><a href="#"> Home And Kitchen </a><i class="fa-solid fa-caret-down"></i></li>
                <li><a href="#"> Electronics </a><i class="fa-solid fa-caret-down"></i></li>
                <li><a href="#"> Coffee Shop </a><i class="fa-solid fa-caret-down"></i></li>
                <li><a href="#"> All Categories </a><i class="fa-solid fa-caret-down"></i></li>
                <li><a href="#"> All Brands </a><i class="fa-solid fa-caret-down"></i></li>
            </ul>
        </div><!--sidebar-->
        <div id="overlay"></div>