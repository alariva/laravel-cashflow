<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cashflow_records', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->enum('flow', ['in', 'out']);
            $table->string('name');
            $table->string('currency')->default('ARS');
            $table->json('details');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cashflow_records');
    }
};
