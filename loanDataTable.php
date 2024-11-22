<?php

namespace App\DataTables;

use App\Models\Loan;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class LoanDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('client_id', function (Loan $loan) {
                return ucwords($loan->client_id);
            })
            ->editColumn('num_of_payment', function (Loan $loan) {
                return ucwords($loan->num_of_payment);
            })
            ->editColumn('first_payment_date', function (Loan $loan) {
                return ($loan->first_payment_date);
            })
            ->editColumn('last_payment_date', function (Loan $loan) {
                return ($loan->last_payment_date);
            })
            ->editColumn('loan_amount', function (Loan $loan) {
                return ($loan->loan_amount);
            });
    }


    /**
     * Get the query source of dataTable.
     */
    public function query(Loan $model): QueryBuilder
    {
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('loan-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('rt' . "<'row'<'col-sm-12'tr>><'d-flex justify-content-between'<'col-sm-12 col-md-5'i><'d-flex justify-content-between'p>>",)
            ->addTableClass('table align-middle table-row-dashed fs-6 gy-5 dataTable no-footer text-gray-600 fw-semibold')
            ->setTableHeadClass('text-start text-muted fw-bold fs-7 text-uppercase gs-0')
            ->orderBy(2)
            ->drawCallback("function() {" . file_get_contents(resource_path('views/pages/apps/user-management/users/columns/_draw-scripts.js')) . "}");
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('client_id')->addClass('d-flex align-items-center')->name('Client Id'),
            Column::make('num_of_payment')->title('EMI MONTH')->addClass('text-nowrap'),
            Column::make('first_payment_date')->title('First payment')->addClass('text-nowrap'),
            Column::make('last_payment_date')->title('Last Payment')->addClass('text-nowrap'),
            Column::make('loan_amount')->title('Loan AMount')->addClass('text-nowrap')
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Invoice_' . date('YmdHis');
    }
}
