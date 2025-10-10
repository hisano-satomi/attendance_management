<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFixesBreakRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fixes_break_requests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('fixes_attendance_request_id')->constrained('fixes_attendance_requests');
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
        Schema::dropIfExists('fixes_break_requests');
    }
}
