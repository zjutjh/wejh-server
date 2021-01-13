<?php

use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Teacher;

class TeacherTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {  
        //
        // ini_set('memory_limit', '512M');
        // $filePath = 'storage/excel/teacher.xls';
        // Excel::load($filePath, function($reader) {
        //     $reader->each(function ($data) {
        //         $teachers = $data->toArray();
        //         foreach ($teachers as $key => $teacher) {
        //             Teacher::create($teacher);
        //         }
        //     });
        // });
    }
}
