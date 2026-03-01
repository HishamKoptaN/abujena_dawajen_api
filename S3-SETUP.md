# S3 Bucket Setup Guide

## 🚀 خطوات الربط

### 1. **تثبيت AWS SDK**
```bash
composer require league/flysystem-aws-s3-v3
```

### 2. **إنشاء Buckets**
- **Dev**: `mubin-api-dev`
- **Prod**: `mubin-api-prod`

### 3. **إعداد IAM User**
1. أنشئ IAM User جديد
2. أرفق الـ Policy من ملف `aws-s3-policy.json`
3. احصل على Access Key و Secret Key

### 4. **تحديث متغيرات البيئة**
تم تحديث `.env` بالمتغيرات التالية:
```
AWS_ACCESS_KEY_ID=your_access_key_here
AWS_SECRET_ACCESS_KEY=your_secret_key_here
AWS_DEFAULT_REGION=eu-north-1
AWS_BUCKET=muein-s3
FILESYSTEM_DISK=s3
```

### 5. **اختبار الاتصال**
```bash
php artisan s3:test
```

## 📁 هيكل المجلدات

```
s3://your-bucket/
├── dev/
│   ├── 2024/
│   │   ├── 01/
│   │   └── 02/
│   └── user/{user_id}/
├── prod/
│   ├── 2024/
│   └── admin/
└── test/
```

## 🔧 الأوامر المتاحة

```bash
# اختبار الاتصال
php artisan s3:test

# تنظيف الملفات القديمة
php artisan s3:cleanup --days=30

# عرض قائمة الملفات
php artisan tinker
>>> $s3 = app(App\Services\S3Service::class);
>>> $files = $s3->listFiles('dev/2024/02');
```

## 🌐 API Endpoints

### Upload (Direct)
```bash
POST /api/s3/upload
Content-Type: multipart/form-data
Authorization: Bearer {token}
```

### Presigned URLs
```bash
# الحصول على رابط رفع
POST /api/s3/presigned-upload-url
{
    "file_name": "document.pdf",
    "content_type": "application/pdf",
    "folder": "documents"
}

# الحصول على رابط تحميل
POST /api/s3/presigned-download-url
{
    "path": "dev/2024/02/document.pdf"
}
```

## 📋 التحقق

1. ✅ تثبيت AWS SDK
2. ✅ إنشاء Buckets
3. ✅ إعداد IAM User
4. ✅ تحديث .env
5. ✅ إعداد filesystems.php
6. ✅ إنشاء S3Service
7. ✅ إنشاء Controllers
8. ✅ إضافة Routes
9. ✅ إنشاء Test Command

## 🎯 الخطوات التالية

1. **شغّل اختبار الاتصال**: `php artisan s3:test`
2. **اختبر الرفع عبر API**
3. **اختبر Presigned URLs**
4. **إعداد CORS للـ Bucket**
5. **إعداد Lifecycle Rules**
