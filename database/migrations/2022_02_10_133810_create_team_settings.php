<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('team_settings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('team_id')->index();
            $table->text('ui_config');
            $table->boolean('ifactory_access');
            $table->boolean('adhero_access');
            $table->boolean('apollo_access');
            $table->boolean('creative_studio_access');
            $table->boolean('redirect_to_platform_enabled');
            $table->text('permissions');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('team_settings');
    }
};
