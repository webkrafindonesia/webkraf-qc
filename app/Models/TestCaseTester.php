<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestCaseTester extends Model
{
    protected $fillable = [
        'test_case_id',
        'user_id',
    ];

    public function testCase()
    {
        return $this->belongsTo(TestCase::class);
    }

    public function tester()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
