<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SalesReportExport;

/**
 * Report Controller
 * 
 * Admin reporting dashboard and exports
 */
class ReportController extends Controller
{
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Dashboard Overview
     */
    public function index(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

        $stats = $this->reportService->getDashboardStats($startDate, $endDate);
        $salesReport = $this->reportService->getSalesReport($startDate, $endDate, 'day');
        $topProducts = $this->reportService->getTopSellingProducts($startDate, $endDate, 10);
        $paymentMethods = $this->reportService->getPaymentMethodReport($startDate, $endDate);
        $orderStatuses = $this->reportService->getOrderStatusReport($startDate, $endDate);

        return view('admin.reports.index', compact(
            'stats',
            'salesReport',
            'topProducts',
            'paymentMethods',
            'orderStatuses',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Sales Report
     */
    public function sales(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        $groupBy = $request->get('group_by', 'day');

        $salesReport = $this->reportService->getSalesReport($startDate, $endDate, $groupBy);

        return view('admin.reports.sales', compact('salesReport', 'startDate', 'endDate', 'groupBy'));
    }

    /**
     * Product Performance Report
     */
    public function products(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

        $topProducts = $this->reportService->getTopSellingProducts($startDate, $endDate, 50);
        $productPerformance = $this->reportService->getProductPerformanceReport($startDate, $endDate);
        $categoryPerformance = $this->reportService->getCategoryPerformanceReport($startDate, $endDate);

        return view('admin.reports.products', compact(
            'topProducts',
            'productPerformance',
            'categoryPerformance',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Inventory Report
     */
    public function inventory(Request $request)
    {
        $inventory = $this->reportService->getInventoryReport();
        $lowStock = $this->reportService->getLowStockProducts(10);
        $outOfStock = $this->reportService->getOutOfStockProducts();

        return view('admin.reports.inventory', compact('inventory', 'lowStock', 'outOfStock'));
    }

    /**
     * Customer Report
     */
    public function customers(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfYear()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

        $customers = $this->reportService->getCustomerReport($startDate, $endDate);

        return view('admin.reports.customers', compact('customers', 'startDate', 'endDate'));
    }

    /**
     * Delivery Zone Report
     */
    public function delivery(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

        $deliveryZones = $this->reportService->getDeliveryZoneReport($startDate, $endDate);

        return view('admin.reports.delivery', compact('deliveryZones', 'startDate', 'endDate'));
    }

    /**
     * Export Sales Report to PDF
     */
    public function exportSalesPdf(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        $groupBy = $request->get('group_by', 'day');

        $salesReport = $this->reportService->getSalesReport($startDate, $endDate, $groupBy);

        $pdf = Pdf::loadView('admin.reports.exports.sales-pdf', compact('salesReport', 'startDate', 'endDate'));
        
        return $pdf->download('sales-report-' . $startDate . '-to-' . $endDate . '.pdf');
    }

    /**
     * Export Inventory Report to PDF
     */
    public function exportInventoryPdf()
    {
        $inventory = $this->reportService->getInventoryReport();
        $lowStock = $this->reportService->getLowStockProducts(10);
        $outOfStock = $this->reportService->getOutOfStockProducts();

        $pdf = Pdf::loadView('admin.reports.exports.inventory-pdf', compact('inventory', 'lowStock', 'outOfStock'));
        
        return $pdf->download('inventory-report-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Export Products Report to PDF
     */
    public function exportProductsPdf(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

        $topProducts = $this->reportService->getTopSellingProducts($startDate, $endDate, 50);
        $categoryPerformance = $this->reportService->getCategoryPerformance($startDate, $endDate);

        $pdf = Pdf::loadView('admin.reports.exports.products-pdf', compact('topProducts', 'categoryPerformance', 'startDate', 'endDate'));
        
        return $pdf->download('products-report-' . $startDate . '-to-' . $endDate . '.pdf');
    }

    /**
     * Export Customers Report to PDF
     */
    public function exportCustomersPdf(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

        $customers = $this->reportService->getCustomerReport($startDate, $endDate);

        $pdf = Pdf::loadView('admin.reports.exports.customers-pdf', compact('customers', 'startDate', 'endDate'));
        
        return $pdf->download('customers-report-' . $startDate . '-to-' . $endDate . '.pdf');
    }

    /**
     * Export Delivery Report to PDF
     */
    public function exportDeliveryPdf(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

        $deliveryZones = $this->reportService->getDeliveryZoneReport($startDate, $endDate);

        $pdf = Pdf::loadView('admin.reports.exports.delivery-pdf', compact('deliveryZones', 'startDate', 'endDate'));
        
        return $pdf->download('delivery-report-' . $startDate . '-to-' . $endDate . '.pdf');
    }
}
