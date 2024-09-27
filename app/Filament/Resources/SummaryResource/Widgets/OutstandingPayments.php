<?php

namespace App\Filament\Resources\SummaryResource\Widgets;

use App\Models\Databaseinvoice;
use App\Models\Perusahaan;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OutstandingPayments extends BaseWidget
{
    protected function getStats(): array
    {
        // Fetch total invoices for the current year
        $total_invoices_year = Databaseinvoice::whereYear('tanggal_penerbitan_invoice', now()->year)->count();

        // Fetch total invoices for this month
        $total_invoices_this_month = Databaseinvoice::whereMonth('tanggal_penerbitan_invoice', now()->month)
            ->whereYear('tanggal_penerbitan_invoice', now()->year) // Ensure it's the current year
            ->count();

        // Fetch total invoices for last month
        $total_invoices_last_month = Databaseinvoice::whereMonth('tanggal_penerbitan_invoice', now()->subMonth()->month)
            ->whereYear('tanggal_penerbitan_invoice', now()->year) // Ensure it's the current year
            ->count();

        // Calculate percentage change for total invoices
        $percentage_change = 0;
        if ($total_invoices_last_month > 0) {
            $percentage_change = (($total_invoices_this_month - $total_invoices_last_month) / $total_invoices_last_month) * 100;
        }

        // Determine color based on increase or decrease
        $color_invoices = $percentage_change > 0 ? 'success' : ($percentage_change < 0 ? 'danger' : 'gray');

        // Outstanding payments statistics
        $total_outstanding_payments_year = Databaseinvoice::whereYear('tanggal_penerbitan_invoice', now()->year)
            ->whereNull('tanggal_pembayaran') // Outstanding payments for the current year
            ->count();

        $total_outstanding_payments_this_month = Databaseinvoice::whereMonth('tanggal_penerbitan_invoice', now()->month)
            ->whereYear('tanggal_penerbitan_invoice', now()->year) // Ensure it's the current year
            ->whereNull('tanggal_pembayaran') // Outstanding payments
            ->count();

        $total_outstanding_payments_last_month = Databaseinvoice::whereMonth('tanggal_penerbitan_invoice', now()->subMonth()->month)
            ->whereYear('tanggal_penerbitan_invoice', now()->year) // Ensure it's the current year
            ->whereNull('tanggal_pembayaran') // Outstanding payments
            ->count();

        // Calculate percentage change for outstanding payments
        $percentage_change_outstanding = 0;
        if ($total_outstanding_payments_last_month > 0) {
            $percentage_change_outstanding = (($total_outstanding_payments_this_month - $total_outstanding_payments_last_month) / $total_outstanding_payments_last_month) * 100;
        }

        // Determine color based on increase or decrease for outstanding payments
        $color_outstanding = $percentage_change_outstanding > 0 ? 'danger' : ($percentage_change_outstanding < 0 ? 'success' : 'gray');

        return [
            Stat::make('Total Invoices This Year', $total_invoices_year)
                ->description("This month: $total_invoices_this_month")
                ->descriptionIcon('heroicon-m-document-text')
                ->color('info'),

            Stat::make('Invoices This Month', $total_invoices_this_month)
                ->description($percentage_change !== 0 ? round(abs($percentage_change), 2) . '% ' . ($percentage_change > 0 ? 'increase' : 'decrease') : 'No change')
                ->descriptionIcon($percentage_change > 0 ? 'heroicon-m-arrow-trending-up' : ($percentage_change < 0 ? 'heroicon-m-arrow-trending-down' : 'heroicon-m-arrow-right'))
                ->color($color_invoices),

            // Update for outstanding payments this year
            Stat::make('Outstanding Payments This Year', $total_outstanding_payments_year)
                ->description("Outstanding this month: $total_outstanding_payments_this_month")
                ->descriptionIcon('heroicon-m-document-text')
                ->color('info'),

            // Compare outstanding payments this month with last month
            Stat::make('Outstanding Payments This Month', $total_outstanding_payments_this_month)
                ->description($percentage_change_outstanding !== 0 ? round(abs($percentage_change_outstanding), 2) . '% ' . ($percentage_change_outstanding > 0 ? 'increase' : 'decrease') : 'No change')
                ->descriptionIcon($percentage_change_outstanding > 0 ? 'heroicon-m-arrow-trending-up' : ($percentage_change_outstanding < 0 ? 'heroicon-m-arrow-trending-down' : 'heroicon-m-arrow-right'))
                ->color($color_outstanding),

        ];
    }
}
