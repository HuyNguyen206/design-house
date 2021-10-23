<?php


namespace App\Repositories\Eloquent;


use App\Models\Design;
use Illuminate\Support\Arr;

class DesignRepository extends BaseRepository implements \App\Repositories\Contracts\DesignInterface
{

    public function model()
    {
        return Design::class;
    }


    /**
     * @param $key
     * @param array $arg
     * @return mixed
     */
    public function search(array $arg)
    {
        $query = (new $this->model)->newQuery();
        if (Arr::get($arg, 'is_live')) {
            $query->where('is_live', true);
        }

        //return only designs with comments
        if (Arr::get($arg, 'has_comment')) {
            $query->has('comments');
        }

        //return only design assigned to teams
        if (Arr::get($arg, 'assign_to_team')) {
            $query->has('team');
        }

        //search by title or description
        if ($key = Arr::get($arg, 'key')) {
            $query->where(function($q) use($key){
                $q->where('title', 'like', "%$key%")
                    ->orWhere('description', 'like',  "%$key%");
            });
        }

        //order by likes or latest design
        if ($orderBy = Arr::get($arg, 'order_by')) {
            if ($orderBy === 'likes') {
                $query->withCount('likedUsers')->orderByDesc('liked_users_count');
            } else {
                $query->latest();
            }
        }
        $query->with('likedUsers');
        return $query->get();
    }
}
