<?php

namespace App\Http\Controllers\Apps;

use App\DataTables\LoanDataTable;
use App\Http\Controllers\Controller;
use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InvoiceManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(LoanDataTable $dataTable)
    {
        return $dataTable->render('pages/apps.‌invoice-management.loan.list');
     
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
          

        DB::statement('DROP TABLE IF EXISTS testedd');
        DB::statement('CREATE TABLE "emi_details" (
            "id" INT(8) NOT NULL AUTO_INCREMENT,
            "client_id" INT(8) NULL DEFAULT NULL,
            "mon" VARCHAR(50) NULL DEFAULT NULL,
            "amount" DECIMAL(23,0) NULL DEFAULT NULL,
            PRIMARY KEY ("id") USING BTREE
        )');
        
        $results = DB::select('SELECT * FROM loans');
        $data=array();
        foreach ($results as $user) {
           $data1=array();
           $data1['Clientid']=$user->client_id;
           $data1['num_of_payment']=$user->num_of_payment;
           $data1['first_payment_date']=$user->first_payment_date;
           $data1['last_payment_date']=$user->last_payment_date;
           $data1['loan_amount']=$user->loan_amount;
           array_push($data,$data1);
        }
        $startDates = array_column($data, 'first_payment_date');
        $endDates = array_column($data, 'last_payment_date');
        $minDate = new Carbon(min($startDates));
        $maxDate = new Carbon(max($endDates));
        
        $allMonths = [];
        $current = clone $minDate;
        while ($current <= $maxDate) {
            $allMonths[] = $current->format('Y_M');
            $current->modify('+1 month');
        }
        
        $results = [];
        
        foreach ($data as $client) {
            $clientid = $client['Clientid'];
            $num_of_payment = $client['num_of_payment'];
            $first_payment_date = new Carbon($client['first_payment_date']);
            $last_payment_date = new Carbon($client['last_payment_date']);
            $loan_amount = $client['loan_amount'];
        
            // Calculate base monthly payment and remainder
            $base_payment = round($loan_amount / $num_of_payment, 2); // Rounded base payment
            $remainder = round($loan_amount - ($base_payment * $num_of_payment), 2);
        
            $clientPayments = array_fill_keys($allMonths, 0.00);
            $paymentsMade = 0;
        
            $currentDate = clone $first_payment_date;
            while ($currentDate <= $last_payment_date && $paymentsMade < $num_of_payment) {
                $monthYear = $currentDate->format('Y_M');
        
                $monthly_payment = $base_payment;
                if ($paymentsMade === $num_of_payment - 1) {
                    $monthly_payment += $remainder; // Add remainder to the last payment
                }
        
                $clientPayments[$monthYear] = round($monthly_payment, 2);
        
                // Move to the next month
                $currentDate->modify('+1 month');
                $paymentsMade++;
            }
        
            // Store the result for the client
            $results[$clientid] = $clientPayments;
      
        }
      foreach($results as $key => $value){
        foreach($value as $mon => $amt){
            DB::insert('INSERT INTO emi_details (client_id,mon,amount) VALUES ('.$key.','.$mon.','.$amt.' )');
        }
        $html="<table><tr>";
        foreach($results as $key => $value){
            foreach($value as $mon => $amt){
                $html.="<tr><td>Client id</td><td>".$mon."<td>";
            }
            $html.="<tr><td>".$key."</td>";
            foreach($value as $mon => $amt){
                $html.="<td>".$amt."<td>";
            }
            $html.="</tr>";
        }

      }
      echo $html;
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
}
