<?php

namespace App\Console\Commands;

use App\Models\SubscriptionType;
use App\Repositories\Eloquent\SubscriptionRepository;
use App\Repositories\SubscriptionRepositoryInterface;
use Illuminate\Console\Command;

class GenerateSubscriptionTypes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subcription-types:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates subscription types based on the provided API endpoint';

    /**
     * @var SubscriptionRepositoryInterface
     */
    private $subscriptionRepository;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(SubscriptionRepository $subscriptionRepository)
    {
        parent::__construct();

        $this->subscriptionRepository = $subscriptionRepository;

    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Downloading users data from the endpoint');
        $users = $this->subscriptionRepository->getExternalUsers();
        $this->info('Users were downloaded');

        $subscriptionsTypes = [];

        foreach ($users as $user){

            $type = $user['type'];

            if (!in_array($type, $subscriptionsTypes)){
                $subscriptionsTypes[] = $type;
            }

        }

        $this->info('Saving types');
        foreach ($subscriptionsTypes as $subscriptionsType){
            $this->info('Saving type '.$subscriptionsType);
            $newType = new SubscriptionType();
            $newType->name = $subscriptionsType;
            $newType->price = rand(10,25);
            $newType->save();
        }

        $this->info('Finished job!');
        return 0;

    }
}
