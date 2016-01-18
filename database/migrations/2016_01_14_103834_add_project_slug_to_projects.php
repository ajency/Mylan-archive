<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Projects;

class AddProjectSlugToProjects extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table( 'projects', function(Blueprint $table) {
            $table->string( 'project_slug');
        } );

        Projects::all()->each( function($project) {
            $projectName = $project->name;
            $project->project_slug = str_slug($projectName);
            $project->save();
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table( 'projects', function(Blueprint $table) {
            $table->dropColumn( 'project_slug' );
        } );
    }
}
