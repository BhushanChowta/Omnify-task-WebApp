<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyRedemptionTypeColumnInDiscountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('discounts', function (Blueprint $table) {
            // Change redemptionType column to VARCHAR (string)
            $table->string('redemptionType', 50)->change();
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
            // Optionally, you can revert the change
            $table->enum('redemptionType', ['MAX_USAGE', 'PER_USER', 'BOTH'])->change();
        });
    }
}
