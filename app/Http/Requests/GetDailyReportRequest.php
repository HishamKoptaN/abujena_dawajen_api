<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;
class GetDailyReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }
    public function rules(): array
    {
        return [
            'date' => 'required|date_format:Y-m-d', 
        ];
    }
    protected function passedValidation()
    {
        $this->merge([
            'date' => Carbon::parse($this->date)->startOfDay(),
        ]);
    }
}