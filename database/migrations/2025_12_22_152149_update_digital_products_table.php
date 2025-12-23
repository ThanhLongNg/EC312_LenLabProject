<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('digital_products', function (Blueprint $table) {
        $table->longText('files')->nullable();
        $table->longText('links')->nullable();
        $table->text('instructions')->nullable();

        $table->boolean('auto_send_email')->default(false);
        $table->text('email_template')->nullable();

        $table->integer('download_limit')->default(3);
        $table->integer('access_days')->default(30);
    });
}
};
