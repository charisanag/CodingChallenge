<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class SubscriptionCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        $filter = '';

        $activeFilter = request()->get('active');
        $from = request()->get('from');
        $to = request()->get('to');


        if ($activeFilter == '1'){
            $filter = 'active';
        }

        if ($activeFilter == '0'){
            $filter = 'inactive';
        }


        if (!empty($from) && !empty($to)){
            $filter = 'date range';
        }

        $schema = [
            'data' => $this->collection,
            'filter' => $filter,

        ];

        if ($filter === 'date range'){
            $schema['from'] = $from;
            $schema['to'] = $to;
        }

        return $schema;
    }
}
