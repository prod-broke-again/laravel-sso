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
        Schema::create(config('sso.tables.tokens', 'sso_tokens'), function (Blueprint $table) {
            $table->id();
            $table->string('token')->unique(); // SSO token
            $table->unsignedBigInteger('user_id'); // user being shared
            $table->string('partner_identifier'); // target partner
            $table->string('source_app'); // source application identifier
            $table->timestamp('expires_at'); // when token expires
            $table->boolean('used')->default(false); // whether token has been used
            $table->timestamp('used_at')->nullable(); // when token was used
            $table->json('user_data')->nullable(); // encrypted user data
            $table->json('metadata')->nullable(); // additional data
            $table->timestamps();

            $table->index(['token', 'used']);
            $table->index(['user_id', 'partner_identifier']);
            $table->index('expires_at');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(config('sso.tables.tokens', 'sso_tokens'));
    }
};
