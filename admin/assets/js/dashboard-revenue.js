/**
 * Dashboard Revenue Chart
 */

'use strict';

(function () {
  // Đợi DOM và ApexCharts load xong
  function initRevenueChart() {
    // Kiểm tra ApexCharts đã load chưa
    if (typeof ApexCharts === 'undefined') {
      setTimeout(initRevenueChart, 100);
      return;
    }

    // Lấy dữ liệu từ PHP (được in ra trong HTML)
    const revenueChartEl = document.querySelector('#revenueChart');
    
    if (typeof revenueChartEl !== 'undefined' && revenueChartEl !== null) {
    // Lấy dữ liệu từ data attributes hoặc từ biến global
    const chartData = window.revenueChartData || [];
    const chartLabels = window.revenueChartLabels || [];
    
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

    const revenueChartOptions = {
      series: [
        {
          name: 'Doanh thu',
          data: chartData
        }
      ],
      chart: {
        height: 300,
        type: 'area',
        toolbar: {
          show: false
        },
        parentHeightOffset: 0,
        parentWidthOffset: 0
      },
      dataLabels: {
        enabled: false
      },
      stroke: {
        width: 2,
        curve: 'smooth'
      },
      legend: {
        show: false
      },
      markers: {
        size: 6,
        colors: 'transparent',
        strokeColors: 'transparent',
        strokeWidth: 4,
        discrete: [
          {
            fillColor: cardColor,
            seriesIndex: 0,
            dataPointIndex: chartData.length - 1,
            strokeColor: typeof config !== 'undefined' ? config.colors.primary : '#696cff',
            strokeWidth: 2,
            size: 6,
            radius: 8
          }
        ],
        hover: {
          size: 7
        }
      },
      colors: [typeof config !== 'undefined' ? config.colors.primary : '#696cff'],
      fill: {
        type: 'gradient',
        gradient: {
          shade: 'light',
          shadeIntensity: 0.6,
          opacityFrom: 0.5,
          opacityTo: 0.25,
          stops: [0, 95, 100]
        }
      },
      grid: {
        borderColor: borderColor,
        strokeDashArray: 3,
        padding: {
          top: -20,
          bottom: -8,
          left: -10,
          right: 8
        }
      },
      xaxis: {
        categories: chartLabels,
        axisBorder: {
          show: false
        },
        axisTicks: {
          show: false
        },
        labels: {
          show: true,
          style: {
            fontSize: '13px',
            colors: axisColor
          }
        }
      },
      yaxis: {
        labels: {
          show: true,
          style: {
            fontSize: '13px',
            colors: axisColor
          },
          formatter: function (val) {
            return new Intl.NumberFormat('vi-VN').format(val) + ' đ';
          }
        }
      },
      tooltip: {
        y: {
          formatter: function (val) {
            return new Intl.NumberFormat('vi-VN').format(val) + ' đ';
          }
        }
      }
    };

      const revenueChart = new ApexCharts(revenueChartEl, revenueChartOptions);
      revenueChart.render();
    }
  }

  // Khởi tạo khi DOM ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initRevenueChart);
  } else {
    initRevenueChart();
  }
})();

