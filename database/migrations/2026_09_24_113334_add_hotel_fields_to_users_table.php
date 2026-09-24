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
        Schema::table('users', function (Blueprint $table) {
            $table->string('national_id')->nullable()->unique()->after('email');
            $table->string('avatar_image')->nullable()->after('national_id');
            $table->string('mobile')->nullable()->after('avatar_image');
            $table->string('country')->nullable()->after('mobile');
            $table->string('gender')->nullable()->after('country');
            $table->boolean('is_approved')->default(false)->after('gender');
            $table->foreignId('approved_by_id')->nullable()->constrained('users')->nullOnDelete()->after('is_approved');
            $table->timestamp('approved_at')->nullable()->after('approved_by_id');
            $table->foreignId('created_by_id')->nullable()->constrained('users')->nullOnDelete()->after('approved_at');
            $table->timestamp('last_login_at')->nullable()->after('created_by_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['approved_by_id']);
            $table->dropForeign(['created_by_id']);
            $table->dropColumn([
                'national_id',
                'avatar_image',
                'mobile',
                'country',
                'gender',
                'is_approved',
                'approved_by_id',
                'approved_at',
                'created_by_id',
                'last_login_at',
            ]);
        });
    }
};
