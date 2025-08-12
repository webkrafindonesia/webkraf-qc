<?php

namespace App\Services;

use App\Models\TestCaseScenario;

class PercentageProgress
{
    public $scenario;
    public $all;

    public function __construct($testCaseId){
        $this->scenario = TestCaseScenario::query()
                    ->whereHas('testCaseId', function($query) use($testCaseId){
                        $query->whereHas('category', function($query2) use($testCaseId){
                            $query2->where('test_case_id',$testCaseId);
                        });
                    });
        $allScenario = clone $this->scenario;
        $this->all = $allScenario->count();
    }

    public function PassedProgress()
    {
        $passedScenario = clone $this->scenario;
        $passed = $passedScenario->where('status','Passed')->count();

        return ($this->all != 0) ? number_format(($passed/$this->all*100),0,',','.').'%' : '0%';
    }

    public function FailedProgress()
    {
        $failedScenario = clone $this->scenario;
        $failed = $failedScenario->where('status','Failed')->count();

        return ($this->all != 0) ? number_format(($failed/$this->all*100),0,',','.').'%' : '0%';
    }

    public function RemarkProgress()
    {
        $remarkScenario = clone $this->scenario;
        $remark = $remarkScenario->where('status','Remark')->count();

        return ($this->all != 0) ? number_format(($remark/$this->all*100),0,',','.').'%' : '0%';
    }

    public function FixedProgress()
    {
        $fixedScenario = clone $this->scenario;
        $fixed = $fixedScenario->where('status','Fixed')->count();

        return ($this->all != 0) ? number_format(($fixed/$this->all*100),0,',','.').'%' : '0%';
    }

    public function ReadyProgress()
    {
        $readyScenario = clone $this->scenario;
        $ready = $readyScenario->whereNull('status')->count();

        return ($this->all != 0) ? number_format(($ready/$this->all*100),0,',','.').'%' : '0%';
    }
}
