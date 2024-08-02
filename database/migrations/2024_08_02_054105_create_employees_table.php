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
        Schema::create('employees', function (Blueprint $table) {
            $table->string('id', 50)->primary();
            $table->string('first_name', 20)->notNull();
            $table->string('last_name', 20)->notNull();
            $table->enum('gender', ['male', 'female'])->notNull();
            $table->string('email', 50)->unique()->notNull();
            $table->string('phone_number', 50)->nullable();
            $table->text('hire_date')->notNull();
            $table->text('job_title')->notNull();
            $table->text('department_id')->notNull();
            $table->timestamps(); // will create created_at and updated_at, NOTED!

            // Foreign key constraint
            $table->foreign('department_id')->references('id')->on('departments');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employees');
    }
};
