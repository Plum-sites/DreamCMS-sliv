<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSnooperTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('snooper', function (Blueprint $table) {
            $table->id();

            $table->string('uuid');
            $table->string('username');

            $table->string('version');

            $table->string('java_version');
            $table->boolean('java64');

            $table->integer('display_frequency');
            $table->string('opengl_vendor');
            $table->string('gpu');
            $table->string('opengl_version');

            $table->boolean('shaders_supported');

            $table->string('os_name');
            $table->string('os_version');
            $table->string('os_architecture');

            $table->integer('memory_total');
            $table->integer('memory_max');
            $table->integer('memory_free');
            $table->integer('memory');

            $table->integer('cpu_cores');
            $table->string('cpu');

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
        Schema::dropIfExists('snooper');
    }
}
