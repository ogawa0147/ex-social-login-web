<?php

use Illuminate\Database\Seeder;
use App\Models\Define;

class DefineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Define::truncate();

        Define::create([
            'key'   => 'signup.mail.subject',
            'value' => 'test',
        ]);
        Define::create([
            'key'   => 'signup.mail.body',
            'value' => "TESTTESTTEST\n\nhttp://example.com/signup/activate?token=__ACTIVATE_TOKEN__\n\nhogehoge",
        ]);
        Define::create([
            'key'   => 'signup.mail.expire.seconds',
            'value' => '300',
        ]);
    }
}
