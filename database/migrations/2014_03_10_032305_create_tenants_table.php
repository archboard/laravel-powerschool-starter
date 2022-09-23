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
            $table->json('sis_config')->nullable();
            $table->string('domain')->nullable();
            $table->string('custom_domain')->nullable();
            $table->boolean('allow_password_auth')->default(false);
            $table->boolean('allow_oidc_login')->default(false);
            $table->dateTime('subscription_started_at')->nullable();
            $table->dateTime('subscription_expires_at')->nullable();
            $table->string('license');
            $table->string('sis_provider')->default(\App\Enums\Sis::PS->value);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tenants');
    }
}
