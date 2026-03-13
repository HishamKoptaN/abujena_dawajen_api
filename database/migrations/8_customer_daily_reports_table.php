<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\CustomerDailyReport;


return new class extends Migration
{
    public function up()
    {
        Schema::create('customer_daily_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->decimal('closing_balance', 12, 2)->default(0);
            $table->timestamps();
        });
        $customers = [
            [
                "id" => 1,
                "name" => "ام عبد الرحمن",
                "number" => 1,
                "closing_balance" => 9300
            ],
             [
                "id" => 2,
                "name" => "ام مدحت",
                "number" => 2,
                "closing_balance" => 780
            ],
            [
                "id" => 3,
                "name" => "محمد حافظ",
                "number" => 3,
                "closing_balance" => 2315
            ],
           
            [
                "id" => 4,
                "name" => "شعرواي",
                "number" => 4,
                "closing_balance" => 1595
            ],
            [
                "id" => 5,
                "name" => "درويش الجزار",
                "number" => 5,
                "closing_balance" => 3685
            ],
            [
                "id" => 6,
                "name" => "المحل",
                "number" => 6,
                "closing_balance" => 0
            ],
            [
                "id" => 7,
                "name" => "ام ندا برنشت",
                "number" => 7,
                "closing_balance" => 710
            ],
            [
                "id" => 8,
                "name" => "ام كريم",
                "number" => 8,
                "closing_balance" => 160
            ],
            [
                "id" => 9,
                "name" => "احمد صلاح",
                "number" => 9,
                "closing_balance" => 3975
            ],
            [
                "id" => 10,
                "name" => "ابو سريع سعيد",
                "number" => 10,
                "closing_balance" => 360
            ],
            [
                "id" => 11,
                "name" => "الحداد",
                "number" => 11,
                "closing_balance" => 9145
            ],
            [
                "id" => 12,
                "name" => "الحاج عمارة",
                "number" => 12,
                "closing_balance" => 7680
            ],
            [
                "id" => 13, 
                "name" => "احمد شعبان",
                "number" => 13,
                "closing_balance" => 2480
            ],
            [
                "id" => 14,
                "name" => "محمد رشاد",
                "number" => 14,
                "closing_balance" => 285
            ],
            [
                "id" => 15,
                "name" => "عبود ابو رية ",
                "number" => 15,
                "closing_balance" => 650
            ],
            [
                "id" => 16,
                "name" => "عيد رياض",
                "number" => 16,
                "closing_balance" => 2925
            ],
            [
                "id" => 17,
                "name" => "خالد سعيد",
                "number" => 17,
                "closing_balance" => 9885
            ],
            [
                "id" => 18,
                "name" => "ام عبدالله",
                "number" => 18,
                "closing_balance" => 90
            ],
             [
                "id" => 19,
                "name" => "ابو عمار",
                "number" => 19,
                "closing_balance" => 0
            ],
            [
                "id" => 20,
                "name" => "محمد الشريف",
                "number" => 20,
                "closing_balance" => 1345
            ],
            [
                "id" => 21,
                "name" => "فكري صابر",
                "number" => 21,
                "closing_balance" => 1615
            ],
            [
                "id" => 22,
                "name" => "نادي عبد العزيز",
                "number" => 22,
                "closing_balance" => 550
            ],
            [
                "id" => 23,
                "name" => "ام فارس سعيد",
                "number" => 23,
                "closing_balance" => 1540
            ],
            [
                "id" => 24,
                "name" => "احمد جمعة",
                "number" => 24,
                "closing_balance" => 1335
            ],
            [
                "id" => 25,
                "name" => "ام سعيد",
                "number" => 25,
                "closing_balance" => 270
            ],
            [
                "id" => 26,
                "name" => "محمد سمحي",
                "number" => 26,
                "closing_balance" => 7100
            ],
            [
                "id" => 27,
                "name" => "محمد مرعي",
                "number" => 27,
                "closing_balance" => 345
            ],
            [
                "id" => 28,
                "name" => "محمد عبد العاطي",
                "number" => 28,
                "closing_balance" => 1000
            ],
            [
                "id" => 29,
                "name" => "ام ميادة",
                "number" => 29,
                "closing_balance" => 780
            ],
            [
                "id" => 30,
                "name" => "كريم حسن",
                "number" => 30,
                "closing_balance" => 2845
            ],
             [
                "id" => 31,
                "name" => "محمود احمد",
                "number" => 31,
                "closing_balance" => 650
            ],
            [
                "id" => 32,
                "name" => "ام جومانا",
                "number" => 32,
                "closing_balance" => 20525
            ],
            [
                "id" => 33,
                "name" => "ام كريم عبدالله",
                "number" => 33,
                "closing_balance" => 2810
            ],
            [
                "id" => 34,
                "name" => "سعيد محمد",
                "number" => 34,
                "closing_balance" => 10025
            ],
            [
                "id" => 35,
                "name" => "ابو ادهم الضبعي",
                "number" => 35,
                "closing_balance" => 3090
            ],
            [
                "id" => 36,
                "name" => "ام محمد نسمة",
                "number" => 36,
                "closing_balance" => 3565
            ],
            [
                "id" => 37,
                "name" => "ام عادل",
                "number" => 37,
                "closing_balance" => 955
            ],
            [
                "id" => 38,
                "name" => "ام محمود الضبعي",
                "number" => 38,
                "closing_balance" => 1295
            ],
            [
                "id" => 39,
                "name" => "عاصم الصعيدي",
                "number" => 39,
                "closing_balance" => 4030
            ],
            [
                "id" => 40,
                "name" => "محمود حسن",
                "number" => 40,
                "closing_balance" => 7600
            ],
            [
                "id" => 41,
                "name" => "ام ايمان",
                "number" => 41,
                "closing_balance" => 1110
            ],
            [
                "id" => 42,
                "name" => "عيد زكريا",
                "number" => 42,
                "closing_balance" => 1865
            ],
            [
                "id" => 43,
                "name" => "محمد صلاح",
                "number" => 43,
                "closing_balance" => 5465
            ],
            [
                "id" => 44,
                "name" => "ام شروق",
                "number" => 44,
                "closing_balance" => 6515
            ],
              [
                "id" => 45,
                "name" => "ام عبدالرحمن ابو نجم",
                "number" => 45,
                "closing_balance" => 480
            ],
              [
                "id" => 46,
                "name" => "الحاج مادين",
                "number" => 46,
                "closing_balance" => 500
            ],
            [
                "id" => 47,
                "name" => "ام ياسين ابو نجم",
                "number" => 47,
                "closing_balance" => 420
            ],
            [
                "id" => 48,
                "name" => "مصطفي شامخ",
                "number" => 48,
                "closing_balance" => 4130
            ],
            [
                "id" => 49,
                "name" => "احمد عبد المجيد",
                "number" => 49,
                "closing_balance" => 8285
            ],
            [
                "id" => 50,
                "name" => "ام محمد البوسطة",
                "number" => 50,
                "closing_balance" => 330
            ],
            [
                "id" => 51,
                "name" => "ام مصطفي العزبة",
                "number" => 51,
                "closing_balance" => 7645
            ],
            [
                "id" => 52,
                "name" => "عبده نمر",
                "number" => 52,
                "closing_balance" => 180
            ],
            [
                "id" => 53,
                "name" => "ام نوسة",
                "number" => 53,
                "closing_balance" => 0
            ],
            [
                "id" => 54,
                "name" => "ام احمد رجب",
                "number" => 54,
                "closing_balance" => 0
            ],
            [
                "id" => 55,
                "name" => "محمد عاشور",
                "number" => 55,
                "closing_balance" => 0
            ],
            [
                "id" => 56,
                "name" => "محمود رجب ",
                "number" => 56,
                "closing_balance" => 300
            ],
            [
                "id" => 57,
                "name" => "عمرو هاشم",
                "number" => 57,
                "closing_balance" => 205
            ],
            [
                "id" => 58,
                "name" => "ام مؤمن",
                "number" => 58,
                "closing_balance" => 3150
            ],
            [
                "id" => 59,
                "name" => "اسلام صادق",
                "number" => 59,
                "closing_balance" => 1695
            ],
            [
                "id" => 60,
                "name" => "مسعد سعد",
                "number" => 60,
                "closing_balance" => 950
            ],
            [
                "id" => 61,
                "name" => "الحاجه ام عبد الناصر",
                "number" => 61,
                "closing_balance" => 3435
            ],
            [
                "id" => 62,
                "name" => "حماده المغربي",
                "number" => 62,
                "closing_balance" => 1020
            ],
            [
                "id" => 63,
                "name" => "الشيخ عبدالدايم",
                "number" => 63,
                "closing_balance" => 7110
            ],
            [
                "id" => 64,
                "name" => "محمد محمود",
                "number" => 64,
                "closing_balance" => 22295
            ],
            [
                "id" => 65,
                "name" => "ياسر شكري",
                "number" => 65,
                "closing_balance" => 700
            ],
            [
                "id" => 66,
                "name" => "ام سوكة",
                "number" => 66,
                "closing_balance" => 1715
            ],
            [
                "id" => 67,
                "name" => "حمدي ابراهيم",
                "number" => 67,
                "closing_balance" => 4725
            ],
            [
                "id" => 68,
                "name" => "عمر ماهر",
                "number" => 68,
                "closing_balance" => 1155
            ],
            [
                "id" => 69,
                "name" => "حسن سلطان",
                "number" => 69,
                "closing_balance" => 1330
            ],
            [
                "id" => 70,
                "name" => "عرفة محمد",
                "number" => 70,
                "closing_balance" => 800
            ],
            [
                "id" => 71,
                "name" => "فرج مزغونة",
                "number" => 71,
                "closing_balance" => 55575
            ],
            [
                "id" => 72,
                "name" => "ام مكة",
                "number" => 72,
                "closing_balance" => 3215
            ],
            [
                "id" => 73,
                "name" => "ام زياد",
                "number" => 73,
                "closing_balance" => 1125,
            ],
            [
                "id" => 74,
                "name" => "ام عبده",
                "number" => 74,
                "closing_balance" => 6150,
            ],
            [
                "id" => 75,
                "name" => "ام احمد علاء",
                "number" => 75,
                "closing_balance" => 230,
            ],
            [
                "id" => 76,
                "name" => "ابو سريع",
                "number" => 76,
                "closing_balance" => 184150,
            ],
            [
                "id" => 77,
                "name" => "اسلام شباك ",
                "number" => 77,
                "closing_balance" => 620,
            ],
            [
                "id" => 78,
                "name" => "مجدي هدهود",
                "number" => 78,
                "closing_balance" => 3500,
            ],
            [
                "id" => 79,
                "name" => "عز عبدالرحيم",
                "number" => 79,
                "closing_balance" => 1630,
            ],
            [
                "id" => 80,
                "name" => "الشيخ عبده ",
                "number" => 80,
                "closing_balance" => 560,
            ],
            [
                "id" => 81,
                "name" => " احمد سعودي ",
                "number" => 81,
                "closing_balance" => 1175
            ],
            [
                "id" => 82,
                "name" => " احمد الحسيني ",
                "number" => 82,
                "closing_balance" =>0,
            ],
            [
                "id" => 83,
                "name" => " حازم بهبيت  ",
                "number" => 83,
                "closing_balance" => 0,
            ],

            [
                "id" => 84,
                "name" => "محمود ياسين",
                "number" => 84,
                "closing_balance" => 20800,
            ],
            [
                "id" => 85,
                "name" => "رجب",
                "number" => 85,
                "closing_balance" => 18800,
            ],
            [
                "id" => 86,
                "name" => "سعيد غريب",
                "number" => 86,
                "closing_balance" => 25000,
            ],
            [
                "id" => 87,
                "name" => "مسعد رجب",
                "number" => 87,
                "closing_balance" => 2450,
            ],
            [
                "id" => 88,
                "name" => "ام سعد ",
                "number" => 88,
                "closing_balance" => 1750,
            ],
            [
                "id" => 89,
                "name" => "ام جنات سعيد",
                "number" => 89,
                "closing_balance" => 34500,
            ],
        ];
        foreach ($customers as $customerData) {
            CustomerDailyReport::Create(
                [
                    'customer_id' => $customerData['id'],
                    'closing_balance' => $customerData['closing_balance'],
                    'created_at' => '2026-03-12',
                    'updated_at' => '2026-03-12',
                ]
            );
        }
    }
    public function down()
    {
        Schema::dropIfExists('customer_daily_reports');
    }
};
