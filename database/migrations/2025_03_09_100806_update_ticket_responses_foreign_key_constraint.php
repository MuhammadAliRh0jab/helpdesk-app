<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Hapus foreign key yang ada pada ticket_id_quote
        Schema::table('ticket_responses', function (Blueprint $table) {
            $table->dropForeign(['ticket_id_quote']);
        });

        // Tambahkan foreign key baru yang merujuk ke ticket_responses.id
        Schema::table('ticket_responses', function (Blueprint $table) {
            $table->foreign('ticket_id_quote')->references('id')->on('ticket_responses')->onDelete('set null');
        });
    }

    public function down(): void
    {
        // Kembalikan ke kondisi semula jika rollback
        Schema::table('ticket_responses', function (Blueprint $table) {
            $table->dropForeign(['ticket_id_quote']);
            $table->foreignId('ticket_id_quote')->constrained('tickets')->onDelete('set null');
        });
    }
};