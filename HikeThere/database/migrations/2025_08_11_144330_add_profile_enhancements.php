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
        // Add profile enhancements to users table
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'profile_picture')) {
                $table->string('profile_picture')->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('profile_picture');
            }
            if (!Schema::hasColumn('users', 'bio')) {
                $table->text('bio')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('users', 'location')) {
                $table->string('location')->nullable()->after('bio');
            }
            if (!Schema::hasColumn('users', 'birth_date')) {
                $table->date('birth_date')->nullable()->after('location');
            }
            if (!Schema::hasColumn('users', 'gender')) {
                $table->enum('gender', ['male', 'female', 'other', 'prefer_not_to_say'])->nullable()->after('birth_date');
            }
            if (!Schema::hasColumn('users', 'hiking_preferences')) {
                $table->json('hiking_preferences')->nullable()->after('gender');
            }
            if (!Schema::hasColumn('users', 'emergency_contact_name')) {
                $table->string('emergency_contact_name')->nullable()->after('hiking_preferences');
            }
            if (!Schema::hasColumn('users', 'emergency_contact_phone')) {
                $table->string('emergency_contact_phone')->nullable()->after('emergency_contact_name');
            }
            if (!Schema::hasColumn('users', 'emergency_contact_relationship')) {
                $table->string('emergency_contact_relationship')->nullable()->after('emergency_contact_phone');
            }
        });

        // Add profile enhancements to organization_profiles table
        Schema::table('organization_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('organization_profiles', 'profile_picture')) {
                $table->string('profile_picture')->nullable()->after('organization_description');
            }
            if (!Schema::hasColumn('organization_profiles', 'website')) {
                $table->string('website')->nullable()->after('profile_picture');
            }
            if (!Schema::hasColumn('organization_profiles', 'social_media_facebook')) {
                $table->string('social_media_facebook')->nullable()->after('website');
            }
            if (!Schema::hasColumn('organization_profiles', 'social_media_instagram')) {
                $table->string('social_media_instagram')->nullable()->after('social_media_facebook');
            }
            if (!Schema::hasColumn('organization_profiles', 'social_media_twitter')) {
                $table->string('social_media_twitter')->nullable()->after('social_media_instagram');
            }
            if (!Schema::hasColumn('organization_profiles', 'mission_statement')) {
                $table->text('mission_statement')->nullable()->after('social_media_twitter');
            }
            if (!Schema::hasColumn('organization_profiles', 'services_offered')) {
                $table->text('services_offered')->nullable()->after('mission_statement');
            }
            if (!Schema::hasColumn('organization_profiles', 'operating_hours')) {
                $table->string('operating_hours')->nullable()->after('services_offered');
            }
            if (!Schema::hasColumn('organization_profiles', 'contact_person')) {
                $table->string('contact_person')->nullable()->after('operating_hours');
            }
            if (!Schema::hasColumn('organization_profiles', 'contact_position')) {
                $table->string('contact_position')->nullable()->after('contact_person');
            }
            if (!Schema::hasColumn('organization_profiles', 'specializations')) {
                $table->json('specializations')->nullable()->after('contact_position');
            }
            if (!Schema::hasColumn('organization_profiles', 'founded_year')) {
                $table->string('founded_year')->nullable()->after('specializations');
            }
            if (!Schema::hasColumn('organization_profiles', 'team_size')) {
                $table->string('team_size')->nullable()->after('founded_year');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove profile enhancements from users table
        Schema::table('users', function (Blueprint $table) {
            $columns = [
                'profile_picture',
                'phone',
                'bio',
                'location',
                'birth_date',
                'gender',
                'hiking_preferences',
                'emergency_contact_name',
                'emergency_contact_phone',
                'emergency_contact_relationship'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        // Remove profile enhancements from organization_profiles table
        Schema::table('organization_profiles', function (Blueprint $table) {
            $columns = [
                'profile_picture',
                'website',
                'social_media_facebook',
                'social_media_instagram',
                'social_media_twitter',
                'mission_statement',
                'services_offered',
                'operating_hours',
                'contact_person',
                'contact_position',
                'specializations',
                'founded_year',
                'team_size'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('organization_profiles', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
