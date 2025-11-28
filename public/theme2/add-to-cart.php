<?php
    include 'header.php';
?>
    <!--add-to-cart-->

    <div class="content-indicator">
        <div class="container">
            <div class="inside-content-indicator">
                <ul>
                    <li> <a href="#"> Home </a> </li>
                    <li> <a href="#"> Billing </a> </li>
                </ul>
            </div><!--inside-content-indicator-->
        </div><!--container-->
    </div><!--content-indicator-->

    <!--billing-detail-->
    
    <div class="billing">
        <div class="container">
            <div class="inside-billing">
                <div class="billing-heading">
                    <h3> billing details </h3>
                </div>
                <div class="billing-section">
                    <div class="billing-form">
                        <form action="#" method="post">

                            <div class="form-row">
                                <label>Full Name *</label>
                                <input type="text" name="fullname" placeholder="Enter Full Name" required>
                            </div>

                            <div class="form-row">
                                <label>Email *</label>
                                <input type="email" name="email" placeholder="Enter Your Email Address" required>
                            </div>

                            <div class="form-row">
                                <label>Number *</label>
                                <input type="number" name="number" placeholder="03" required>
                            </div>

                            <div class="form-row">
                                <label>WhatsApp Number *</label>
                                <input type="number" name="whatsapp" placeholder="92" required>
                            </div>

                            <div class="form-row">
                                <label>Country *</label>
                                <select name="country" required>
                                    <option value="Pakistan">Pakistan</option>
                                    <option value="India">India</option>
                                    <option value="Bangladesh">Bangladesh</option>
                                </select>
                            </div>

                            <div class="form-row">
                                <label>City *</label>
                                <input type="text" name="city" placeholder="Enter Your City Name" required>
                            </div>

                            <div class="form-row">
                                <label>Select Quantity</label>
                                <input type="number" name="quantity" class="quantity" placeholder="Enter Your Product Quantity">
                            </div>

                            <div class="form-row">
                                <label>Complete Address</label>
                                <textarea class="address" name="address" placeholder="Please Enter Your Complete Address House No/Street No/Block Name/Main Area/City Name."></textarea>
                            </div>

                        </form>
                    </div><!--billing-form-->
                    <div class="yours-order-section">
                        <h3> your order </h3>
                        <h4> Product </h4>
                        <div class="order-detail-section">
                            <div class="your-order-image">
                                <img src="img/onion.webp">
                            </div>
                            <div class="your-order-name">
                                <h3>  Moroccan Blue Nila Skin Whitening Powder </h3>
                            </div>
                            <div class="your-order-price">
                                <p> Rs. 1500 </p>
                            </div>
                        </div><!--order-detail-section-->
                        <div class="order-shipping">
                            <h3> Shipping </h3>
                            <p> Rs: 200 </p>
                        </div>
                        <div class="order-total">
                            <h3> Total </h3>
                            <p> Rs: 200 </p>
                        </div>
                        <div class="book-my-order">
                            <button> Book My Order </button>
                        </div>
                    </div><!--your-order-section -->
                </div><!--billing-section-->
            </div><!--inside-billing-->
        </div><!--container-->
    </div><!--billing-->
        

<?php
    include 'footer.php';
?>    