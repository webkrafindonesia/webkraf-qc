<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class TestCaseScenario extends Model implements HasMedia
{
    use SoftDeletes;
    use InteractsWithMedia;

    protected $fillable = [
        'test_case_id_id',
        'scenario_name',
        'scenario_steps',
        'expected_result',
        'actual_result',
        'status',
        'remarks',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'scenario_steps' => 'array',
    ];

    public function testCaseId()
    {
        return $this->belongsTo(TestCaseID::class, 'test_case_id_id');
    }
}
