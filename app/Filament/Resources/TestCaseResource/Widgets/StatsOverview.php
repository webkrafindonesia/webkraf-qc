<?php

namespace App\Filament\Resources\TestCaseResource\Widgets;

use App\Models\TestCaseScenario;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    public $test_case_id;

    protected function getStats(): array
    {
        $testCaseId = $this->test_case_id;

        $scenario = TestCaseScenario::query()
                    ->whereHas('testCaseId', function($query) use($testCaseId){
                        $query->whereHas('category', function($query2) use($testCaseId){
                            $query2->where('test_case_id',$testCaseId);
                        });
                    });

        $passedScenario = clone $scenario;
        $failedScenario = clone $scenario;
        $remarkScenario = clone $scenario;
        $fixedScenario = clone $scenario;
        $readyScenario = clone $scenario;

        $passed = $passedScenario->where('status','Passed')->count();
        $failed = $failedScenario->where('status','Failed')->count();
        $remark = $remarkScenario->where('status','Remark')->count();
        $fixed = $fixedScenario->where('status','Fixed by Dev')->count();
        $ready = $readyScenario->whereNull('status')->count();

        return [
            Stat::make('Passed', $passed)
                ->color('success')
                ->description('Total skenario yang sudah Passed')
                ->descriptionIcon('heroicon-m-check-circle')
                ->chart([7, 2, 10, 3, 15, 4, 17]),
            Stat::make('Failed', $failed)
                ->color('danger')
                ->description('Total skenario yang masih Failed')
                ->descriptionIcon('heroicon-m-x-circle')
                ->chart([7, 2, 10, 3, 15, 4, 17]),
            Stat::make('Remark', $remark)
                ->color('warning')
                ->description('Total skenario yang lolos dengan Remark')
                ->descriptionIcon('heroicon-m-exclamation-circle')
                ->chart([7, 2, 10, 3, 15, 4, 17]),
            Stat::make('Fixed by Dev', $fixed)
                ->color('info')
                ->description('Total skenario yang telah diperbaiki developer')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->chart([7, 2, 10, 3, 15, 4, 17]),
            Stat::make('Ready to Test', $ready)
                ->color('gray')
                ->description('Total skenario yang belum di-test')
                ->descriptionIcon('heroicon-m-arrow-left-start-on-rectangle')
                ->chart([7, 2, 10, 3, 15, 4, 17])
,
        ];
    }

    public function getColumns(): int
    {
        return 3;
    }
}
