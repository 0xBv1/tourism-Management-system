<?php

namespace App\DataTables;

use App\Models\ServiceApproval;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ServiceApprovalDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('action', function ($approval) {
                return '<a href="' . route('dashboard.service-approvals.show', $approval->id) . '" class="btn btn-sm btn-info"><i class="fa fa-eye"></i></a>';
            })
            ->addColumn('status', function ($approval) {
                $color = match($approval->status) {
                    'pending' => 'warning',
                    'approved' => 'success',
                    'rejected' => 'danger',
                    default => 'secondary',
                };
                return '<span class="badge bg-' . $color . '">' . ucfirst($approval->status) . '</span>';
            })
            ->addColumn('supplier_name', function ($approval) {
                try {
                    $supplier = \App\Models\Supplier::find($approval->supplier_id);
                    return $supplier && $supplier->company_name ? $supplier->company_name : 'N/A';
                } catch (\Exception $e) {
                    return 'N/A';
                }
            })
            ->addColumn('supplier_email', function ($approval) {
                try {
                    $supplier = \App\Models\Supplier::find($approval->supplier_id);
                    if ($supplier && $supplier->user_id) {
                        $user = \App\Models\User::find($supplier->user_id);
                        return $user && $user->email ? $user->email : 'N/A';
                    }
                    return 'N/A';
                } catch (\Exception $e) {
                    return 'N/A';
                }
            })
            ->addColumn('service_name', function ($approval) {
                try {
                    return $this->getServiceName($approval);
                } catch (\Exception $e) {
                    return 'N/A';
                }
            })
            ->addColumn('approved_by', function ($approval) {
                try {
                    if ($approval->approved_by) {
                        $user = \App\Models\User::find($approval->approved_by);
                        return $user && $user->name ? $user->name : 'N/A';
                    }
                    return 'N/A';
                } catch (\Exception $e) {
                    return 'N/A';
                }
            })
            ->editColumn('created_at', function ($approval) {
                try {
                    return $approval->created_at ? $approval->created_at->format('M d, Y H:i') : 'N/A';
                } catch (\Exception $e) {
                    return 'N/A';
                }
            })
            ->editColumn('approved_at', function ($approval) {
                try {
                    return $approval->approved_at ? $approval->approved_at->format('M d, Y H:i') : 'N/A';
                } catch (\Exception $e) {
                    return 'N/A';
                }
            })
            ->editColumn('rejected_at', function ($approval) {
                try {
                    return $approval->rejected_at ? $approval->rejected_at->format('M d, Y H:i') : 'N/A';
                } catch (\Exception $e) {
                    return 'N/A';
                }
            })
            ->rawColumns(['action', 'status'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(ServiceApproval $model): QueryBuilder
    {
        $query = $model->newQuery();

        // Apply status filter
        if (request()->filled('status')) {
            $query->where('status', request('status'));
        }

        // Apply service type filter
        if (request()->filled('service_type')) {
            $query->where('service_type', request('service_type'));
        }

        // Apply supplier filter
        if (request()->filled('supplier_id')) {
            $query->where('supplier_id', request('supplier_id'));
        }

        return $query;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('service-approvals-table')
            ->columns($this->getColumns())
            ->minifiedAjax(request()->fullUrlWithQuery(['data' => '']))
            ->dom('Blfrtip')
            ->orderBy(0)
            ->selectStyleSingle()
            ->buttons(array_reverse([
                Button::make('excel')->className('btn btn-sm float-right ms-1 p-1 text-light btn-success'),
                Button::make('csv')->className('btn btn-sm float-right ms-1 p-1 text-light btn-primary'),
                Button::make('print')->className('btn btn-sm float-right ms-1 p-1 text-light btn-secondary'),
                Button::make('reload')->className('btn btn-sm float-right ms-1 p-1 text-light btn-info'),
            ]));
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('id')->title('ID')->width(60),
            Column::make('supplier_name')->title('Supplier')->width(150),
            Column::make('service_type')->title('Service Type')->width(100),
            Column::make('service_name')->title('Service Name')->width(200),
            Column::make('status')->title('Status')->width(100),
            Column::make('approved_by')->title('Approved By')->width(120),
            Column::make('created_at')->title('Submitted')->width(120),
            Column::make('approved_at')->title('Approved At')->width(120),
            Column::make('rejected_at')->title('Rejected At')->width(120),
            Column::computed('action')->title('Actions')->width(150)->exportable(false)->printable(false),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'ServiceApprovals_' . date('YmdHis');
    }

    /**
     * Get service name based on service type and ID.
     */
    private function getServiceName(ServiceApproval $approval): string
    {
        try {
            switch ($approval->service_type) {
                case 'hotel':
                    $service = \App\Models\SupplierHotel::find($approval->service_id);
                    return $service && $service->name ? $service->name : 'N/A';
                case 'tour':
                    $service = \App\Models\SupplierTour::find($approval->service_id);
                    return $service && $service->title ? $service->title : 'N/A';
                case 'trip':
                    $service = \App\Models\SupplierTrip::find($approval->service_id);
                    return $service && $service->name ? $service->name : 'N/A';
                case 'transport':
                    $service = \App\Models\SupplierTransport::find($approval->service_id);
                    return $service && $service->name ? $service->name : 'N/A';
                case 'room':
                    $service = \App\Models\SupplierRoom::find($approval->service_id);
                    return $service && $service->name ? $service->name : 'N/A';
                default:
                    return 'N/A';
            }
        } catch (\Exception $e) {
            return 'N/A';
        }
    }
}
