<?php

namespace App\DataTables;

use App\Models\Settlement;
use App\Enums\SettlementType;
use App\Enums\SettlementStatus;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class SettlementDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('settlement_number', function (Settlement $settlement) {
                return '<span class="fw-bold text-primary">' . $settlement->settlement_number . '</span>';
            })
            ->editColumn('resource_type', function (Settlement $settlement) {
                $badgeClass = match($settlement->resource_type) {
                    'guide' => 'bg-info',
                    'representative' => 'bg-success',
                    'hotel' => 'bg-warning',
                    'vehicle' => 'bg-secondary',
                    'dahabia' => 'bg-primary',
                    'restaurant' => 'bg-danger',
                    'ticket' => 'bg-dark',
                    'extra' => 'bg-light text-dark',
                    default => 'bg-secondary'
                };
                return '<span class="badge ' . $badgeClass . '">' . ucfirst($settlement->resource_type) . '</span>';
            })
            ->editColumn('settlement_type', function (Settlement $settlement) {
                $badgeClass = match($settlement->settlement_type) {
                    SettlementType::MONTHLY => 'bg-primary',
                    SettlementType::WEEKLY => 'bg-info',
                    SettlementType::QUARTERLY => 'bg-warning',
                    SettlementType::YEARLY => 'bg-success',
                    SettlementType::CUSTOM => 'bg-secondary',
                    default => 'bg-light text-dark'
                };
                return '<span class="badge ' . $badgeClass . '">' . ucfirst($settlement->settlement_type->value) . '</span>';
            })
            ->editColumn('total_amount', function (Settlement $settlement) {
                return $settlement->total_amount ? $settlement->currency . ' ' . number_format($settlement->total_amount, 2) : '-';
            })
            ->editColumn('status', function (Settlement $settlement) {
                $badgeClass = match($settlement->status) {
                    SettlementStatus::PENDING => 'bg-warning',
                    SettlementStatus::CALCULATED => 'bg-info',
                    SettlementStatus::APPROVED => 'bg-success',
                    SettlementStatus::REJECTED => 'bg-danger',
                    SettlementStatus::PAID => 'bg-primary',
                    default => 'bg-secondary'
                };
                return '<span class="badge ' . $badgeClass . '">' . ucfirst($settlement->status->value) . '</span>';
            })
            ->editColumn('created_at', function (Settlement $settlement) {
                return $settlement->created_at ? $settlement->created_at->format('M d, Y H:i') : '-';
            })
            ->addColumn('action', function (Settlement $settlement) {
                return view('dashboard.settlements.action', [
                    'id' => $settlement->id,
                    'status' => $settlement->status
                ])->render();
            })
            ->setRowId('id')
            ->rawColumns(['settlement_number', 'resource_type', 'settlement_type', 'status', 'action']);
    }

    public function query(Settlement $model): QueryBuilder
    {
        return $model->newQuery();
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('data-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('Blfrtip')
            ->orderBy(0)
            ->selectStyleSingle()
            ->buttons(array_reverse([
                Button::make('excel')->className('btn btn-sm float-right ms-1 p-1 text-light btn-success'),
                Button::make('csv')->className('btn btn-sm float-right ms-1 p-1 text-light btn-primary'),
                Button::make('print')->className('btn btn-sm float-right ms-1 p-1 text-light btn-secondary'),
                Button::make('reload')->className('btn btn-sm float-right ms-1 p-1 text-light btn-info')
            ]));
    }

    public function getColumns(): array
    {
        return [
            Column::make('id'),
            Column::make('settlement_number'),
            Column::make('resource_type'),
            Column::make('settlement_type'),
            Column::make('total_amount')->title('Amount'),
            Column::make('status'),
            Column::make('created_at'),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(120)
                ->addClass('text-center'),
        ];
    }

    protected function filename(): string
    {
        return 'Settlement_' . date('YmdHis');
    }
}