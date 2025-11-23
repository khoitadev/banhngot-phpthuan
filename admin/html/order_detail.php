<?php
ob_start();
include($_SERVER["DOCUMENT_ROOT"] . '/admin/inc/header.php');
include($_SERVER['DOCUMENT_ROOT'] . "/admin/inc/navbar.php");
include($_SERVER['DOCUMENT_ROOT'] . "/database/connect.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/helpers/order_status.php");

if (isset($_GET['id'])) {
    $id_order = $_GET['id'];

    $query = "SELECT * FROM oders where OderId = '$id_order'";
    $order_query = mysqli_query($conn, $query);
    $order = mysqli_fetch_assoc($order_query);

    $id_custommer = $order['CustomerId'];

    $custommer_query = mysqli_query($conn, "SELECT * from customers where CustomerId = '$id_custommer'");
    $customer = mysqli_fetch_assoc($custommer_query);

    $products_query = "SELECT a.Quantity, a.Price, p.Image, p.Name  FROM orderdetails a, products p, oders o 
                where a.ProductId = p.ProductId  and a.Order_Detail_Id = o.OderId and o.OderId = '$id_order'";
    $products = mysqli_query($conn, $products_query);

    if (isset($_POST['submit'])) {
        $newStatus = (int)$_POST['status'];
        $oldStatus = (int)$order['status'];
        $statusInfo = getOrderStatus($newStatus);
        $adminName = isset($_SESSION['admin']) ? $_SESSION['admin']['Username'] : 'Admin';
        
        // Cập nhật status và các ngày tương ứng
        $updateFields = ["status = '$newStatus'", "status_name = '" . mysqli_real_escape_string($conn, $statusInfo['name']) . "'", "status_updated_at = NOW()"];
        
        if ($newStatus == 2) { // Đang giao hàng
            $updateFields[] = "shipping_date = NOW()";
        } elseif ($newStatus == 3) { // Đã nhận hàng
            $updateFields[] = "delivered_date = NOW()";
        }
        
        $updateQuery = "UPDATE oders SET " . implode(", ", $updateFields) . " WHERE OderId = '$id_order'";
        $result = mysqli_query($conn, $updateQuery);
        
        if ($result) {
            // Lưu lịch sử thay đổi
            $historyQuery = "INSERT INTO order_status_history (OderId, OldStatus, NewStatus, StatusName, ChangedBy, Note) 
                            VALUES ('$id_order', '$oldStatus', '$newStatus', '" . mysqli_real_escape_string($conn, $statusInfo['name']) . "', 
                                    '" . mysqli_real_escape_string($conn, $adminName) . "', 'Cập nhật bởi admin')";
            mysqli_query($conn, $historyQuery);
            
            header("location: order_detail.php?id=$id_order");
            exit;
        } else {
            echo "<div class='alert alert-danger'>Xảy ra lỗi khi cập nhật!</div>";
        }
    }
    
    $statusInfo = getOrderStatus($order['status']);
    $allStatuses = getAllOrderStatuses();
}

?>
<div class="layout-page">
    <!-- Navbar -->

    <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
        <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
            <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                <i class="bx bx-menu bx-sm"></i>
            </a>
        </div>

        <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
            <!-- Search -->
            
            <!-- /Search -->

            <ul class="navbar-nav flex-row align-items-center ms-auto">
                <!-- Place this tag where you want the button to render. -->
                <li class="nav-item lh-1 me-3">
                </li>

                <!-- User -->
                <li class="nav-item navbar-dropdown dropdown-user dropdown">
                    <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                        <div class="avatar avatar-online">
                            <img src="../assets/img/avatars/111.png" alt class="w-px-40 h-auto rounded-circle" />
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="#">
                                <div class="d-flex">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="avatar avatar-online">
                                            <img src="../assets/img/avatars/111.png" alt class="w-px-40 h-auto rounded-circle" />
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <span class="fw-semibold d-block"><?php echo session::get('Username') ?></span>
                                        <small class="text-muted">Quản trị viên</small>
                                    </div>
                                </div>
                            </a>
                        </li>
                        
                        <li>
                            <div class="dropdown-divider"></div>
                        </li>
                        <li>
                            <a class="dropdown-item" href="?action=logout">
                                <i class="bx bx-power-off me-2"></i>
                                <span class="align-middle">Đăng xuất</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <!--/ User -->
            </ul>
        </div>
    </nav>

    <div class="content-wrapper">
        <!-- Content -->
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="row">
                <div class="panel panel-infor">
                    <div class="panel-heading">
                        <h3 class="panel-title">Thông tin khách hàng</h3>
                    </div>
                    <div class="panel-body text-left">
                        <p>Tên khách hàng: <?php echo $customer['Fullname'] ?></p>
                        <p>Số điện thoại: <?php echo $order['number_phone'] ?></p>
                        <p>Địa chỉ nhận hàng: <?php echo $order['address'] ?></p>
                        <p>Ngày đặt hàng: <?php echo $order['order_date'] ?></p>
                        <p>Ghi chú của khách hàng: <?php echo $order['Note'] ?> </p>
                        <p>Trạng thái đơn hàng: <?php echo getOrderStatusBadge($order['status']); ?></p>
                        <?php if ($order['status_updated_at']) : ?>
                            <p>Cập nhật lần cuối: <?php echo date('d/m/Y H:i', strtotime($order['status_updated_at'])); ?></p>
                        <?php endif; ?>
                        <?php if ($order['shipping_date']) : ?>
                            <p>Ngày giao hàng: <?php echo date('d/m/Y H:i', strtotime($order['shipping_date'])); ?></p>
                        <?php endif; ?>
                        <?php if ($order['delivered_date']) : ?>
                            <p>Ngày nhận hàng: <?php echo date('d/m/Y H:i', strtotime($order['delivered_date'])); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <!-- Basic Bootstrap Table -->
            <div class="panel-heading">
                <h3 class="panel-title">Thông tin chi tiết đơn hàng</h3>
            </div>
            <div class="card">
                <div class="table-responsive text-nowrap">

                    <table class="table" style="text-align: center">
                        <thead>
                            <tr>
                                <th>STT</th>
                                <th>Tên sản phẩm</th>
                                <th>Hình ảnh</th>
                                <th>Số lượng</th>
                                <th>Giá</th>
                                <th>Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            <?php
                            $total_price = 0;
                            foreach ($products as $key => $value) : $total_price += $value['Price']  * $value['Quantity']; ?>
                                <tr>
                                    <td><?php echo $key + 1 ?></td>
                                    <td><?php echo $value['Name'] ?></td>
                                    <td>
                                        <img src="..//uploads//<?php echo $value['Image'] ?>" alt="" width="100">
                                    </td>
                                    <td><?php echo $value['Quantity'] ?></td>
                                    <td><?php echo $value['Price'] ?></td>
                                    <td><?php echo $value['Quantity'] * $value['Price']  ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <tr>
                                <td>Tổng tiền: </td>
                                <td class="bg-infor"><?php echo $total_price ?> VND</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card mt-4">
                <div class="card-header">
                    <h5>Cập nhật trạng thái đơn hàng</h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="form-group">
                            <label for="status">Trạng thái:</label>
                            <select name="status" id="status" class="form-control" required>
                                <?php foreach ($allStatuses as $code => $name) : 
                                    $selected = ($code == $order['status']) ? 'selected' : '';
                                ?>
                                    <option value="<?php echo $code; ?>" <?php echo $selected; ?>>
                                        <?php echo htmlspecialchars($name); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button class="btn btn-primary" type="submit" name="submit">
                            <i class="fa fa-save"></i> Cập nhật trạng thái
                        </button>
                        <a href="order_list.php" class="btn btn-secondary">Quay lại</a>
                    </form>
                </div>
            </div>
            
            <!-- Status History -->
            <?php
            $historyQuery = "SELECT * FROM order_status_history 
                           WHERE OderId = '$id_order' 
                           ORDER BY CreatedAt DESC";
            $historyResult = mysqli_query($conn, $historyQuery);
            if (mysqli_num_rows($historyResult) > 0) :
            ?>
            <div class="card mt-4">
                <div class="card-header">
                    <h5>Lịch sử thay đổi trạng thái</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Thời gian</th>
                                    <th>Trạng thái cũ</th>
                                    <th>Trạng thái mới</th>
                                    <th>Người thay đổi</th>
                                    <th>Ghi chú</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($history = mysqli_fetch_assoc($historyResult)) : ?>
                                    <tr>
                                        <td><?php echo date('d/m/Y H:i', strtotime($history['CreatedAt'])); ?></td>
                                        <td><?php echo getOrderStatus($history['OldStatus'])['name']; ?></td>
                                        <td><strong><?php echo htmlspecialchars($history['StatusName']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($history['ChangedBy']); ?></td>
                                        <td><?php echo htmlspecialchars($history['Note']); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php
    include($_SERVER["DOCUMENT_ROOT"] . '/admin/inc/footer.php');
    ?>