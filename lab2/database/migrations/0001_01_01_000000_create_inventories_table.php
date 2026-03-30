<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('quantity');
            $table->integer('minimum_stock')->default(5);
            $table->date('expiration_date')->nullable();
            $table->string('category')->nullable();
            $table->timestamps();
            $table->softDeletes(); // IMPORTANT for trash
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
