<?php

use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Student;

class StudentTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        // for ($i = 2013; $i < 2018; $i++) {
        //     $filePath = 'storage/excel/'.$i.'.xls';
        //     echo "import $i's students\n";
        //     Excel::load($filePath, function($reader) use($i) {
        //         $reader->each(function ($data) use($i) {
        //             $student = $data->toArray();
        //             $student['grade'] = $i;
        //             Student::create($student);
        //         });
        //     });
        // }
    }
}
