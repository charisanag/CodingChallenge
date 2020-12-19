<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Subscription extends Model
{

    protected $fillable = ["*"];

    public $timestamps = false;

    protected $primaryKey ='user_id';


    public function type()
    {
        return $this->hasOne(SubscriptionType::class);
    }

}
