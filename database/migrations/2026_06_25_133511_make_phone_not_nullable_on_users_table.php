<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 先将已有的 null 值填充为空字符串，避免修改约束时报错
        DB::table('users')->whereNull('phone')->update(['phone' => '']);

        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 20)->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 20)->nullable()->change();
        });
    }
};
