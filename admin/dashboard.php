<?php
    include_once "sidebar.php";
    require_once "connect.php"; // Đảm bảo connect.php đã được include

    // --- Lấy dữ liệu cho các thống kê chung (giữ nguyên hoặc cập nhật) ---
    // Ví dụ: Tổng số khách hàng
    $sql_total_customers = "SELECT COUNT(id_khach) AS total_customers FROM khach_hang";
    $query_total_customers = mysqli_query($conn, $sql_total_customers);
    $total_customers = mysqli_fetch_assoc($query_total_customers)['total_customers'] ?? 0;

    // Ví dụ: Tổng số đơn hàng
    $sql_total_orders = "SELECT COUNT(id_donhang) AS total_orders FROM don_hang";
    $query_total_orders = mysqli_query($conn, $sql_total_orders);
    $total_orders = mysqli_fetch_assoc($query_total_orders)['total_orders'] ?? 0;

    // Ví dụ: Tổng doanh thu (chỉ đơn hàng đã hoàn thành)
    $sql_total_revenue = "SELECT SUM(tong_tien) AS total_revenue FROM don_hang WHERE trang_thai = 3"; // trang_thai = 3 là đã hoàn thành
    $query_total_revenue = mysqli_query($conn, $sql_total_revenue);
    $total_revenue = mysqli_fetch_assoc($query_total_revenue)['total_revenue'] ?? 0;

    // --- Lấy dữ liệu cho Sơ đồ sản phẩm ---
    // Sản phẩm bán chạy nhất
    $sql_top_selling_products = "SELECT sp.ten_sp, SUM(dhct.soluong) AS total_sold_quantity
                                 FROM donhang_chitiet dhct
                                 INNER JOIN sanpham_chitiet spct ON dhct.id_spchitiet = spct.id_spchitiet
                                 INNER JOIN san_pham sp ON spct.id_sp = sp.id_sp
                                 GROUP BY sp.id_sp, sp.ten_sp
                                 ORDER BY total_sold_quantity DESC
                                 LIMIT 5"; // Lấy 5 sản phẩm bán chạy nhất
    $query_top_selling = mysqli_query($conn, $sql_top_selling_products);
    $top_selling_products = mysqli_fetch_all($query_top_selling, MYSQLI_ASSOC);

    // Sản phẩm bán ít nhất (hoặc chưa bán được)
    // Bao gồm tất cả sản phẩm đang hiện, và đếm số lượng bán.
    // Những sản phẩm chưa bán sẽ có total_sold_quantity là 0
    $sql_least_selling_products = "SELECT sp.ten_sp, IFNULL(SUM(dhct.soluong), 0) AS total_sold_quantity
                                   FROM san_pham sp
                                   LEFT JOIN sanpham_chitiet spct ON sp.id_sp = spct.id_sp
                                   LEFT JOIN donhang_chitiet dhct ON spct.id_spchitiet = dhct.id_spchitiet
                                   WHERE sp.trangthai = 0 -- Chỉ lấy sản phẩm đang hiện
                                   GROUP BY sp.id_sp, sp.ten_sp
                                   ORDER BY total_sold_quantity ASC, sp.ten_sp ASC
                                   LIMIT 5"; // Lấy 5 sản phẩm bán ít nhất
    $query_least_selling = mysqli_query($conn, $sql_least_selling_products);
    $least_selling_products = mysqli_fetch_all($query_least_selling, MYSQLI_ASSOC);


    // Dữ liệu cho biểu đồ (có thể lấy từ top_selling_products)
    $chart_labels = [];
    $chart_data = [];
    foreach ($top_selling_products as $product) {
        $chart_labels[] = htmlspecialchars($product['ten_sp']);
        $chart_data[] = $product['total_sold_quantity'];
    }

?>
<div class="main-content">
        <div class="page-title">
          <div class="title">Dashboard</div>
          <div class="action-buttons">
            <button class="btn btn-outline">
              <i class="fas fa-download"></i>
              Export
            </button>
            <button class="btn btn-primary">
              <i class="fas fa-plus"></i>
              Add New
            </button>
          </div>
        </div>

        <div class="stats-cards">
          <div class="stat-card">
            <div class="card-header">
              <div>
                <div class="card-value"><?php echo number_format($total_customers); ?></div>
                <div class="card-label">Tổng khách hàng</div>
              </div>
              <div class="card-icon purple">
                <i class="fas fa-users"></i>
              </div>
            </div>
            <div class="card-change positive">
              <i class="fas fa-arrow-up"></i>
              <span>12.5% from last month</span> </div>
          </div>

          <div class="stat-card">
            <div class="card-header">
              <div>
                <div class="card-value"><?php echo number_format($total_revenue); ?> VNĐ</div>
                <div class="card-label">Tổng doanh thu</div>
              </div>
              <div class="card-icon blue">
                <i class="fas fa-dollar-sign"></i>
              </div>
            </div>
            <div class="card-change positive">
              <i class="fas fa-arrow-up"></i>
              <span>8.2% from last month</span> </div>
          </div>

          <div class="stat-card">
            <div class="card-header">
              <div>
                <div class="card-value"><?php echo number_format($total_orders); ?></div>
                <div class="card-label">Tổng đơn hàng</div>
              </div>
              <div class="card-icon green">
                <i class="fas fa-shopping-cart"></i>
              </div>
            </div>
            <div class="card-change negative">
              <i class="fas fa-arrow-down"></i>
              <span>3.1% from last month</span> </div>
          </div>

          <div class="stat-card">
            <div class="card-header">
              <div>
                <div class="card-value">85%</div>
                <div class="card-label">Tỷ lệ chuyển đổi</div>
              </div>
              <div class="card-icon orange">
                <i class="fas fa-chart-line"></i>
              </div>
            </div>
            <div class="card-change positive">
              <i class="fas fa-arrow-up"></i>
              <span>4.6% from last month</span> </div>
          </div>
        </div>

        <div class="table-card" style="margin-bottom: 30px;">
            <div class="card-title">
                <h3><i class="fas fa-chart-bar"></i> Sản phẩm bán chạy nhất</h3>
            </div>
            <div style="width: 100%; max-width: 800px; margin: auto;">
                <canvas id="topSellingChart"></canvas>
            </div>
        </div>

        <div class="table-card">
            <div class="card-title">
                <h3><i class="fas fa-arrow-down"></i> Sản phẩm bán ít nhất / Tồn kho</h3>
                <button class="btn btn-outline btn-sm">
                    <i class="fas fa-eye"></i> Xem tất cả
                </button>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Tên sản phẩm</th>
                        <th>Số lượng bán</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($least_selling_products)): ?>
                        <?php foreach($least_selling_products as $product): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($product['ten_sp']); ?></td>
                                <td><?php echo $product['total_sold_quantity']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="2" class="text-center">Không có dữ liệu sản phẩm bán ít nhất.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="table-card">
          <div class="card-title">
            <h3><i class="fas fa-shopping-bag"></i> Recent Orders</h3>
            <button class="btn btn-outline btn-sm">
              <i class="fas fa-eye"></i> View All
            </button>
          </div>
          <table class="data-table">
            <thead>
              <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Date</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>#ORD-001</td>
                <td>John Smith</td>
                <td>15 Mar 2025</td>
                <td>$125.00</td>
                <td>
                  <span class="status active"
                    ><i class="fas fa-check-circle"></i> Completed</span
                  >
                </td>
                <td>
                  <button class="btn btn-outline btn-sm">
                    <i class="fas fa-eye"></i> View
                  </button>
                </td>
              </tr>
              <tr>
                <td>#ORD-002</td>
                <td>Emma Johnson</td>
                <td>14 Mar 2025</td>
                <td>$245.99</td>
                <td>
                  <span class="status pending"
                    ><i class="fas fa-clock"></i> Pending</span
                  >
                </td>
                <td>
                  <button class="btn btn-outline btn-sm">
                    <i class="fas fa-eye"></i> View
                  </button>
                </td>
              </tr>
              <tr>
                <td>#ORD-003</td>
                <td>Michael Brown</td>
                <td>13 Mar 2025</td>
                <td>$79.50</td>
                <td>
                  <span class="status active"
                    ><i class="fas fa-check-circle"></i> Completed</span
                  >
                </td>
                <td>
                  <button class="btn btn-outline btn-sm">
                    <i class="fas fa-eye"></i> View
                  </button>
                </td>
              </tr>
              <tr>
                <td>#ORD-004</td>
                <td>Sarah Davis</td>
                <td>12 Mar 2025</td>
                <td>$350.00</td>
                <td>
                  <span class="status cancelled"
                    ><i class="fas fa-times-circle"></i> Cancelled</span
                  >
                </td>
                <td>
                  <button class="btn btn-outline btn-sm">
                    <i class="fas fa-eye"></i> View
                  </button>
                </td>
              </tr>
              <tr>
                <td>#ORD-005</td>
                <td>David Wilson</td>
                <td>11 Mar 2025</td>
                <td>$185.25</td>
                <td>
                  <span class="status active"
                    ><i class="fas fa-check-circle"></i> Completed</span
                  >
                </td>
                <td>
                  <button class="btn btn-outline btn-sm">
                    <i class="fas fa-eye"></i> View
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </body>
</html>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('topSellingChart').getContext('2d');
        const topSellingChart = new Chart(ctx, {
            type: 'bar', // Loại biểu đồ: cột
            data: {
                labels: <?php echo json_encode($chart_labels); ?>, // Tên sản phẩm
                datasets: [{
                    label: 'Số lượng đã bán',
                    data: <?php echo json_encode($chart_data); ?>, // Số lượng bán
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.6)', // Blue
                        'rgba(255, 99, 132, 0.6)', // Red
                        'rgba(75, 192, 192, 0.6)', // Green
                        'rgba(255, 206, 86, 0.6)', // Yellow
                        'rgba(153, 102, 255, 0.6)'  // Purple
                    ],
                    borderColor: [
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 99, 132, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(153, 102, 255, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false // Không hiển thị chú giải
                    },
                    title: {
                        display: true,
                        text: 'Top 5 Sản phẩm bán chạy nhất'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Số lượng'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Tên sản phẩm'
                        }
                    }
                }
            }
        });
    });
</script>