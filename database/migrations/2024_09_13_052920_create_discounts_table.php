<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiscountsTable extends Migration
{
    public function up()
    {
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('discountCode')->unique();
            $table->enum('discountType', ['PER', 'FIX']);
            $table->integer('value');
            $table->enum('applies', ['ALL', ''])->default('');
            $table->enum('eligibility', ['ALL', ''])->default('');
            $table->boolean('autoApply')->default(false);
            $table->date('expiryOn')->nullable();
            $table->enum('redemptionType', ['ALL', 'PER_USER', 'BOTH']);
            $table->json('redemptionLimit')->nullable(); // For storing JSON data
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('discounts');
    }
}
