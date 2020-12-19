<?php


namespace App\Repositories\Eloquent;



use App\Models\Subscription;
use App\Models\SubscriptionType;
use App\Repositories\SubscriptionRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Mockery\Exception;
use Symfony\Component\HttpFoundation\Request;

class SubscriptionRepository extends BaseRepository implements SubscriptionRepositoryInterface
{

    private $apiBaseUrl = "http://haris.users.challenge.dev.monospacelabs.com/users";

    private $discount = 0.3;

    /**
     * UserRepository constructor.
     *
     * @param Subscription $model
     */
    public function __construct(Subscription $model)
    {
        parent::__construct($model);
    }

    /**
     * @return Collection
     */
    public function all(): Collection
    {
        return $this->model->all();
    }


    public function subscribeUser(Request $request)
    {

        $subscription = json_decode($request->getContent(), true);


        $user = $this->getExternalUserById($subscription['user_id']);

        if (empty($user) || !$user['active']) {
            return "lathsos";
        }


        $subscriptionType = SubscriptionType::where('name', $user['type'])->first();

        $activeSubscriptionByType = $this->getActiveSubscriptions($subscriptionType->id);

        if (!$activeSubscriptionByType) {
            return 'lathoghfghfhgs';
        }

        Subscription::create(
            [
                'subscription_type_id'=>$subscriptionType->id,
               'user_Id'=>$user['id'],
                'price'=>$this->getSubscriptionPrice($subscriptionType),
                'from'=>$subscription['from'],
                'to'=>$subscription['to'],
            ]
        );

        return $activeSubscriptionByType;

    }

    public function getExternalUsers()
    {

        $users = [];

        try {
            $response = Http::get($this->apiBaseUrl);

            $users = json_decode($response->body(), true);

        } catch (Exception $exception) {

        }

        return $users;

    }


    /**
     * @param $userId
     * @return array
     */
    public function getExternalUserById($userId)
    {
        $users = $this->getExternalUsers();


        foreach ($users as $user) {
            if ($user['id'] === $userId) {
                return $user;
            }
        }

        return [];
    }

    public function getActiveSubscriptions($typeId = '')
    {

        $sqlBuilder = $this->model->whereDate('from', "<=", date("Y-m-d"))->whereDate("to", ">=", date('Y-m-d'));

        if (!empty($typeId)) {
            $sqlBuilder->where('subscription_type_id', $typeId);
        }

        return $sqlBuilder->get();
    }


    public function getInactiveSubscriptions($typeId = '')
    {

        $sqlBuilder = $this->model->whereDate('to', "<", date("Y-m-d"));


        return $sqlBuilder->get();
    }


    public function getSubscriptionsFromRange($from, $to)
    {

        $sqlBuilder = $this->model->whereDate('from', ">=", $from)->whereDate("to", "<=", $to);


        return $sqlBuilder->get();
    }

    public function getSubscriptionPrice($subscriptionType)
    {
        $activeSubscriptions = $this->getActiveSubscriptions();

        if($activeSubscriptions){
            return $subscriptionType->price * (1-$this->discount);
        }else{
            return $subscriptionType->price;
        }
    }


    public function getSubscriptions()
    {
        $activeFilter = request()->get('active');
        $from = request()->get('from');
        $to = request()->get('to');


        if ($activeFilter == '1'){
            return $this->getActiveSubscriptions();
        }

        if ($activeFilter == '0'){
            return $this->getInactiveSubscriptions();
        }


        if (!empty($from) && !empty($to)){
            return $this->getSubscriptionsFromRange($from, $to);
        }


        return Subscription::all();
    }
}