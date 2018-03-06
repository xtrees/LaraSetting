<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('group', 50);
            $table->string('key', 50);
            $table->text('value');
            $table->string('display_name', 100)->nullable();
            $table->string('type', 100)->nullable();
            $table->text('details')->nullable();
            $table->timestamps();
            $table->unique(['group', 'key']);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings');
    }
}
