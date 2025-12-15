/**
 * Dashboard Order Status Pie Chart
 */

'use strict';

(function () {
  // Đợi DOM và ApexCharts load xong
  function initOrderStatusChart() {
    // Kiểm tra ApexCharts đã load chưa
    if (typeof ApexCharts === 'undefined') {
      setTimeout(initOrderStatusChart, 100);
      return;
    }

    // Lấy dữ liệu từ PHP (được in ra trong HTML)
    const orderStatusChartEl = document.querySelector('#orderStatusChart');
    
    // Debug: Kiểm tra element có tồn tại không
    if (!orderStatusChartEl) {
      console.warn('Order Status Chart: Element #orderStatusChart not found');
      return;
    }
    
    if (typeof orderStatusChartEl !== 'undefined' && orderStatusChartEl !== null) {
      // Lấy dữ liệu từ biến global
      const chartData = window.orderStatusChartData || [];
      const chartLabels = window.orderStatusChartLabels || [];
      const chartColors = window.orderStatusChartColors || [];
      
      // Debug: Log dữ liệu
      console.log('Order Status Chart Data:', {
        data: chartData,
        labels: chartLabels,
        colors: chartColors
      });
      
      // Kiểm tra dữ liệu có hợp lệ không
      if (!chartData || chartData.length === 0 || !Array.isArray(chartData)) {
        console.warn('Order Status Chart: No data available', {
          chartData: chartData,
          chartLabels: chartLabels
        });
        // Hiển thị thông báo nếu không có dữ liệu
        orderStatusChartEl.innerHTML = '<div class="text-center p-4"><p class="text-muted">Chưa có dữ liệu đơn hàng</p></div>';
        return;
      }
      
      let cardColor, headingColor, axisColor, borderColor;
      
      if (typeof config !== 'undefined') {
        cardColor = config.colors.white;
        headingColor = config.colors.headingColor;
        axisColor = config.colors.axisColor;
        borderColor = config.colors.borderColor;
      } else {
        cardColor = '#fff';
        headingColor = '#5d596c';
        axisColor = '#a1acb8';
        borderColor = '#d8d6de';
      }

      // Tính tổng để hiển thị phần trăm
      const total = chartData.reduce((sum, val) => sum + val, 0);
      
      // Nếu tổng = 0, hiển thị thông báo
      if (total === 0) {
        console.warn('Order Status Chart: Total is 0');
        orderStatusChartEl.innerHTML = '<div class="text-center p-4"><p class="text-muted">Chưa có dữ liệu đơn hàng</p></div>';
        return;
      }

      const orderStatusChartOptions = {
        series: chartData,
        chart: {
          height: 300,
          type: 'donut',
          toolbar: {
            show: false
          }
        },
        labels: chartLabels,
        colors: chartColors.length > 0 ? chartColors : [
          typeof config !== 'undefined' ? config.colors.primary : '#696cff',
          typeof config !== 'undefined' ? config.colors.secondary : '#8592a3',
          typeof config !== 'undefined' ? config.colors.info : '#03c3ec',
          typeof config !== 'undefined' ? config.colors.success : '#71dd37',
          typeof config !== 'undefined' ? config.colors.warning : '#ffab00',
          typeof config !== 'undefined' ? config.colors.danger : '#ff3e1d'
        ],
        stroke: {
          width: 5,
          colors: cardColor
        },
        dataLabels: {
          enabled: true,
          formatter: function (val, opts) {
            const label = opts.w.globals.labels[opts.seriesIndex];
            const value = opts.w.globals.series[opts.seriesIndex];
            return label + ': ' + value;
          },
          style: {
            fontSize: '12px',
            fontWeight: 500,
            colors: [headingColor]
          },
          dropShadow: {
            enabled: false
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
                  show: true,
                  fontSize: '15px',
                  fontFamily: 'Public Sans',
                  fontWeight: 500,
                  color: headingColor,
                  offsetY: -10
                },
                value: {
                  show: true,
                  fontSize: '20px',
                  fontFamily: 'Public Sans',
                  fontWeight: 600,
                  color: headingColor,
                  offsetY: 16,
                  formatter: function (val) {
                    return val;
                  }
                },
                total: {
                  show: true,
                  showAlways: true,
                  label: 'Tổng đơn hàng',
                  fontSize: '13px',
                  fontFamily: 'Public Sans',
                  fontWeight: 500,
                  color: axisColor,
                  formatter: function () {
                    return total;
                  }
                }
              }
            }
          }
        },
        grid: {
          padding: {
            top: 0,
            bottom: 0,
            right: 0
          }
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
        },
        tooltip: {
          y: {
            formatter: function (val, opts) {
              const label = opts.w.globals.labels[opts.seriesIndex];
              const percentage = ((val / total) * 100).toFixed(1);
              return label + ': ' + val + ' đơn (' + percentage + '%)';
            }
          }
        }
      };

      try {
        const orderStatusChart = new ApexCharts(orderStatusChartEl, orderStatusChartOptions);
        orderStatusChart.render();
        console.log('Order Status Chart rendered successfully');
      } catch (error) {
        console.error('Error rendering Order Status Chart:', error);
        orderStatusChartEl.innerHTML = '<div class="text-center p-4"><p class="text-danger">Lỗi khi tải biểu đồ</p></div>';
      }
    }
  }

  // Đợi một chút để đảm bảo dữ liệu đã được set
  function waitForData() {
    if (typeof window.orderStatusChartData !== 'undefined') {
      initOrderStatusChart();
    } else {
      setTimeout(waitForData, 50);
    }
  }

  // Khởi tạo khi DOM ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', waitForData);
  } else {
    waitForData();
  }
})();

