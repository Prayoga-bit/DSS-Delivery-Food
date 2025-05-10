<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComparisonMatrixTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comparison_matrix', function (Blueprint $table) {
            $table->id();
            $table->foreignId('criteria1_id')->constrained('criterias')->onDelete('cascade');
            $table->foreignId('criteria2_id')->constrained('criterias')->onDelete('cascade');
            $table->double('value'); // Comparison value (1-9)
            $table->timestamps();
            
            // Ensure unique pairs of criteria
            $table->unique(['criteria1_id', 'criteria2_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comparison_matrix');
    }
}