<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('discounts', function (Blueprint $table) {
            // Change the 'applies' column from ENUM to string
            $table->string('discountType')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('discounts', function (Blueprint $table) {
            // Optionally, revert the change back to ENUM
            $table->enum('discountType', ['NONE', 'ALL', 'FAMILY', 'REPEAT'])->change();
        });
    }
};
