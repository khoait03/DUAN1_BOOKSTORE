<!-- Breadcrumb Begin -->
<?php
    $success = '';
    $error = '';
try {    
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["checkout"])) {
        // Table orders
        $user_id = $_POST["user_id"];
        $total = $_POST["total_checkout"];
        $address = $_POST["address"];
        $phone = $_POST["phone"];
        $note = $_POST["note"];

        // Table orderdetails
        $arr_product_id = $_POST["product_id"];
        $arr_quantity = $_POST["quantity"];
        $arr_price = $_POST["price"];

        // Bước 1: Insert dữ liệu vào orders
        $OrderModel->insert_orders($user_id, $total, $address, $phone, $note);
        // Bước 2: Lấy order_id mới tạo để thểm vào 
        $result_select = $OrderModel->select_order_id();
        $order_id = $result_select['order_id'];
        echo $order_id;

        if(!empty($order_id)) {
            // Insert orderdetails
            for ($i = 0; $i < count($arr_product_id); $i++) {
                $product_id = $arr_product_id[$i];
                $quantity = $arr_quantity[$i];
                $price = $arr_price[$i];
    
                $OrderModel->insert_orderdetails($order_id, $product_id, $quantity, $price);
            }
            // Sau khi đặt hàng xóa giỏ hàng
            $OrderModel->delete_cart_by_user_id($user_id);
            $success = 'Đặt hàng thành công ' .'<a href="index.php?url=don-hang" class="alert-link">Xem đơn hàng đã mua</a>';
        }
        

    }
} catch (Exception $e) {
    $error_message = $e->getMessage();
    echo $error_message;
}


?>
<?php 
    if(isset($_SESSION['user'])) { 
        $user_id = $_SESSION['user']['id'];
        $list_carts = $CartModel->select_all_carts($user_id);
        $count_cart = count($CartModel->count_cart($user_id));
    ?>
    <div class="breadcrumb-option">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb__links">
                        <a href="index.php"><i class="fa fa-home"></i> Trang chủ</a>
                        <span>Thanh toán</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Breadcrumb End -->

    <!-- Checkout Section Begin -->
    <section class="checkout spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <h6 class="coupon__link"><span class="icon_tag_alt mr-1"></span>Tiến hành thanh toán đơn hàng <a class="text-primary" href="gio-hang">Trở lại giỏ hàng</a> </h6>
                </div>
            </div>
            <form action="" method="post" class="checkout__form">
                <?php
                    if($success != '') {
                        $alert = $BaseModel->alert_error_success($error, $success);
                        echo $alert;
                    }
                ?>
                <div class="row">
                    <div class="col-lg-8">
                        <h5>CHI TIẾT THANH TOÁN</h5>
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-6">
                                <div class="checkout__form__input">
                                    <p>Họ tên <span>*</span></p>
                                    <input type="text" readonly name="full_name" value="<?= $_SESSION['user']['full_name'] ?>">
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6">
                                <div class="checkout__form__input">
                                    <p>Email <span>*</span></p>
                                    <input readonly type="text" name="email" value="<?= $_SESSION['user']['email'] ?>">
                                </div>
                            </div>
                            <div class="col-lg-12">

                                <div class="checkout__form__input">
                                    <p>Địa chỉ <span>*</span></p>
                                    <input readonly type="text" name="address" value="<?= $_SESSION['user']['address'] ?>">

                                </div>

                            </div>
                            <div class="col-lg-12">
                                <div class="checkout__form__input">
                                    <p>Số điện thoại <span>*</span></p>
                                    <input readonly type="text" name="phone" value="<?= $_SESSION['user']['phone'] ?>">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="checkout__form__input">
                                    <p>Ghi chú<span></span></p>
                                    <input type="text" name="note">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="cart__btn">
                                    <a href="index.php?url=ho-so">Chỉnh sửa thông tin</a>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="checkout__order">
                            <h5>ĐƠN HÀNG</h5>
                            <div class="checkout__order__product">
                                <ul>
                                    <li>
                                        <span class="top__text">Sản phẩm</span>
                                        <span class="top__text__right">Tổng</span>
                                    </li>
                                    <?php
                                        $i = 0;
                                        $totalPayment = 0;
                                        foreach ($list_carts as $value) {
                                        extract($value);
                                        $totalPrice = ($product_price * $product_quantity);
                                        $totalPayment += $totalPrice;
                                        $i++;
                                    ?>
                                    <li>
                                        <!-- Thông tin insert vào orders -->
                                        <input type="hidden" name="user_id" value="<?=$user_id?>">
                                        <input type="hidden" name="total_checkout" value="<?=$totalPayment?>">
                                        <!-- Thông tin insert vào orderdetails -->
                                        <input type="hidden" name="product_id[]" value="<?=$product_id?>">
                                        <input type="hidden" name="quantity[]" value="<?=$product_quantity?>">
                                        <input type="hidden" name="price[]" value="<?=$product_price?>">    

                                        <?=$i?>.
                                        <?=$product_name?>
                                        <a class="text-primary">x<?=$product_quantity?></a>
                                        <span><?=number_format($totalPrice)?>đ</span>
                                    </li>
                                    <?php
                                        }
                                    ?>
                                </ul>
                            </div>
                            <div class="checkout__order__total">
                                <ul>
                                   
                                    <li>Tổng <span><?=number_format($totalPayment)?>đ</span></li>
                                </ul>
                            </div>
                            <!-- <div class="checkout__order__widget">
                                <label for="paypal">
                                    Thanh toán khi nhận hàng
                                    <input type="checkbox" id="paypal">
                                    <span class="checkmark"></span>
                                </label>
                            </div> -->
                            <?php if($count_cart > 0) {?>
                                
                            <button type="submit" name="checkout" class="site-btn">ĐẶT HÀNG</button>
                            <?php }?>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
    <!-- Checkout Section End -->
<?php } else { ?>
    <div class="row" style="margin-bottom: 400px;">
        <div class="col-lg-12 col-md-12">
            <div class="container-fluid mt-5">
                <div class="row rounded justify-content-center mx-0 pt-5">
                    <div class="col-md-6 text-center">
                        <h4 class="mb-4">Vui lòng đăng nhập để có thể thanh toán</h4>
                        <a class="btn btn-primary rounded-pill py-3 px-5" href="index.php?url=dang-nhap">Đăng nhập</a>
                        <a class="btn btn-secondary rounded-pill py-3 px-5" href="index.php">Trang chủ</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>


<style>
    .cart__btn a:hover {
        background-color: #0A68FF;
        color: #fff;
        transition: 0.2s;
    }
</style>