<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblDisposisiCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_disposisi_comments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('disposisi_id');
            $table->unsignedBigInteger('user_id');
            $table->text('message');
            $table->boolean('is_read')->default(0);
            $table->timestamps();

            // Tambahkan foreign key jika diperlukan
            $table->foreign('disposisi_id')->references('id')->on('tbl_disposisi')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_disposisi_comments');
    }
}
