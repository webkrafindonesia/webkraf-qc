<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TestCaseID extends Model
{
    use SoftDeletes;

    protected $table = 'test_case_ids';

    protected $fillable = [
        'test_case_category_id',
        'id_name',
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

    public function category()
    {
        return $this->belongsTo(TestCaseCategory::class, 'test_case_category_id');
    }

    public function scenarios()
    {
        return $this->hasMany(TestCaseScenario::class, 'test_case_id_id');
    }
}
