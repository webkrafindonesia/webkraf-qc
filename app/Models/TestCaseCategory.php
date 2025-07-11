<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TestCaseCategory extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'test_case_id',
        'category_name',
        'description',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function testCase()
    {
        return $this->belongsTo(TestCase::class, 'test_case_id');
    }

    public function ids()
    {
        return $this->hasMany(TestCaseID::class, 'test_case_category_id');
    }
}
