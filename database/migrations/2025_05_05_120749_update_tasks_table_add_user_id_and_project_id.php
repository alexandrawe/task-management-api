<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->nullable()
                ->after('deadline')
                ->constrained();

            $table->foreignId('project_id')
                ->nullable()
                ->after('created_by')
                ->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign('tasks_user_id_foreign');
            $table->dropForeign('tasks_project_id_foreign');

            $table->dropColumn('user_id');
            $table->dropColumn('project_id');
        });
    }
};
