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
        Schema::table('transactions', function (Blueprint $table) {
            $table->decimal('finalAmount', 10, 2)->nullable()->after('services'); // Adjust column name accordingly
            $table->decimal('discountAmount', 10, 2)->nullable()->after('finalAmount');
        });
    }

    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('finalAmount');
            $table->dropColumn('discountAmount');
        });
    }
};
