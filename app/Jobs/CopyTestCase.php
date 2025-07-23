<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\TestCase;
use App\Models\TestCaseCategory;
use App\Models\TestCaseID;
use App\Models\TestCaseScenario;

class CopyTestCase implements ShouldQueue
{
    use Queueable;

    /**
     * The test case to copy.
     *
     * @var mixed
     */
    protected $testCase;

    /**
     * Create a new job instance.
     */
    public function __construct($testCase)
    {
        $this->testCase = $testCase;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Logic to copy the test case
        $newTestCase = $this->testCase->replicate();
        $newTestCase->system_name .= ' - Copy'; // Append ' - Copy' to the system name
        $newTestCase->save();

        // Optionally, copy related scenarios or other data
        foreach ($this->testCase->categories as $category) {
            $newCategory = $category->replicate();
            $newCategory->test_case_id = $newTestCase->id; // Associate with new test case
            $newCategory->save();

            foreach ($category->ids as $id) {
                $newId = $id->replicate();
                $newId->test_case_category_id = $newCategory->id; // Associate with new category
                $newId->save();

                foreach ($id->scenarios as $scenario) {
                    $newScenario = new TestCaseScenario(
                        [
                            'sort' => $scenario->sort,
                            'test_case_id_id' => $newId->id,
                            'scenario_name' => $scenario->scenario_name,
                            'scenario_steps' => $scenario->scenario_steps,
                            'expected_result' => $scenario->expected_result,
                        ]
                    );
                    $newScenario->save();
                }
            }
        }
    }
}
