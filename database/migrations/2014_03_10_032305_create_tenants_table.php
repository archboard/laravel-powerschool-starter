<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenantsTable extends Migration
{
    public function up()
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('domain')->unique();
            $table->string('ps_url')->nullable();
            $table->string('ps_client_id')->nullable();
            $table->string('ps_secret')->nullable();
            $table->boolean('allow_password_auth')->default(false);
            $table->dateTime('subscription_started_at')->nullable();
            $table->dateTime('subscription_expires_at')->nullable();
            $table->string('license');
            $table->string('sis_provider')->default(\App\SisProviders\PowerSchoolProvider::class);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tenants');
    }
}
