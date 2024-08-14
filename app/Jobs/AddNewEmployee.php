<?php

namespace App\Jobs;

use App\Models\Employees;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AddNewEmployee implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $requestBodyData;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($requestBodyData)
    {
        $this->requestBodyData = $requestBodyData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        var_dump('berhasil horee');
        sleep(10);
        Employees::create($this->requestBodyData); // QueryException if email already been taken code 23000
    }
}
