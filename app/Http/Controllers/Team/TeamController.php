<?php

namespace App\Http\Controllers\Team;

use App\Http\Controllers\Controller;
use App\Http\Resources\DesignResource;
use App\Http\Resources\TeamResource;
use App\Repositories\Contracts\TeamInterface;
use App\Repositories\Eloquent\Criteria\ApplyEagerLoading;
use GeoJson\Geometry\Point;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TeamController extends Controller
{
    public $teamRepo;

    public function __construct(TeamInterface $teamRepo)
    {
        $this->teamRepo = $teamRepo;
    }

    //
    public function index()
    {
        return $this->teamRepo->paginate();
    }

    public function fetchUserTeams()
    {
        return TeamResource::collection(auth()->user()->teams()->with(['owner', 'members'])->get());
    }

    public function store()
    {
        $data = \request()->validate([
            'name' => 'required|string|max:80|unique:teams',
        ]);
        $data['slug'] = Str::slug($data['name']);
        $user = auth()->user();
        $team = $user->ownTeams()->create($data);
        return response()->success(new TeamResource($team), 'Create team success');
    }

    public function destroy($id)
    {
        $team = $this->teamRepo->find($id);
        $this->authorize('delete', $team);
        $this->teamRepo->delete($id);
        return response()->success([], 'Delete success');
    }

    public function update($id)
    {
        $team = $this->teamRepo->withCriteria(new ApplyEagerLoading(['owner', 'members']))->find($id);
        $this->authorize('update', $team);

        $data = \request()->validate([
            'name' => 'required|string|max:80|unique:teams',
        ]);
        $data['slug'] = Str::slug($data['name']);
        $team->update($data);
        return response()->success(new TeamResource($team), 'Update team success');
    }

    public function findTeamById($id)
    {
        return new TeamResource($this->teamRepo->withCriteria(new ApplyEagerLoading(['owner', 'members']))->find($id)) ;
    }

    public function deleteUserFromTeam(int $id, int $userId)
    {
        $team = $this->teamRepo->find($id);
        $user = auth()->user();
        // Check if the removed user is owner of team
        if ($team->owner->id === $userId) {
            return response()->error('You cannot remove the owner of this team', 403, 403);
        }
        // Check if user own the team
        if (!$user->isOwnerOfTeam($id)) {
            return response()->error('You are not owner of this team', 403, 403);
        }
        // Check if the removed users is member of the team
        if (!$team->members()->where('users.id', $userId)->exists()) {
            return response()->error("The user with Id $userId not belong to the team $team->name to remove");
        }
        $team->members()->detach($userId);
        return response()->success("The user $userId was remove successfully");
    }

    public function getDesignForTeam($id)
    {
        $designs = $this->teamRepo->find($id)->designs;
        return response()->success(DesignResource::collection($designs));
    }

}
