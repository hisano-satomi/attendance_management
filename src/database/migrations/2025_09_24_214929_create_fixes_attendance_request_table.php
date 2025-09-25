<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFixesAttendanceRequestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fixes_attendance_request', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('attendance_id')->constrained('attendance');
            $table->timestamp('work_start')->nullable();
            $table->timestamp('work_stop')->nullable();
            $table->string('request_reason');
            $table->enum('status', ['pending', 'approved']);
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
        Schema::dropIfExists('fixes_attendance_request');
    }
}
