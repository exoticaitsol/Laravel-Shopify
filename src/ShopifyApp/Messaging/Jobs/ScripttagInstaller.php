<?php

namespace Osiset\ShopifyApp\Messaging\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Osiset\ShopifyApp\Objects\Values\ShopId;
use Osiset\ShopifyApp\Actions\CreateScripts as CreateScriptsAction;

/**
 * Webhook job responsible for handling installing scripttag.
 */
class ScripttagInstaller implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * The shop's ID.
     *
     * @var ShopId
     */
    protected $shopId;

    /**
     * The scripts to add.
     *
     * @var array
     */
    protected $configScripts;

    /**
     * Create a new job instance.
     *
     * @param ShopId $shopId        The shop ID.
     * @param array  $configScripts The scripts to add.
     *
     * @return void
     */
    public function __construct(ShopId $shopId, array $configScripts)
    {
        $this->shopId = $shopId;
        $this->configScripts = $configScripts;
    }

    /**
     * Execute the job.
     *
     * @param CreateScriptsAction $createScriptsAction The action for creating scripttags.
     *
     * @return array
     */
    public function handle(CreateScriptsAction $createScriptsAction): array
    {
        return call_user_func(
            $createScriptsAction,
            $this->shopId,
            $this->configScripts
        );
    }
}
