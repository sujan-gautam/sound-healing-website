<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterRowsToTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('funds', function (Blueprint $table) {
            $table->integer('payment_id')->nullable();
        });


        Schema::table('payout_logs', function (Blueprint $table) {
            $table->string('response_id')->nullable();
            $table->string('currency_code')->nullable();
            $table->text('meta_field')->nullable();
            $table->text('last_error')->nullable();
        });


        Schema::table('payout_methods', function (Blueprint $table) {
            $table->string('code')->nullable();
            $table->string('description')->nullable();
            $table->text('bank_name')->nullable();
            $table->text('banks')->nullable();
            $table->text('parameters')->nullable();
            $table->text('extra_parameters')->nullable();
            $table->text('currency_lists')->nullable();
            $table->text('supported_currency')->nullable();
            $table->text('convert_rate')->nullable();
            $table->tinyInteger('is_automatic')->default(0)->nullable();
            $table->tinyInteger('is_sandbox')->default(0)->nullable();
            $table->tinyInteger('environment')->default(0)->nullable();
            $table->tinyInteger('is_auto_update')->default(1)->nullable();
        });

        Schema::create('razorpay_contacts', function (Blueprint $table) {
            $table->id();
            $table->string('contact_id')->nullable();
            $table->string('entity')->nullable();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
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
        Schema::table('funds', function (Blueprint $table) {
            $table->dropColumn('payment_id');
        });

        Schema::table('payout_logs', function (Blueprint $table) {
            $table->dropColumn('response_id');
            $table->dropColumn('currency_code');
            $table->dropColumn('meta_field');
            $table->dropColumn('last_error');
        });

        Schema::table('payout_methods', function (Blueprint $table) {
            $table->dropColumn('code');
            $table->dropColumn('description');
            $table->dropColumn('bank_name');
            $table->dropColumn('banks');
            $table->dropColumn('parameters');
            $table->dropColumn('extra_parameters');
            $table->dropColumn('currency_lists');
            $table->dropColumn('supported_currency');
            $table->dropColumn('convert_rate');
            $table->dropColumn('is_automatic');
            $table->dropColumn('is_sandbox');
            $table->dropColumn('environment');
            $table->dropColumn('is_auto_update');
        });


        Schema::dropIfExists('razorpay_contacts');

    }
}
