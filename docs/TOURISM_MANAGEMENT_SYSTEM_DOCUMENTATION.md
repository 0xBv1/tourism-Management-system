# نظام إدارة السياحة - Tourism Management System

## نظرة عامة على النظام

نظام إدارة السياحة هو تطبيق ويب متكامل مبني على Laravel 10 مصمم لإدارة عمليات السياحة والسفر. يوفر النظام إدارة شاملة للاستفسارات والحجوزات والموارد السياحية والمدفوعات مع نظام إشعارات متقدم.

## الميزات الرئيسية

### 1. إدارة الاستفسارات (Inquiry Management)
- **إنشاء وإدارة الاستفسارات**: إمكانية إنشاء استفسارات جديدة من العملاء
- **تتبع الحالة**: تتبع حالة الاستفسارات (معلق، مؤكد، ملغي)
- **تعيين المستخدمين**: إمكانية تعيين استفسارات لمستخدمين محددين بأدوار مختلفة
- **إدارة الموارد**: ربط الموارد السياحية بالاستفسارات
- **نظام الدردشة**: نظام دردشة داخلي للتواصل بين الفريق

### 2. إدارة الحجوزات (Booking Management)
- **إنشاء ملفات الحجز**: إنشاء ملفات PDF للحجوزات المؤكدة
- **تتبع التقدم**: نظام قائمة مهام لتتبع تقدم الحجز
- **إدارة الحالة**: تتبع حالة الحجز من البداية حتى النهاية
- **تحميل وإرسال الملفات**: إمكانية تحميل وإرسال ملفات الحجز

### 3. إدارة الموارد السياحية (Resource Management)
- **الفنادق**: إدارة الفنادق مع التفاصيل والأسعار والتوفر
- **المركبات**: إدارة أسطول المركبات مع السائقين والمواصفات
- **المرشدين**: إدارة المرشدين السياحيين مع التخصصات واللغات
- **الممثلين**: إدارة الممثلين المحليين للخدمات السياحية
- **التذاكر**: إدارة تذاكر المعالم السياحية
- **الرحلات النيلية**: إدارة رحلات الدهابية والنيل
- **المطاعم**: إدارة المطاعم والوجبات

### 4. إدارة المدفوعات (Payment Management)
- **معالجة المدفوعات**: إدارة المدفوعات بطرق مختلفة
- **تتبع الحالة**: تتبع حالة المدفوعات (مدفوع، معلق، غير مدفوع)
- **التقارير المالية**: تقارير مفصلة عن المدفوعات والإيرادات
- **الإشعارات**: إشعارات تلقائية للمدفوعات المتأخرة

### 5. نظام الإشعارات (Notification System)
- **إشعارات البريد الإلكتروني**: إرسال إشعارات عبر البريد الإلكتروني
- **إشعارات قاعدة البيانات**: إشعارات داخلية في النظام
- **إشعارات WhatsApp**: إمكانية إرسال إشعارات عبر WhatsApp
- **إشعارات SMS**: إرسال رسائل نصية عبر Twilio

### 6. إدارة المستخدمين والأدوار (User & Role Management)
- **إدارة المستخدمين**: إنشاء وإدارة حسابات المستخدمين
- **نظام الأدوار**: أدوار مختلفة (Admin, Sales, Reservation, Operator)
- **نظام الصلاحيات**: صلاحيات مفصلة لكل دور
- **إدارة العملاء**: إدارة حسابات العملاء

## الهيكل التقني

### التقنيات المستخدمة
- **Laravel 10**: إطار العمل الأساسي
- **PHP 8.1+**: لغة البرمجة
- **MySQL**: قاعدة البيانات
- **Tailwind CSS**: للتصميم
- **Alpine.js**: للتفاعل في الواجهة الأمامية
- **Vite**: لأدوات البناء

### المكتبات والحزم المهمة
- **Spatie Laravel Permission**: لإدارة الصلاحيات
- **Spatie Laravel Activity Log**: لتسجيل الأنشطة
- **Yajra DataTables**: لجداول البيانات التفاعلية
- **Barryvdh DomPDF**: لإنشاء ملفات PDF
- **Laravel Passport**: للمصادقة API
- **Twilio SDK**: لإرسال الرسائل النصية
- **Google Translate**: للترجمة التلقائية

## هيكل قاعدة البيانات

### الجداول الرئيسية

#### 1. جدول الاستفسارات (inquiries)
```sql
- id (Primary Key)
- inquiry_id (معرف فريد للاستفسار)
- guest_name (اسم الضيف)
- email (البريد الإلكتروني)
- phone (رقم الهاتف)
- arrival_date (تاريخ الوصول)
- departure_date (تاريخ المغادرة)
- number_pax (عدد الأشخاص)
- tour_name (اسم الرحلة)
- nationality (الجنسية)
- subject (موضوع الاستفسار)
- tour_itinerary (برنامج الرحلة)
- status (الحالة: pending, confirmed, cancelled)
- client_id (معرف العميل)
- assigned_to (معرف المستخدم المعين)
- assigned_reservation_id (معرف موظف الحجوزات)
- assigned_operator_id (معرف موظف العمليات)
- assigned_admin_id (معرف المدير)
- booking_file_id (معرف ملف الحجز)
- total_amount (المبلغ الإجمالي)
- paid_amount (المبلغ المدفوع)
- remaining_amount (المبلغ المتبقي)
- payment_method (طريقة الدفع)
- confirmed_at (تاريخ التأكيد)
- completed_at (تاريخ الإنجاز)
- created_at, updated_at, deleted_at
```

#### 2. جدول ملفات الحجز (booking_files)
```sql
- id (Primary Key)
- inquiry_id (معرف الاستفسار)
- file_name (اسم الملف)
- file_path (مسار الملف)
- status (الحالة: pending, confirmed, in_progress, completed, cancelled, refunded)
- generated_at (تاريخ الإنشاء)
- sent_at (تاريخ الإرسال)
- downloaded_at (تاريخ التحميل)
- checklist (قائمة المهام - JSON)
- notes (ملاحظات)
- total_amount (المبلغ الإجمالي)
- currency (العملة)
- created_at, updated_at
```

#### 3. جدول الفنادق (hotels)
```sql
- id (Primary Key)
- name (اسم الفندق)
- description (الوصف)
- address (العنوان)
- city_id (معرف المدينة)
- phone (الهاتف)
- email (البريد الإلكتروني)
- website (الموقع الإلكتروني)
- star_rating (التقييم بالنجوم)
- total_rooms (إجمالي الغرف)
- available_rooms (الغرف المتاحة)
- price_per_night (السعر لليلة)
- currency (العملة)
- amenities (المرافق - JSON)
- images (الصور - JSON)
- status (الحالة: available, occupied, maintenance, out_of_service)
- active (نشط)
- enabled (مفعل)
- check_in_time (وقت تسجيل الوصول)
- check_out_time (وقت تسجيل المغادرة)
- cancellation_policy (سياسة الإلغاء)
- notes (ملاحظات)
- created_at, updated_at, deleted_at
```

#### 4. جدول المركبات (vehicles)
```sql
- id (Primary Key)
- name (اسم المركبة)
- type (نوع المركبة)
- brand (الماركة)
- model (الموديل)
- year (السنة)
- license_plate (رقم اللوحة)
- capacity (السعة)
- description (الوصف)
- city_id (معرف المدينة)
- driver_name (اسم السائق)
- driver_phone (هاتف السائق)
- driver_license (رخصة السائق)
- price_per_hour (السعر للساعة)
- price_per_day (السعر لليوم)
- currency (العملة)
- fuel_type (نوع الوقود)
- transmission (ناقل الحركة)
- features (المميزات - JSON)
- images (الصور - JSON)
- status (الحالة)
- active (نشط)
- enabled (مفعل)
- insurance_expiry (انتهاء التأمين)
- registration_expiry (انتهاء التسجيل)
- last_maintenance (آخر صيانة)
- next_maintenance (الصيانة القادمة)
- notes (ملاحظات)
- created_at, updated_at, deleted_at
```

#### 5. جدول المرشدين (guides)
```sql
- id (Primary Key)
- name (الاسم)
- email (البريد الإلكتروني)
- phone (الهاتف)
- nationality (الجنسية)
- languages (اللغات - JSON)
- specializations (التخصصات - JSON)
- experience_years (سنوات الخبرة)
- city_id (معرف المدينة)
- price_per_hour (السعر للساعة)
- price_per_day (السعر لليوم)
- currency (العملة)
- bio (السيرة الذاتية)
- certifications (الشهادات - JSON)
- profile_image (صورة الملف الشخصي)
- status (الحالة)
- active (نشط)
- enabled (مفعل)
- rating (التقييم)
- total_ratings (إجمالي التقييمات)
- availability_schedule (جدول التوفر - JSON)
- emergency_contact (جهة الاتصال الطارئ)
- emergency_phone (هاتف الطوارئ)
- notes (ملاحظات)
- created_at, updated_at, deleted_at
```

#### 6. جدول المدفوعات (payments)
```sql
- id (Primary Key)
- invoice_id (معرف الفاتورة)
- booking_id (معرف الحجز)
- gateway (بوابة الدفع)
- amount (المبلغ)
- status (الحالة: paid, pending, not_paid)
- paid_at (تاريخ الدفع)
- transaction_request (طلب المعاملة - JSON)
- transaction_verification (تحقق المعاملة - JSON)
- notes (ملاحظات)
- reference_number (رقم المرجع)
- created_at, updated_at
```

#### 7. جدول حجز الموارد (resource_bookings)
```sql
- id (Primary Key)
- booking_file_id (معرف ملف الحجز)
- resource_type (نوع المورد: hotel, vehicle, guide, representative)
- resource_id (معرف المورد)
- start_date (تاريخ البداية)
- end_date (تاريخ النهاية)
- start_time (وقت البداية)
- end_time (وقت النهاية)
- quantity (الكمية)
- unit_price (سعر الوحدة)
- total_price (السعر الإجمالي)
- currency (العملة)
- status (الحالة)
- special_requirements (متطلبات خاصة - JSON)
- notes (ملاحظات)
- created_at, updated_at
```

#### 8. جدول موارد الاستفسار (inquiry_resources)
```sql
- id (Primary Key)
- inquiry_id (معرف الاستفسار)
- resource_type (نوع المورد)
- resource_id (معرف المورد)
- resource_name (اسم المورد)
- added_by (أضيف بواسطة)
- start_at (يبدأ في)
- end_at (ينتهي في)
- check_in (تسجيل الوصول)
- check_out (تسجيل المغادرة)
- number_of_rooms (عدد الغرف)
- number_of_adults (عدد البالغين)
- number_of_children (عدد الأطفال)
- rate_per_adult (السعر للبالغ)
- rate_per_child (السعر للطفل)
- price_type (نوع السعر)
- original_price (السعر الأصلي)
- new_price (السعر الجديد)
- increase_percent (نسبة الزيادة)
- effective_price (السعر الفعال)
- currency (العملة)
- price_note (ملاحظة السعر)
- resource_details (تفاصيل المورد - JSON)
- created_at, updated_at
```

#### 9. جدول الدردشة (chats)
```sql
- id (Primary Key)
- inquiry_id (معرف الاستفسار)
- sender_id (معرف المرسل)
- recipient_id (معرف المستقبل)
- message (الرسالة)
- read_at (تاريخ القراءة)
- created_at, updated_at
```

## تدفق العمل (Workflow)

### 1. عملية الاستفسار
1. **إنشاء الاستفسار**: يتم إنشاء استفسار جديد من العميل أو الموظف
2. **تعيين المستخدمين**: يتم تعيين الاستفسار لمستخدمين محددين حسب الدور
3. **إضافة الموارد**: يتم ربط الموارد السياحية المناسبة بالاستفسار
4. **التواصل**: يتم التواصل عبر نظام الدردشة الداخلي
5. **التأكيد**: عند التأكيد، يتم إنشاء ملف حجز تلقائياً

### 2. عملية الحجز
1. **إنشاء ملف الحجز**: يتم إنشاء ملف PDF للحجز المؤكد
2. **تتبع التقدم**: يتم استخدام قائمة المهام لتتبع تقدم الحجز
3. **إدارة الموارد**: يتم تعيين الموارد للحجز مع التحقق من التوفر
4. **المتابعة**: يتم متابعة الحجز حتى الإنجاز

### 3. عملية الدفع
1. **إنشاء الدفع**: يتم إنشاء سجل دفع جديد
2. **معالجة الدفع**: يتم معالجة الدفع عبر البوابة المحددة
3. **تحديث الحالة**: يتم تحديث حالة الدفع والحجز
4. **الإشعارات**: يتم إرسال إشعارات للعميل والموظفين

## الأدوار والصلاحيات

### 1. المدير (Admin)
- **صلاحيات كاملة**: جميع الصلاحيات في النظام
- **إدارة المستخدمين**: إنشاء وإدارة حسابات المستخدمين
- **إدارة الأدوار**: إدارة الأدوار والصلاحيات
- **التقارير**: الوصول لجميع التقارير والإحصائيات

### 2. المبيعات (Sales)
- **إدارة الاستفسارات**: إنشاء وتعديل الاستفسارات
- **تعديل برنامج الرحلة**: إمكانية تعديل برنامج الرحلة
- **إضافة الموارد**: إضافة موارد للاستفسارات
- **الدردشة**: التواصل مع الفريق عبر الدردشة

### 3. الحجوزات (Reservation)
- **تأكيد الاستفسارات**: تأكيد الاستفسارات مع تفاصيل الدفع
- **إدارة الحجوزات**: إدارة ملفات الحجز
- **تتبع التقدم**: تحديث قائمة المهام للحجوزات
- **الدردشة**: التواصل مع فريق المبيعات

### 4. العمليات (Operator)
- **إدارة الموارد**: إدارة الموارد السياحية
- **تعيين الموارد**: تعيين الموارد للحجوزات
- **تتبع التوفر**: مراقبة توفر الموارد
- **الدردشة**: التواصل مع الفريق

## النظام التقني المتقدم

### 1. نظام الأحداث (Event System)
- **InquiryConfirmed**: حدث تأكيد الاستفسار
- **NewInquiryCreated**: حدث إنشاء استفسار جديد
- **ChatMessageSent**: حدث إرسال رسالة دردشة

### 2. المستمعون (Listeners)
- **GenerateBookingFileListener**: إنشاء ملف الحجز عند التأكيد
- **SendChatMessageNotification**: إرسال إشعارات الدردشة

### 3. الخدمات (Services)
- **BookingService**: خدمة إدارة الحجوزات
- **FinanceService**: خدمة إدارة المالية
- **ResourceAssignmentService**: خدمة تعيين الموارد

### 4. السمات (Traits)
- **HasAuditLog**: تسجيل الأنشطة تلقائياً
- **HasStatuses**: إدارة الحالات
- **CurrencyConversion**: تحويل العملات

## واجهات برمجة التطبيقات (API)

### 1. API المصادقة
- **POST /api/auth/login**: تسجيل الدخول
- **POST /api/auth/register**: التسجيل
- **POST /api/auth/password/forget**: نسيان كلمة المرور
- **POST /api/auth/password/reset**: إعادة تعيين كلمة المرور

### 2. API الملف الشخصي
- **GET /api/profile/me**: الحصول على بيانات الملف الشخصي
- **PATCH /api/profile**: تحديث الملف الشخصي
- **POST /api/profile/change/image**: تغيير صورة الملف الشخصي

### 3. API المدفوعات
- **GET /api/payments/fawaterk/methods**: طرق الدفع المتاحة
- **GET /api/payments/paypal/capture**: التقاط دفع PayPal
- **GET /api/payments/paypal/cancel**: إلغاء دفع PayPal

### 4. API التقويم
- **GET /api/calendar/availability**: التحقق من توفر الموارد

## التكوين والإعداد

### 1. متغيرات البيئة (.env)
```env
# إعدادات قاعدة البيانات
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tourism_management
DB_USERNAME=root
DB_PASSWORD=

# إعدادات البريد الإلكتروني
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"

# إعدادات WhatsApp
WHATSAPP_API_URL=https://graph.facebook.com
WHATSAPP_TOKEN=your-whatsapp-token
WHATSAPP_PHONE_NUMBER_ID=your-phone-number-id

# إعدادات SMS
TWILIO_SID=your-twilio-sid
TWILIO_TOKEN=your-twilio-token
TWILIO_FROM=+1234567890

# إعدادات PayPal
PAYPAL_CLIENT_ID=your-paypal-client-id
PAYPAL_CLIENT_SECRET=your-paypal-client-secret
PAYPAL_MODE=sandbox
```

### 2. تثبيت النظام
```bash
# تثبيت التبعيات
composer install
npm install && npm run dev

# إعداد البيئة
cp .env.example .env
php artisan key:generate

# إعداد قاعدة البيانات
php artisan migrate
php artisan db:seed

# إنشاء رابط التخزين
php artisan storage:link

# تشغيل النظام
php artisan serve
```

## الأمان والحماية

### 1. المصادقة والتفويض
- **Laravel Passport**: للمصادقة API
- **Spatie Permission**: لإدارة الصلاحيات
- **CSRF Protection**: حماية من هجمات CSRF
- **Rate Limiting**: تحديد معدل الطلبات

### 2. حماية البيانات
- **تشفير كلمات المرور**: تشفير آمن لكلمات المرور
- **Soft Deletes**: حذف آمن للبيانات
- **Audit Logging**: تسجيل جميع الأنشطة
- **Input Validation**: التحقق من صحة البيانات المدخلة

### 3. الأمان الإضافي
- **CORS Middleware**: إدارة طلبات CORS
- **Encrypted Cookies**: تشفير ملفات تعريف الارتباط
- **Secure Headers**: رؤوس أمان إضافية

## الصيانة والدعم

### 1. النسخ الاحتياطي
- **نسخ احتياطي لقاعدة البيانات**: نسخ احتياطي منتظم
- **نسخ احتياطي للملفات**: نسخ احتياطي لملفات التخزين
- **مراقبة الأداء**: مراقبة أداء النظام

### 2. التحديثات
- **تحديثات Laravel**: تحديثات أمنية ووظيفية
- **تحديثات المكتبات**: تحديثات المكتبات الخارجية
- **تحديثات قاعدة البيانات**: تحديثات هيكل قاعدة البيانات

### 3. المراقبة
- **Laravel Telescope**: مراقبة الأداء والأخطاء
- **Queue Monitor**: مراقبة المهام في الخلفية
- **Activity Log**: تسجيل الأنشطة والعمليات

## التطوير المستقبلي

### 1. الميزات المخططة
- **تطبيق الهاتف المحمول**: تطبيق للهواتف الذكية
- **نظام الحجز المباشر**: حجز مباشر من العملاء
- **تحليلات متقدمة**: تحليلات وأتمتة ذكية
- **تكامل مع أنظمة خارجية**: تكامل مع أنظمة الحجز العالمية

### 2. التحسينات التقنية
- **تحسين الأداء**: تحسين سرعة النظام
- **قابلية التوسع**: دعم عدد أكبر من المستخدمين
- **الأمان المتقدم**: ميزات أمان إضافية
- **واجهة مستخدم محسنة**: تحسين تجربة المستخدم

## الخلاصة

نظام إدارة السياحة هو حل متكامل وشامل لإدارة عمليات السياحة والسفر. يوفر النظام جميع الأدوات اللازمة لإدارة الاستفسارات والحجوزات والموارد السياحية والمدفوعات مع نظام إشعارات متقدم وأمان عالي.

النظام مبني على أحدث التقنيات ويوفر واجهة سهلة الاستخدام للمستخدمين مع إمكانيات متقدمة للمطورين. يمكن تخصيص النظام وتوسيعه حسب احتياجات كل شركة سياحية.

---

**تم إنشاء هذا التوثيق بواسطة**: نظام التوثيق التلقائي  
**تاريخ الإنشاء**: {{ date('Y-m-d') }}  
**الإصدار**: 1.0  
**اللغة**: العربية
