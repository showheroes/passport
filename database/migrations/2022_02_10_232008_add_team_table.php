<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table(
            'teams',
            function (Blueprint $table) {
                $table->integer('owner_id');
                $table->integer('default_locale')->nullable();
                $table->text('meta_data')->nullable();
                $table->timestamp('deleted_at')->nullable()->after('updated_at');
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
            'teams',
            function (Blueprint $table) {
                $table->dropColumn(
                    'owner_id',
                    'default_locale',
                    'meta_data',
                    'deleted_at'
                );
            }
        );
    }
};
