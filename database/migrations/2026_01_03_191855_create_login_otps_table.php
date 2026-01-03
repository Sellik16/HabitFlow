<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('login_otps', function (Blueprint $table) {
            $table->id();
            $table->uuid('challenge_id')->index();

            $table->string('email')->index();
            $table->string('code_hash', 64)->index(); // sha256 hex

            $table->timestamp('expires_at')->index();
            $table->timestamp('used_at')->nullable()->index();

            $table->unsignedTinyInteger('attempts')->default(0);

            $table->string('ip')->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('login_otps');
    }
};
