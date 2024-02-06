<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('device_notifications_logs', function (Blueprint $table) {
            $table->id();
            $table->integer("company_id");
            $table->integer("branch_id");
            $table->integer("notification_id");
            $table->integer("notification_manager_id");
            $table->string("email")->nullable();
            $table->string("whatsapp_number")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('device_notifications_logs');
    }
};
