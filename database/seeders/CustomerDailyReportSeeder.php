<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\CustomerDailyReport;
use App\Models\Customer;
use App\Models\Transaction;
use App\Models\DailyCollection;
use Carbon\Carbon;
class CustomerDailyReportSeeder extends Seeder
{
    public function run()
    {
        $customers = Customer::all();
        for ($i = 30; $i >= 0; $i--) {
            $reportDate = Carbon::now()->subDays($i);
            $previousReport = CustomerDailyReport::where('customer_id', $customers->random()->id)
                ->whereDate('created_at', $reportDate->copy()->subDay())
                ->first();
            $openingBalance = $previousReport ? $previousReport->closing_balance : rand(-500, 500);
            $totalSales = Transaction::where('customer_id', $customers->random()->id)
                ->whereDate('created_at', $reportDate)
                ->with(['transactionDetails.product'])
                ->get()
                ->sum(function ($transaction) {
                    return $transaction->transactionDetails->sum(function ($detail) {
                        return ($detail->quantity * $detail->price_at_time) - $detail->discount;
                    });
                });
            $totalCollections = DailyCollection::where('customer_id', $customers->random()->id)
                ->whereDate('created_at', $reportDate)
                ->sum('amount');
            $closingBalance = $openingBalance + $totalSales - $totalCollections;
            CustomerDailyReport::create([
                'customer_id' => $customers->random()->id,
                'closing_balance' => $closingBalance,
                'created_at' => $reportDate,
                'updated_at' => $reportDate
            ]);
        }
    }
}
