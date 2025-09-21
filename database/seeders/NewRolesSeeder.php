<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class NewRolesSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'Sales' => 'مبيعات - إدارة الاستفسارات وتحويلها لحجوزات',
            'Reservation' => 'حجوزات - الرد على طلبات Sales وإدارة تفاصيل الحجوزات',
            'Operation' => 'تشغيل - إدارة العمليات التشغيلية والموارد',
            'Finance' => 'مالية - إدارة الماليات والدفعات والمستحقات',
            'Admin' => 'أدمن - إدارة شاملة للنظام والمستخدمين'
        ];

        foreach ($roles as $roleName => $description) {
            Role::updateOrCreate(
                ['name' => $roleName],
                [
                    'name' => $roleName,
                    'guard_name' => 'web'
                ]
            );
        }
    }
}
