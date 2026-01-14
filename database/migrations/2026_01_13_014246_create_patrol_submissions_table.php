<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('patrol_submissions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('barcode')->nullable(); // hasil scan titik patroli
            $table->string('area'); // luar/smoking/balkon

            $table->string('photo_path'); // storage path
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->text('address')->nullable(); // multi-line digabung

            $table->timestamp('submitted_at')->useCurrent(); // timestamp server
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patrol_submissions');
    }
};
