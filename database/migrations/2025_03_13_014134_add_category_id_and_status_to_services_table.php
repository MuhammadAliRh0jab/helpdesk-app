<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->default(1)->after('svc_name'); // 1 = Pemerintah, 2 = Publik
            $table->enum('status', ['active', 'inactive'])->default('active')->after('category_id');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['category_id', 'status']);
        });
    }
};