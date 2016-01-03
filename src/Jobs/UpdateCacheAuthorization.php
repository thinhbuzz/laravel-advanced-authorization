<?php

namespace Buzz\Authorization\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateCacheAuthorization extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    /**
     * @var
     */
    private $users;

    /**
     * Create a new job instance.
     *
     * @param $users
     */
    public function __construct($users)
    {
        $this->users = $users;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $auto_update = app('config')->get('authorization.cache.auto_update');
        if ($auto_update) {
            foreach ($this->users as $user) {
                $user->forceUpdateCache();
            }
        } else {
            foreach ($this->users as $user) {
                $user->forgetCache();
            }
        }
    }
}
