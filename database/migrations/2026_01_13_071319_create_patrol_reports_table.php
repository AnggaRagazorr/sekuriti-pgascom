<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('patrol_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // otomatis
            $table->date('report_date');              
            $table->string('report_day');            
            $table->dateTime('submitted_time');      

            // input user
            $table->text('situasi')->nullable();
            $table->text('aght')->nullable();
            $table->string('cuaca')->nullable();
            $table->string('pdam')->nullable();
            $table->string('personel_wfo')->nullable();      
            $table->string('personel_tambahan')->nullable();

            $table->timestamps();

            // biar 1 user cuma 1 report per hari (kalau nanti mau per shift, ini bisa diubah)
            $table->unique(['user_id', 'report_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patrol_reports');
    }
};
