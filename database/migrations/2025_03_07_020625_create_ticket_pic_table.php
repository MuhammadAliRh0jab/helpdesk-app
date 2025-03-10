<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketPicTable extends Migration
{
    public function up()
    {
        Schema::create('ticket_pic', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ticket_id');
            $table->unsignedBigInteger('pic_id');
            $table->string('pic_stats')->default('active'); // Status penugasan PIC
            $table->timestamps();

            $table->foreign('ticket_id')->references('id')->on('tickets')->onDelete('cascade');
            $table->foreign('pic_id')->references('id')->on('pics')->onDelete('cascade');

            // Pastikan kombinasi ticket_id dan pic_id unik
            $table->unique(['ticket_id', 'pic_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('ticket_pic');
    }
}