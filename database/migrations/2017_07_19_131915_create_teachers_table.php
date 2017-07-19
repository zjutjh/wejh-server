<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTeachersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teachers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('tid')->unique()->index()->comment("教工号");
            $table->string('name')->index()->comment("姓名");
            $table->string('birthday')->nullable()->default('')->comment("生日");
            $table->string('gender')->nullable()->default('')->comment("性别");
            $table->string('primary_department')->nullable()->default('')->comment("一级部门");
            $table->string('secondary_department')->nullable()->default('')->comment("二级部门");
            $table->string('tertiary_department')->nullable()->default('')->comment("三级部门");
            $table->string('ethnic_group')->nullable()->default('')->comment("民族");
            $table->string('native_place')->nullable()->default('')->comment("籍贯");
            $table->string('political_status')->nullable()->default('')->comment("政治面貌");
            $table->string('work_start_date')->nullable()->default('')->comment("工作时间");
            $table->string('come_school_date')->nullable()->default('')->comment("来校时间");
            $table->string('compile_category')->nullable()->default('')->comment("编制类别");
            $table->string('job_category')->nullable()->default('')->comment("岗位类别");
            $table->string('job_property')->nullable()->default('')->comment("岗位性质");
            $table->string('job_level')->nullable()->default('')->comment("所聘岗位等级");
            $table->string('second_job')->nullable()->default('')->comment("双肩挑岗");
            $table->string('is_part_time')->nullable()->default('')->comment("是否兼任教师");
            $table->string('is_phd')->nullable()->default('')->comment("是否硕博导");
            $table->string('identity')->nullable()->default('')->comment("人员身份");
            $table->string('education')->nullable()->default('')->comment("最高学历");
            $table->string('graduated_school')->nullable()->default('')->comment("毕业学校");
            $table->string('graduation_date')->nullable()->default('')->comment("毕业时间");
            $table->string('major')->nullable()->default('')->comment("所学专业");
            $table->string('degree')->nullable()->default('')->comment("最高学位");
            $table->string('degree_school')->nullable()->default('')->comment("授学位学校");
            $table->string('degree_time')->nullable()->default('')->comment("获学位时间");
            $table->string('subject_category')->nullable()->default('')->comment("学科门类");
            $table->string('primary_subject')->nullable()->default('')->comment("一级学科");
            $table->string('secondary_subject')->nullable()->default('')->comment("所属第二学科");
            $table->string('party_job')->nullable()->default('')->comment("党政职务");
            $table->string('party_level')->nullable()->default('')->comment("职务等级");
            $table->string('party_job_start_at')->nullable()->default('')->comment("任职时间");
            $table->string('administration_level')->nullable()->default('')->comment("行政职级");
            $table->string('administration_start_at')->nullable()->default('')->comment("职级评定时间");
            $table->string('technical_titles')->nullable()->default('')->comment("技术职称");
            $table->string('technical_titles_level')->nullable()->default('')->comment("职称等级");
            $table->string('titles_start_at')->nullable()->default('')->comment("职称评定时间");
            $table->string('technical_job_name')->nullable()->default('')->comment("技术职称");
            $table->string('technical_job_level')->nullable()->default('')->comment("职称等级");
            $table->string('technical_job_start_at')->nullable()->default('')->comment("技工聘任时间");
            $table->string('is_abroad')->nullable()->default('')->comment("是否出国进修");
            $table->string('abroad_country')->nullable()->default('')->comment("进修国别");
            $table->string('first_language')->nullable()->default('')->comment("语种一");
            $table->string('second_language')->nullable()->default('')->comment("语种二");
            $table->string('address')->nullable()->default('')->comment("家庭地址");
            $table->string('office_phone')->nullable()->default('')->comment("办公室电话");
            $table->string('zip_code')->nullable()->default('')->comment("家庭邮编");
            $table->string('email')->nullable()->default('')->comment("email地址");
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
        Schema::dropIfExists('teachers');
    }
}
