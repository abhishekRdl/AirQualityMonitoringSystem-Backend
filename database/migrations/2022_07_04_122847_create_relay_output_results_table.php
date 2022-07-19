<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRelayOutputResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('relay_output_results', function (Blueprint $table) {
            $table->id();
            $table->text('a_date')->nullable();             
            $table->text('a_time')->nullable();   
            $table->text('companyCode')->nullable();   
            $table->text('deviceId')->nullable();   
            $table->text('sensorId')->nullable();   
            $table->text('sensorTag')->nullable();   
            $table->text('alertType')->nullable();              
            $table->text('severity')->nullable();
            $table->text('statusMessage')->nullable();
            $table->text('relayOutputStatus')->nullable();            
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
        Schema::dropIfExists('relay_output_results');
    }
}
