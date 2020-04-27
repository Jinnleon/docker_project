<?php

use App\GlobalValues;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->string(GlobalValues::SCAN_DIR);
            $table->string(GlobalValues::PATH);
            $table->integer(GlobalValues::DEPTH);
            $table->string(GlobalValues::FILE_NAME);
            $table->string(GlobalValues::EXTENSION);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('files');
    }
}
