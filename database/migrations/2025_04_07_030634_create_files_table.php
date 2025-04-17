<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

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
            $table->string('filename');
            $table->string('file_type');
            $table->binary('file_contents'); 
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['uploaded', 'processed', 'failed'])->default('uploaded');
            $table->text('processing_notes')->nullable();
            $table->timestamps();
        });

        // Modify the binary column to use LONGBLOB for maximum size
        DB::statement("ALTER TABLE files MODIFY file_contents LONGBLOB");
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