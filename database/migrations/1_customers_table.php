<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Customer;
return new class extends Migration
{
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->unique();
            $table->integer('number')->nullable();
            $table->string('address')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
         $customers = [
            ['name' => 'أحمد', 'phone' => '01210000001', 'number' => 1, 'address' => 'شارع الرئيسي'],
            ['name' => 'أبو محمد', 'phone' => '01210000002', 'number' => 2, 'address' => 'حي النخيل'],
            ['name' => 'إبراهيم', 'phone' => '01210000003', 'number' => 3, 'address' => 'المركز'],
            ['name' => 'أبو ناصر', 'phone' => '01210000004', 'number' => 4, 'address' => 'المنطقة الشمالية'],
            ['name' => 'محمد علي', 'phone' => '01210000005', 'number' => 5, 'address' => 'شارع الملك فهد'],
            ['name' => 'عبدالله', 'phone' => '01210000006', 'number' => 6, 'address' => 'حي الروضة'],
            ['name' => 'خالد', 'phone' => '01210000007', 'number' => 7, 'address' => 'المنطقة الشرقية'],
            ['name' => 'سعد', 'phone' => '01210000008', 'number' => 8, 'address' => 'شارع الأمير محمد'],
            ['name' => 'فهد', 'phone' => '01210000009', 'number' => 9, 'address' => 'حي المروج'],
            ['name' => 'ناصر', 'phone' => '01210000010', 'number' => 10, 'address' => 'المنطقة الغربية'],
            ['name' => 'عمر', 'phone' => '01210000011', 'number' => 11, 'address' => 'شارع العزيزية'],
            ['name' => 'بندر', 'phone' => '01210000012', 'number' => 12, 'address' => 'حي الربيع'],
            ['name' => 'سالم', 'phone' => '01210000013', 'number' => 13, 'address' => 'المعذر'],
            ['name' => 'فيصل', 'phone' => '01210000014', 'number' => 14, 'address' => 'حي الملز'],
            ['name' => 'تركي', 'phone' => '01210000015', 'number' => 15, 'address' => 'شارع التحلية'],
            ['name' => 'مشاري', 'phone' => '01210000016', 'number' => 16, 'address' => 'حي السفارات'],
            ['name' => 'عبدالعزيز', 'phone' => '01210000017', 'number' => 17, 'address' => 'حي النخيل'],
            ['name' => 'سلطان', 'phone' => '01210000018', 'number' => 18, 'address' => 'المنطقة المركزية'],
            ['name' => 'يوسف', 'phone' => '01210000019', 'number' => 19, 'address' => 'شارع الأمير تركي'],
            ['name' => 'عبدالرحمن', 'phone' => '01210000020', 'number' => 20, 'address' => 'حي العارض'],
            ['name' => 'محمود', 'phone' => '01210000021', 'number' => 21, 'address' => 'المنطقة الصناعية'],
            ['name' => 'حسن', 'phone' => '01210000022', 'number' => 22, 'address' => 'حي الريان'],
            ['name' => 'علي', 'phone' => '01210000023', 'number' => 23, 'address' => 'شارع الملك عبدالله'],
            ['name' => 'حمد', 'phone' => '01210000024', 'number' => 24, 'address' => 'حي الشفا'],
            ['name' => 'خالد بن', 'phone' => '01210000025', 'number' => 25, 'address' => 'المنطقة الجنوبية'],
            ['name' => 'سعود', 'phone' => '01210000026', 'number' => 26, 'address' => 'حي المرسلات'],
            ['name' => 'راشد', 'phone' => '01210000027', 'number' => 27, 'address' => 'شارع الثمامة'],
            ['name' => 'متعب', 'phone' => '01210000028', 'number' => 28, 'address' => 'حي الغدير'],
            ['name' => 'فهد بن', 'phone' => '01210000029', 'number' => 29, 'address' => 'المنطقة الشمالية الشرقية'],
            ['name' => 'أبو بدر', 'phone' => '01210000030', 'number' => 30, 'address' => 'حي الياسمين'],
            ['name' => 'أبو خالد', 'phone' => '01210000031', 'number' => 31, 'address' => 'شارع الوشم'],
            ['name' => 'أبو سعود', 'phone' => '01210000032', 'number' => 32, 'address' => 'حي العزيزية'],
            ['name' => 'أبو عبدالله', 'phone' => '01210000033', 'number' => 33, 'address' => 'المنطقة الوسطى'],
            ['name' => 'أبو فهد', 'phone' => '01210000034', 'number' => 34, 'address' => 'حي النظيم'],
            ['name' => 'أبو نايف', 'phone' => '01210000035', 'number' => 35, 'address' => 'شارع المطار'],
            ['name' => 'أبو تركي', 'phone' => '01210000036', 'number' => 36, 'address' => 'حي الروضة'],
            ['name' => 'أبو يوسف', 'phone' => '01210000037', 'number' => 37, 'address' => 'المنطقة الغربية الشمالية'],
            ['name' => 'أبو إبراهيم', 'phone' => '01210000038', 'number' => 38, 'address' => 'حي السلي'],
            ['name' => 'أبو حسن', 'phone' => '01210000039', 'number' => 39, 'address' => 'شارع العليا'],
            ['name' => 'أبو علي', 'phone' => '01210000040', 'number' => 40, 'address' => 'حي المروج'],
            ['name' => 'أبو عمر', 'phone' => '01210000041', 'number' => 41, 'address' => 'المنطقة الشرقية الجنوبية'],
            ['name' => 'أبو عبدالرحمن', 'phone' => '01210000042', 'number' => 42, 'address' => 'حي الشفاء'],
            ['name' => 'أبو محمود', 'phone' => '01210000043', 'number' => 43, 'address' => 'شارع الملك فهد'],
            ['name' => 'أبو حسين', 'phone' => '01210000044', 'number' => 44, 'address' => 'حي الرفيعة'],
            ['name' => 'أبو أحمد', 'phone' => '01210000045', 'number' => 45, 'address' => 'المنطقة المركزية الشمالية'],
            ['name' => 'أبو سعد', 'phone' => '01210000046', 'number' => 46, 'address' => 'حي العقيق'],
            ['name' => 'أبو ناصر الثاني', 'phone' => '01210000047', 'number' => 47, 'address' => 'شارع الأمير سعود'],
            ['name' => 'أبو بندر', 'phone' => '01210000048', 'number' => 48, 'address' => 'حي ضاحية الرماح'],
            ['name' => 'أبو فيصل', 'phone' => '01210000049', 'number' => 49, 'address' => 'المنطقة الجنوبية الشرقية'],
            ['name' => 'أبو تركي الثاني', 'phone' => '01210000050', 'number' => 50, 'address' => 'حي العزيزية الجديدة']
        ];
        foreach ($customers as $customer) {
            Customer::create($customer);
        }
    }
    public function down()
    {
        Schema::dropIfExists('customers');
    }
};
