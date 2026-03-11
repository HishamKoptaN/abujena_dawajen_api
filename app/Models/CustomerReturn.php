<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerReturn extends Model
{
    use HasFactory;
    protected $table = 'customer_returns';
    protected $fillable = [
        'customer_id',
        'product_id',
        'weight',
        'reason'
    ];
    public $timestamps = true;
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
