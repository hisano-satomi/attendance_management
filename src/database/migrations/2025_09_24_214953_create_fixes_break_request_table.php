<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFixesBreakRequestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fixes_break_request', function (Blueprint $table) {
            $table->bigInteger('id');
            $table->bigInteger('fixes_attendance_request_id')->unsigned();
            $table->foreign('fixes_attendance_request_id')->references('id')->on('fixes_attendance_request');
            $table->timestamp('break_start')->nullable();
            $table->timestamp('break_stop')->nullable();
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
        Schema::dropIfExists('fixes_break_request');
    }
}
