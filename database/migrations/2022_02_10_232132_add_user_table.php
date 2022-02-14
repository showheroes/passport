<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table(
            'users',
            function (Blueprint $table) {
                $table->string('timezone')
                    ->nullable();

                $table->string('photo_url')
                    ->nullable();

                $table->boolean('email_verified')
                    ->nullable();

                $table->integer('level')
                    ->nullable();

                $table->boolean('is_blocked')
                    ->nullable();

                $table->text('meta_data')
                    ->nullable();

                $table->text('default_locale')
                    ->nullable();

                $table->timestamp('last_read_announcements_at')->nullable()->after('updated_at');
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(
            'users',
            function (Blueprint $table) {
                $table->dropColumn(
                    'timezone',
                    'photo_url',
                    'email_verified',
                    'level',
                    'is_blocked',
                    'meta_data',
                    'default_locale',
                    'last_read_announcements_at'
                );
            }
        );
    }
};
