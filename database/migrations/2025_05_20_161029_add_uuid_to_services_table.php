<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUuidToServicesTable extends Migration
{
    public function up()
    {
        Schema::table('services', function (Blueprint $table) {
            $table->uuid('uuid')->unique()->after('id');
        });

        // Optional: Populate existing records with UUIDs
        \App\Models\Service::all()->each(function ($service) {
            $service->uuid = \Ramsey\Uuid\Uuid::uuid4()->toString();
            $service->save();
        });
    }

    public function down()
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
    }
}