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
        $table->renameColumn('applies', 'availableTo');
    });
}

public function down()
{
    Schema::table('discounts', function (Blueprint $table) {
        $table->renameColumn('availableTo', 'applies');
    });
}

};
