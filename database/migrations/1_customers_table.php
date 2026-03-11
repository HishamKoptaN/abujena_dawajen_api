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
            $table->string('phone')->nullable();
            $table->integer('number')->nullable();
            $table->string('address')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
         $customers = [
            [
                "id" => 1,
                "name" => "ام عبد الرحمن",
                "number" => 1,
            ],
             [
                "id" => 2,
                "name" => "ام مدحت",
                "number" => 2,
            ],
            [
                "id" => 3,
                "name" => "محمد حافظ",
                "number" => 3,
            ],
           
            [
                "id" => 4,
                "name" => "شعرواي",
                "number" => 4,
            ],
            [
                "id" => 5,
                "name" => "درويش الجزار",
                "number" => 5,
            ],
            [
                "id" => 6,
                "name" => "المحل",
                "number" => 6,
            ],
             [
                "id" => 7,
                "name" => "ام ندا برنشت",
                "number" => 7,
            ],
            [
                "id" => 8,
                "name" => "ام كريم",
                "number" => 8,
            ],
            [
                "id" => 9,
                "name" => "احمد صلاح",
                "number" => 9,
            ],
            [
                "id" => 10,
                "name" => "ابو سريع سعيد",
                "number" => 10,
            ],
            [
                "id" => 11,
                "name" => "الحداد",
                "number" => 11,
            ],
            [
                "id" => 12,
                "name" => "الحاج عمارة",
                "number" => 12,
            ],
            [
                "id" => 13, 
                "name" => "احمد شعبان",
                "number" => 13,
            ],
            [
                "id" => 14,
                "name" => "محمد رشاد",
                "number" => 14,
            ],
            [
                "id" => 15,
                "name" => "عبود ابو رية ",
                "number" => 15,
            ],
            [
                "id" => 16,
                "name" => "عيد رياض",
                "number" => 16,
            ],
            [
                "id" => 17,
                "name" => "خالد سعيد",
                "number" => 17,
            ],
            [
                "id" => 18,
                "name" => "ام عبدالله",
                "number" => 18,
            ],
             [
                "id" => 19,
                "name" => "ابو عمار",
                "number" => 19,
            ],
            [
                "id" => 20,
                "name" => "محمد الشريف",
                "number" => 20,
            ],
            [
                "id" => 21,
                "name" => "فكري صابر",
                "number" => 21,
            ],
            [
                "id" => 22,
                "name" => "نادي عبد العزيز",
                "number" => 22,
            ],
            [
                "id" => 23,
                "name" => "ام فارس سعيد",
                "number" => 23,
            ],
            [
                "id" => 24,
                "name" => "احمد جمعة",
                "number" => 24,
            ],
            [
                "id" => 25,
                "name" => "ام سعيد",
                "number" => 25,
            ],
            [
                "id" => 26,
                "name" => "محمد سمحي",
                "number" => 26,
            ],
            [
                "id" => 27,
                "name" => "محمد مرعي",
                "number" => 27,
            ],
            [
                "id" => 28,
                "name" => "محمد عبد العاطي",
                "number" => 28,
            ],
            [
                "id" => 29,
                "name" => "ام ميادة",
                "number" => 29,
            ],
            [
                "id" => 30,
                "name" => "كريم حسن",
                "number" => 30,
            ],
             [
                "id" => 31,
                "name" => "محمود احمد",
                "number" => 31,
            ],
            [
                "id" => 32,
                "name" => "ام جومانا",
                "number" => 32,
            ],
            [
                "id" => 33,
                "name" => "ام كريم عبدالله",
                "number" => 33,
            ],
            [
                "id" => 34,
                "name" => "سعيد محمد",
                "number" => 34,
            ],
            [
                "id" => 35,
                "name" => "ابو ادهم الضبعي",
                "number" => 35,
            ],
            [
                "id" => 36,
                "name" => "ام محمد نسمة",
                "number" => 36,
            ],
            [
                "id" => 37,
                "name" => "ام عادل",
                "number" => 37,
            ],
            [
                "id" => 38,
                "name" => "ام محمود الضبعي",
                "number" => 38,
            ],
            [
                "id" => 39,
                "name" => "عاصم الصعيدي",
                "number" => 39,
            ],
            [
                "id" => 40,
                "name" => "محمود حسن",
                "number" => 40,
            ],
            [
                "id" => 41,
                "name" => "ام ايمان",
                "number" => 41,
            ],
            [
                "id" => 42,
                "name" => "عيد زكريا",
                "number" => 42,
            ],
            [
                "id" => 43,
                "name" => "محمد صلاح",
                "number" => 43,
            ],
            [
                "id" => 44,
                "name" => "ام شروق",
                "number" => 44,
            ],
              [
                "id" => 45,
                "name" => "ام عبدالرحمن ابو نجم",
                "number" => 45,
            ],
              [
                "id" => 46,
                "name" => "الحاج مادين",
                "number" => 46,
            ],
            [
                "id" => 47,
                "name" => "ام ياسين ابو نجم",
                "number" => 47,
            ],
            [
                "id" => 48,
                "name" => "مصطفي شامخ",
                "number" => 48,
            ],
            [
                "id" => 49,
                "name" => "احمد عبد المجيد",
                "number" => 49,
            ],
            [
                "id" => 50,
                "name" => "ام محمد البوسطة",
                "number" => 50,
            ],
            [
                "id" => 51,
                "name" => "ام مصطفي العزبة",
                "number" => 51,
            ],
            [
                "id" => 52,
                "name" => "عبده نمر",
                "number" => 52,
            ],
            [
                "id" => 53,
                "name" => "ام نوسة",
                "number" => 53,
            ],
            [
                "id" => 54,
                "name" => "ام احمد رجب",
                "number" => 54,
            ],
            [
                "id" => 55,
                "name" => "محمد عاشور",
                "number" => 55,
            ],
            [
                "id" => 56,
                "name" => "محمود رجب ",
                "number" => 56,
            ],
            [
                "id" => 57,
                "name" => "عمرو هاشم",
                "number" => 57,
            ],
            [
                "id" => 58,
                "name" => "ام مؤمن",
                "number" => 58,
            ],
            [
                "id" => 59,
                "name" => "اسلام صادق",
                "number" => 59,
            ],
            [
                "id" => 60,
                "name" => "مسعد سعد",
                "number" => 60,
            ],
            [
                "id" => 61,
                "name" => "الحاجه ام عبد الناصر",
                "number" => 61,
            ],
            [
                "id" => 62,
                "name" => "حماده المغربي",
                "number" => 62,
            ],
            [
                "id" => 63,
                "name" => "الشيخ عبدالدايم",
                "number" => 63,
            ],
            [
                "id" => 64,
                "name" => "محمد محمود",
                "number" => 64,
            ],
            [
                "id" => 65,
                "name" => "ياسر شكري",
                "number" => 65,
            ],
            [
                "id" => 66,
                "name" => "ام سوكة",
                "number" => 66,
            ],
            [
                "id" => 67,
                "name" => "حمدي ابراهيم",
                "number" => 67,
            ],
            [
                "id" => 68,
                "name" => "عمر ماهر",
                "number" => 68,
            ],
            [
                "id" => 69,
                "name" => "حسن سلطان",
                "number" => 69,
            ],
            [
                "id" => 70,
                "name" => "عرفة محمد",
                "number" => 70,
            ],
            [
                "id" => 71,
                "name" => "فرج مزغونة",
                "number" => 71,
            ],
             [
                "id" => 72,
                "name" => "ام مكة",
                "number" => 72,
            ],
            [
                "id" => 73,
                "name" => "ام زياد",
                "number" => 73,
            ],
            [
                "id" => 74,
                "name" => "ام عبده",
                "number" => 74,
            ],
            [
                "id" => 75,
                "name" => "ام احمد علاء",
                "number" => 75,
            ],
            [
                "id" => 76,
                "name" => "ابو سريع",
                "number" => 76,
            ],
            [
                "id" => 77,
                "name" => "اسلام شباك ",
                "number" => 77,
            ],
            [
                "id" => 78,
                "name" => "مجدي هدهود",
                "number" => 78,
            ],
            [
                "id" => 79,
                "name" => "عز عبدالرحيم",
                "number" => 79,
            ],
            [
                "id" => 80,
                "name" => "الشيخ عبده ",
                "number" => 80,
            ],
            [
                "id" => 81,
                "name" => " احمد سعودي ",
                "number" => 81,
            ],
            [
                "id" => 82,
                "name" => " احمد الحسيني ",
                "number" => 82,
            ],
            [
                "id" => 83,
                "name" => " حازم بهبيت  ",
                "number" => 83,
            ],

            [
                "id" => 84,
                "name" => "محمود ياسين",
                "number" => 84,
            ],
            [
                "id" => 85,
                "name" => "رجب",
                "number" => 85,
            ],
            [
                "id" => 86,
                "name" => "سعيد غريب",
                "number" => 86,
            ],
            [
                "id" => 87,
                "name" => "مسعد رجب",
                "number" => 87,
            ],
            [
                "id" => 88,
                "name" => "ام سعد ",
                "number" => 88,
            ],
            [
                "id" => 89,
                "name" => "ام جنات سعيد",
                "number" => 89,
            ],
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
