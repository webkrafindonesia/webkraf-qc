<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class TestCase extends Model
{
    use HasUuids;
    use SoftDeletes;

    protected $fillable = [
        'system_name',
        'system_version',
        'company',
        'description',
        'type',
        'status',
        'tester_name',
        'start_date',
        'end_date',
        'platform_version',
        'url',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'platform_version' => 'array',
        'tester_name' => 'array'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function categories()
    {
        return $this->hasMany(TestCaseCategory::class, 'test_case_id');
    }

    public function testers()
    {
        return $this->hasMany(TestCaseTester::class, 'test_case_id');
    }
}
