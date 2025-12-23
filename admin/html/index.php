<?php
include($_SERVER['DOCUMENT_ROOT'] . "/admin/inc/header.php");
include($_SERVER['DOCUMENT_ROOT'] . "/admin/inc/navbar.php");
include($_SERVER['DOCUMENT_ROOT'] . "/database/connect.php");


$query = "SELECT Count(*) FROM category where status = 1";
$category = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($category);
$quantity_category = implode($data);

$query1 = "SELECT Count(*) FROM brands  where Status = 1";
$brands = mysqli_query($conn, $query1);
$data = mysqli_fetch_assoc($brands);
$quantity_brands = implode($data);

$query2 = "SELECT Count(*) FROM customers  where Status = 1";
$customers = mysqli_query($conn, $query2);
$data = mysqli_fetch_assoc($customers);
$quantity_customers = implode($data);

$query3 = "SELECT Count(*) FROM contacts";
$contacts = mysqli_query($conn, $query3);
$data = mysqli_fetch_assoc($contacts);
$quantity_contacts = implode($data);

$query4 = "SELECT Count(*) FROM oders where status = 0 order by order_date DESC";
$orders = mysqli_query($conn, $query4);
$data = mysqli_fetch_assoc($orders);
$quantity_orders = implode($data);

$query5 = "SELECT Count(*) FROM products where status = 1 and is_accept = 1";
$products = mysqli_query($conn, $query5);
$data = mysqli_fetch_assoc($products);
$quantity_products = implode($data);

// Thống kê doanh thu
// Tổng doanh thu (tất cả đơn hàng đã thành công - status = 2 hoặc 3)
$query_revenue_total = "SELECT COALESCE(SUM(COALESCE(FinalTotal, total_price)), 0) as total_revenue 
                          FROM oders WHERE status IN (2, 3)";
$result_revenue_total = mysqli_query($conn, $query_revenue_total);
$revenue_total_data = mysqli_fetch_assoc($result_revenue_total);
$revenue_total = number_format($revenue_total_data['total_revenue'], 0, ',', '.');

// Doanh thu tháng này
$query_revenue_month = "SELECT COALESCE(SUM(COALESCE(FinalTotal, total_price)), 0) as month_revenue 
                          FROM oders 
                          WHERE status IN (2, 3) 
                          AND MONTH(order_date) = MONTH(CURRENT_DATE()) 
                          AND YEAR(order_date) = YEAR(CURRENT_DATE())";
$result_revenue_month = mysqli_query($conn, $query_revenue_month);
$revenue_month_data = mysqli_fetch_assoc($result_revenue_month);
$revenue_month = number_format($revenue_month_data['month_revenue'], 0, ',', '.');

// Doanh thu hôm nay
$query_revenue_today = "SELECT COALESCE(SUM(COALESCE(FinalTotal, total_price)), 0) as today_revenue 
                          FROM oders 
                          WHERE status IN (2, 3) 
                          AND DATE(order_date) = CURDATE()";
$result_revenue_today = mysqli_query($conn, $query_revenue_today);
$revenue_today_data = mysqli_fetch_assoc($result_revenue_today);
$revenue_today = number_format($revenue_today_data['today_revenue'], 0, ',', '.');

// Thống kê đơn hàng
// Tổng đơn hàng
$query_orders_total = "SELECT COUNT(*) as total_orders FROM oders";
$result_orders_total = mysqli_query($conn, $query_orders_total);
$orders_total_data = mysqli_fetch_assoc($result_orders_total);
$orders_total = $orders_total_data['total_orders'];

// Đơn hàng thành công
$query_orders_success = "SELECT COUNT(*) as success_orders FROM oders WHERE status IN (2, 3)";
$result_orders_success = mysqli_query($conn, $query_orders_success);
$orders_success_data = mysqli_fetch_assoc($result_orders_success);
$orders_success = $orders_success_data['success_orders'];

// Đơn hàng đang xử lý
$query_orders_processing = "SELECT COUNT(*) as processing_orders FROM oders WHERE status IN (0, 1)";
$result_orders_processing = mysqli_query($conn, $query_orders_processing);
$orders_processing_data = mysqli_fetch_assoc($result_orders_processing);
$orders_processing = $orders_processing_data['processing_orders'];

// Dữ liệu biểu đồ doanh thu 7 ngày gần nhất
$chart_data = [];
$chart_labels = [];
for ($i = 6; $i >= 0; $i--) {
  $date = date('Y-m-d', strtotime("-$i days"));
  $date_label = date('d/m', strtotime("-$i days"));
  $chart_labels[] = $date_label;

  $query_chart = "SELECT COALESCE(SUM(COALESCE(FinalTotal, total_price)), 0) as daily_revenue 
                    FROM oders 
                    WHERE status IN (2, 3) 
                    AND DATE(order_date) = '$date'";
  $result_chart = mysqli_query($conn, $query_chart);
  $chart_row = mysqli_fetch_assoc($result_chart);
  $chart_data[] = (float)$chart_row['daily_revenue'];
}

// Dữ liệu biểu đồ tròn - Phân bố đơn hàng theo trạng thái
$pie_chart_data = [];
$pie_chart_labels = [];
$pie_chart_colors = [];

$status_map = [
  0 => ['label' => 'Chờ xử lý', 'color' => '#ffab00'],
  1 => ['label' => 'Đang chuẩn bị', 'color' => '#03c3ec'],
  2 => ['label' => 'Đang giao hàng', 'color' => '#696cff'],
  3 => ['label' => 'Đã nhận hàng', 'color' => '#71dd37'],
  4 => ['label' => 'Chờ đánh giá', 'color' => '#8592a3'],
  5 => ['label' => 'Hoàn thành', 'color' => '#20c997'],
  -1 => ['label' => 'Đã hủy', 'color' => '#ff3e1d']
];

foreach ($status_map as $status_code => $status_info) {
  // Xử lý status âm đúng cách - sử dụng prepared statement để tránh SQL injection
  if ($status_code == -1) {
    $query_pie = "SELECT COUNT(*) as count FROM oders WHERE status = -1";
  } else {
    $query_pie = "SELECT COUNT(*) as count FROM oders WHERE status = " . (int)$status_code;
  }

  $result_pie = mysqli_query($conn, $query_pie);

  if ($result_pie) {
    $pie_row = mysqli_fetch_assoc($result_pie);
    $count = (int)$pie_row['count'];

    // Thêm tất cả status, kể cả count = 0 để hiển thị đầy đủ
    $pie_chart_data[] = $count;
    $pie_chart_labels[] = $status_info['label'];
    $pie_chart_colors[] = $status_info['color'];
  } else {
    // Nếu query lỗi, vẫn thêm với count = 0
    $pie_chart_data[] = 0;
    $pie_chart_labels[] = $status_info['label'];
    $pie_chart_colors[] = $status_info['color'];
  }
}

// Debug: Hiển thị dữ liệu (có thể xóa sau)
// echo "<!-- Pie Chart Data: " . print_r($pie_chart_data, true) . " -->";

$query = "SELECT o.OderId, o.number_phone, o.order_date, o.note, o.address, c.Fullname, o.total_price, o.status
          FROM oders o, Customers c where o.CustomerId = c.CustomerId and o.status = 0 order by o.order_date DESC limit 5";

$Orders = mysqli_query($conn, $query);

// Chuyển đổi dữ liệu biểu đồ sang JSON để sử dụng trong JavaScript
$chart_data_json = json_encode($chart_data, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
$chart_labels_json = json_encode($chart_labels, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
$pie_chart_data_json = json_encode($pie_chart_data, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
$pie_chart_labels_json = json_encode($pie_chart_labels, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
$pie_chart_colors_json = json_encode($pie_chart_colors, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);

?>

<body>
  <!-- Layout container -->
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

    <!-- / Navbar -->
    <!-- Content wrapper -->
    <div class="content-wrapper">
      <!-- Content -->

      <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Thống kê doanh thu -->
        <div class="row mb-4">
          <div class="col-lg-3 col-md-6 col-12 mb-4">
            <div class="card">
              <div class="card-body">
                <div class="card-title d-flex align-items-start justify-content-between">
                  <div class="avatar flex-shrink-0">
                    <i class="bx bx-dollar-circle bx-lg text-success"></i>
                  </div>
                </div>
                <span class="fw-semibold d-block mb-1">Tổng doanh thu</span>
                <h3 class="card-title mb-2"><?php echo $revenue_total; ?> đ</h3>
                <small class="text-success fw-semibold"><i class="bx bx-up-arrow-alt"></i> Tất cả thời gian</small>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-md-6 col-12 mb-4">
            <div class="card">
              <div class="card-body">
                <div class="card-title d-flex align-items-start justify-content-between">
                  <div class="avatar flex-shrink-0">
                    <i class="bx bx-calendar bx-lg text-primary"></i>
                  </div>
                </div>
                <span class="fw-semibold d-block mb-1">Doanh thu tháng này</span>
                <h3 class="card-title mb-2"><?php echo $revenue_month; ?> đ</h3>
                <small class="text-primary fw-semibold"><i class="bx bx-calendar"></i> <?php echo date('m/Y'); ?></small>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-md-6 col-12 mb-4">
            <div class="card">
              <div class="card-body">
                <div class="card-title d-flex align-items-start justify-content-between">
                  <div class="avatar flex-shrink-0">
                    <i class="bx bx-time bx-lg text-info"></i>
                  </div>
                </div>
                <span class="fw-semibold d-block mb-1">Doanh thu hôm nay</span>
                <h3 class="card-title mb-2"><?php echo $revenue_today; ?> đ</h3>
                <small class="text-info fw-semibold"><i class="bx bx-time"></i> <?php echo date('d/m/Y'); ?></small>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-md-6 col-12 mb-4">
            <div class="card">
              <div class="card-body">
                <div class="card-title d-flex align-items-start justify-content-between">
                  <div class="avatar flex-shrink-0">
                    <i class="bx bx-package bx-lg text-warning"></i>
                  </div>
                </div>
                <span class="fw-semibold d-block mb-1">Tổng đơn hàng</span>
                <h3 class="card-title mb-2"><?php echo $orders_total; ?></h3>
                <small class="text-muted">Thành công: <?php echo $orders_success; ?> | Đang xử lý: <?php echo $orders_processing; ?></small>
              </div>
            </div>
          </div>
        </div>
        <!-- / Thống kê doanh thu -->

        <div class="row">
          <div class="col-lg-8 mb-4 order-0">
            <div class="card">
              <div class="d-flex align-items-end row">
                <div class="col-sm-7">
                  <div class="card-body">
                    <h5 class="card-title text-primary">Sản phẩm mới</h5>
                    <p class="mb-4">
                      Số lượng: <?php echo $quantity_products  ?>
                    </p>
                    <a href="product_list.php" class="btn btn-sm btn-outline-primary">Xem chi tiết</a>
                  </div>
                </div>
                <div class="col-sm-5 text-center text-sm-left">
                  <div class="card-body pb-0 px-0 px-md-4">
                    <img src="../assets/img/illustrations/22.png" height="140" alt="View Badge User" data-app-dark-img="illustrations/man-with-laptop-dark.png" data-app-light-img="illustrations/22.png" />
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-4 col-md-4 order-1">
            <div class="row">
              <div class="col-lg-6 col-md-12 col-6 mb-4">
                <div class="card">
                  <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                    </div>
                    <h3>Người Dùng</h3>
                    <p class="card-title text-nowrap mb-1">Số lượng: <?php echo $quantity_customers ?> </p>
                    <a href="user_list.php" class="mt-2 btn btn-sm btn-outline-primary">Xem chi tiết</a>
                  </div>
                </div>
              </div>
              <div class="col-lg-6 col-md-12 col-6 mb-4">
                <div class="card">
                  <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                    </div>
                    <h3>Phản hồi</h3>
                    <p class="card-title text-nowrap mb-1">Số lượng: <?php echo $quantity_contacts ?> </p>
                    <a href="contact_list.php" class="mt-2 btn btn-sm btn-outline-primary">Xem chi tiết</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- Biểu đồ doanh thu và tròn -->
          <div class="col-12 col-lg-8 order-2 order-md-3 order-lg-2 mb-4">
            <div class="card">
              <div class="row row-bordered g-0">
                <div class="col-md-12">
                  <h5 class="card-header m-0 me-2 pb-3">Biểu đồ doanh thu 7 ngày gần nhất</h5>
                  <div id="revenueChart" style="min-height: 300px;"></div>
                </div>
              </div>
            </div>
          </div>
          <!-- Biểu đồ tròn - Phân bố đơn hàng -->
          <div class="col-12 col-lg-4 order-3 order-md-4 order-lg-3 mb-4">
            <div class="card">
              <div class="row row-bordered g-0">
                <div class="col-md-12">
                  <h5 class="card-header m-0 me-2 pb-3">Phân bố đơn hàng</h5>
                  <div id="orderStatusChart" style="min-height: 300px; position: relative;">
                    <div id="orderStatusChartLoading" class="text-center p-4">
                      <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!--/ Biểu đồ doanh thu và tròn -->
          <!-- Total Revenue -->
          <div class="col-12 col-lg-8 order-4 order-md-5 order-lg-4 mb-4">
            <div class="card">
              <div class="row row-bordered g-0">
                <div class="col-md-12">
                  <h5 class="card-header m-0 me-2 pb-3">Danh sách đặt hàng</h5>
                  <table class="table" style="text-align: center">
                    <thead>
                      <tr>
                        <th>STT</th>
                        <th>Tên khách hàng</th>
                        <th>Tổng tiền</th>
                        <th>Ngày đặt</th>
                        <th>Trạng thái</th>
                        <th>Chức năng</th>
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                      <?php
                      foreach ($Orders as $key => $value) : ?>
                        <tr>
                          <td><?php echo $key + 1 ?></td>
                          <td><?php echo $value['Fullname'] ?></td>
                          <td><?php echo $value['total_price'] ?></td>
                          <td><?php echo $value['order_date'] ?></td>
                          <td>
                            <?php if ($value['status'] == 0) { ?>
                              <span class="label bg-red">Chưa duyệt</span>
                            <?php } else if ($value['status'] == 1) { ?>
                              <span class="label bg-red">Đã duyệt</span>
                            <?php } else if ($value['status'] == 2) { ?>
                              <span class="label bg-red">Thành công</span>
                            <?php } else if ($value['status'] == 3) { ?>
                              <span class="label bg-red">Đã giao hàng</span>
                            <?php } ?>
                          </td>
                          <td>
                            <button type="button" class="btn btn-primary">
                              <a style="color: white" ; href="order_detail.php?id=<?php echo $value['OderId'] ?>">Chi tiết</a>
                            </button>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
          <!--/ Total Revenue -->
          <div class="col-12 col-md-8 col-lg-4 order-4 order-md-3">
            <div class="row">
              <div class="col-6 mb-4">
                <div class="card">
                  <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                    </div>
                    <h3>Thương Hiệu</h3>
                    <p class="card-title text-nowrap mb-1">Số lượng: <?php echo $quantity_brands ?> </p>
                    <a href="brand_list.php" class="mt-2 btn btn-sm btn-outline-primary">Xem chi tiết</a>
                  </div>
                </div>
              </div>
              <div class="col-6 mb-4">
                <div class="card">
                  <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                    </div>
                    <h3>Loại Bánh</h3>
                    <p class="card-title text-nowrap mb-1">Số lượng: <?php echo $quantity_category ?> </p>
                    <a href="category_list.php" class="mt-2 btn btn-sm btn-outline-primary">Xem chi tiết</a>
                  </div>
                </div>
              </div>
              <!-- </div>
    <div class="row"> -->
              <div class="col-12 mb-4">
                <div class="card">
                  <div class="card-body">
                    <div class="d-flex flex-column gap-3">
                      <div class="d-flex flex-column">
                        <h3>Đơn đặt hàng chưa xử lý</h3>
                        <p class="card-title mb-2">Số lượng: <?php echo $quantity_orders ?> </p>
                        <a href="order_list.php" class="btn btn-sm btn-outline-primary w-100 w-sm-auto">Xem chi tiết</a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- / Content -->
    <!-- Footer -->

    <!-- Content wrapper -->
    <?php
    include($_SERVER["DOCUMENT_ROOT"] . '/admin/inc/footer.php');
    ?>
    <!-- Revenue Chart Data -->
    <script>
      // Truyền dữ liệu từ PHP sang JavaScript
      window.revenueChartData = <?php echo $chart_data_json; ?>;
      window.revenueChartLabels = <?php echo $chart_labels_json; ?>;

      // Dữ liệu biểu đồ tròn
      window.orderStatusChartData = <?php echo $pie_chart_data_json; ?>;
      window.orderStatusChartLabels = <?php echo $pie_chart_labels_json; ?>;
      window.orderStatusChartColors = <?php echo $pie_chart_colors_json; ?>;

      // Debug: Log dữ liệu
      console.log('Order Status Chart Data from PHP:', {
        data: window.orderStatusChartData,
        labels: window.orderStatusChartLabels,
        colors: window.orderStatusChartColors
      });
    </script>
    <!-- Revenue Chart JS -->
    <script src="../assets/js/dashboard-revenue.js"></script>
    <!-- Order Status Chart JS - Inline để đảm bảo load -->
    <script>
      (function() {
        'use strict';

        function initOrderStatusChart() {
          // Kiểm tra ApexCharts
          if (typeof ApexCharts === 'undefined') {
            console.log('Waiting for ApexCharts...');
            setTimeout(initOrderStatusChart, 100);
            return;
          }

          // Kiểm tra element
          const orderStatusChartEl = document.querySelector('#orderStatusChart');
          if (!orderStatusChartEl) {
            console.error('Order Status Chart: Element not found');
            return;
          }

          // Lấy dữ liệu
          const chartData = window.orderStatusChartData || [];
          const chartLabels = window.orderStatusChartLabels || [];
          const chartColors = window.orderStatusChartColors || [];

          console.log('Order Status Chart - Initializing with data:', {
            data: chartData,
            labels: chartLabels,
            colors: chartColors,
            dataLength: chartData.length
          });

          // Kiểm tra dữ liệu
          if (!chartData || chartData.length === 0) {
            console.warn('Order Status Chart: No data');
            const loadingEl = document.getElementById('orderStatusChartLoading');
            if (loadingEl) {
              loadingEl.innerHTML = '<p class="text-muted">Chưa có dữ liệu đơn hàng</p>';
            }
            return;
          }

          // Tính tổng
          const total = chartData.reduce((sum, val) => sum + val, 0);
          if (total === 0) {
            console.warn('Order Status Chart: Total is 0');
            const loadingEl = document.getElementById('orderStatusChartLoading');
            if (loadingEl) {
              loadingEl.innerHTML = '<p class="text-muted">Chưa có dữ liệu đơn hàng</p>';
            }
            return;
          }

          // Xóa loading
          const loadingEl = document.getElementById('orderStatusChartLoading');
          if (loadingEl) {
            loadingEl.remove();
          }

          // Lấy màu từ config nếu có
          let cardColor = '#fff';
          let headingColor = '#5d596c';
          let axisColor = '#a1acb8';

          if (typeof config !== 'undefined') {
            cardColor = config.colors.white;
            headingColor = config.colors.headingColor;
            axisColor = config.colors.axisColor;
          }

          // Tạo biểu đồ
          const chartOptions = {
            series: chartData,
            chart: {
              height: 300,
              type: 'donut',
              toolbar: {
                show: false
              }
            },
            labels: chartLabels,
            colors: chartColors.length > 0 ? chartColors : ['#696cff', '#8592a3', '#03c3ec', '#71dd37', '#ffab00', '#ff3e1d'],
            stroke: {
              width: 5,
              colors: [cardColor]
            },
            dataLabels: {
              enabled: true,
              formatter: function(val, opts) {
                // Chỉ hiển thị số, không hiển thị label
                return opts.w.globals.series[opts.seriesIndex];
              },
              style: {
                fontSize: '12px',
                fontWeight: 600
              }
            },
            legend: {
              show: true,
              position: 'bottom',
              horizontalAlign: 'center',
              fontSize: '13px',
              fontFamily: 'Public Sans',
              fontWeight: 400,
              labels: {
                colors: axisColor
              },
              markers: {
                width: 8,
                height: 8,
                radius: 12,
                offsetX: -3
              },
              itemMargin: {
                horizontal: 10,
                vertical: 5
              }
            },
            plotOptions: {
              pie: {
                donut: {
                  size: '65%',
                  labels: {
                    show: true,
                    name: {
                      show: false
                    },
                    value: {
                      show: true,
                      fontSize: '20px',
                      fontFamily: 'Public Sans',
                      fontWeight: 600,
                      color: headingColor,
                      formatter: function(val) {
                        return val;
                      }
                    },
                    total: {
                      show: true,
                      label: 'Tổng đơn hàng',
                      fontSize: '13px',
                      fontFamily: 'Public Sans',
                      fontWeight: 500,
                      color: axisColor,
                      formatter: function() {
                        return total;
                      }
                    }
                  }
                }
              }
            },
            tooltip: {
              enabled: true,
              fillSeriesColor: true,
              y: {
                formatter: function(val, opts) {
                  const label = opts.w.globals.labels[opts.seriesIndex];
                  const percentage = total > 0 ? ((val / total) * 100).toFixed(1) : 0;
                  return '<div style="padding: 4px 0;">' +
                    '<div style="font-weight: 600; margin-bottom: 4px;">' + label + '</div>' +
                    '<div>Số lượng: <strong>' + val + '</strong> đơn hàng</div>' +
                    '<div>Phần trăm: <strong>' + percentage + '%</strong></div>' +
                    '</div>';
                }
              },
              style: {
                fontSize: '13px',
                fontFamily: 'Public Sans'
              },
              theme: 'light'
            },
            states: {
              hover: {
                filter: {
                  type: 'none'
                }
              },
              active: {
                filter: {
                  type: 'none'
                }
              }
            }
          };

          try {
            const chart = new ApexCharts(orderStatusChartEl, chartOptions);
            chart.render();
            console.log('Order Status Chart rendered successfully');

            // Hàm hiển thị modal với thông tin chi tiết
            function showDetailModal(label, value, percentage, color) {
              // Tạo modal HTML
              const modalHTML = `
                 <div class="modal fade" id="orderStatusDetailModal" tabindex="-1" aria-labelledby="orderStatusDetailModalLabel" aria-hidden="true">
                   <div class="modal-dialog modal-dialog-centered">
                     <div class="modal-content">
                       <div class="modal-header" style="background-color: ${color}; color: white;">
                         <h5 class="modal-title" id="orderStatusDetailModalLabel">${label}</h5>
                         <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                       </div>
                       <div class="modal-body">
                         <div class="text-center">
                           <div class="mb-3">
                             <h2 style="color: ${color}; font-size: 3rem; font-weight: bold;">${value}</h2>
                             <p class="text-muted mb-0">đơn hàng</p>
                           </div>
                           <div class="mb-3">
                             <h3 style="color: ${color}; font-size: 2rem; font-weight: bold;">${percentage}%</h3>
                             <p class="text-muted mb-0">tổng số đơn hàng</p>
                           </div>
                           <div class="mt-4">
                             <p class="text-muted">Tổng số đơn hàng: <strong>${total}</strong></p>
                           </div>
                         </div>
                       </div>
                       <div class="modal-footer">
                         <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                       </div>
                     </div>
                   </div>
                 </div>
               `;

              // Xóa modal cũ nếu có
              const oldModal = document.getElementById('orderStatusDetailModal');
              if (oldModal) {
                oldModal.remove();
              }

              // Thêm modal mới vào body
              document.body.insertAdjacentHTML('beforeend', modalHTML);

              // Hiển thị modal
              const modal = new bootstrap.Modal(document.getElementById('orderStatusDetailModal'));
              modal.show();

              // Xóa modal khi đóng
              document.getElementById('orderStatusDetailModal').addEventListener('hidden.bs.modal', function() {
                this.remove();
              });
            }

            // Thêm event listener để hiển thị phần trăm khi click vào phần biểu đồ
            chart.addEventListener('dataPointSelection', function(e, chartContext, config) {
              const dataPointIndex = config.dataPointIndex;
              const value = chartData[dataPointIndex];
              const label = chartLabels[dataPointIndex];
              const color = chartColors[dataPointIndex] || '#696cff';
              const percentage = total > 0 ? ((value / total) * 100).toFixed(2) : 0;

              showDetailModal(label, value, percentage, color);
            });

            // Thêm event listener cho legend click sau khi chart render xong
            setTimeout(function() {
              const legendItems = document.querySelectorAll('.apexcharts-legend-series');
              legendItems.forEach((item, index) => {
                item.style.cursor = 'pointer';
                item.addEventListener('click', function(e) {
                  e.stopPropagation();
                  const value = chartData[index];
                  const label = chartLabels[index];
                  const color = chartColors[index] || '#696cff';
                  const percentage = total > 0 ? ((value / total) * 100).toFixed(2) : 0;

                  showDetailModal(label, value, percentage, color);
                });
              });
            }, 1000);

          } catch (error) {
            console.error('Error rendering chart:', error);
            orderStatusChartEl.innerHTML = '<div class="text-center p-4"><p class="text-danger">Lỗi: ' + error.message + '</p></div>';
          }
        }

        // Khởi tạo
        if (document.readyState === 'loading') {
          document.addEventListener('DOMContentLoaded', function() {
            setTimeout(initOrderStatusChart, 500);
          });
        } else {
          setTimeout(initOrderStatusChart, 500);
        }
      })();
    </script>