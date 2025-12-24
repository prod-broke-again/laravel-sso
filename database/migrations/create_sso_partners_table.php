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
        Schema::create(config('sso.tables.partners', 'sso_partners'), function (Blueprint $table) {
            $table->id();
            $table->string('identifier')->unique(); // partner identifier (e.g., 'partner1')
            $table->string('name'); // display name
            $table->string('url'); // base URL of partner
            $table->text('public_key')->nullable(); // public key for encryption
            $table->text('private_key')->nullable(); // private key for decryption
            $table->boolean('enabled')->default(true);
            $table->json('metadata')->nullable(); // additional configuration
            $table->timestamps();

            $table->index(['identifier', 'enabled']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(config('sso.tables.partners', 'sso_partners'));
    }
};
