<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableFormProfession extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('form_professions', function (Blueprint $table) {
            $table->id();
            $table->string('label')->nullable(false)->comment('Лэйбл');
            $table->boolean('visible')->nullable(false)->default(true)->comment('Видимый');
            $table->integer('sort')->nullable(false)->default(0)->comment('Приоритет');
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
        Schema::dropIfExists('table_form_profession');
    }
}
