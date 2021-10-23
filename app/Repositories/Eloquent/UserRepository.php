<?php


namespace App\Repositories\Eloquent;


use App\Models\Design;
use App\Models\User;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Support\Arr;

class UserRepository extends BaseRepository implements \App\Repositories\Contracts\UserInterface
{

    public function model()
    {
       return User::class;
    }

    public function search(array $arg)
    {
      $query = (new $this->model)->newQuery();

      //only designer who have designs
        if (Arr::get($arg, 'has_design')) {
            $query->has('designs');
        }

        //check for available_to_hire
        if (Arr::get($arg, 'available_to_hire')) {
            $query->where('available_to_hire', 1);
        }

        //Geographic search
        $lat = Arr::get($arg, 'lat');
        $lng = Arr::get($arg, 'lng');
        //dist can be km or mile
        $dist = Arr::get($arg, 'dist');
        $unit = Arr::get($arg, 'unit');

        if($lat && $lng) {
            $point = new Point($lat, $lng);
            //convert dist to meter
            $unit === 'km' ? $dist *= 1000 : $dist *= 1609.34;
            $query->distanceSphereExcludingSelf('location', $point, $dist);

        }

        if ($orderBy = Arr::get($arg, 'order_by')) {
            if ($orderBy === 'closest') {
                $query->orderByDistanceSphere('location', $point, $direction = 'asc');
            } else {
                $query->orderBy('created_at', $orderBy);
            }
        }

        return $query->get();

    }
}
