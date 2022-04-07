<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\ChatApp\Repos\ChatGroup\ChatGroupEloquentRepo;

class ChatGroupAccess
{
    protected $chatGroupRepo;

    public function __construct(ChatGroupEloquentRepo $chatGroupRepo)
    {
        $this->chatGroupRepo = $chatGroupRepo;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if(!$request->group_id){
            return response()->json([ 'error' => __("Group doesn't exist."), ]);
        }

        $user = auth()->user();
        $group = $this->chatGroupRepo->find($request->group_id)->with('participants')->first();

        if(!$group){
            return response()->json([ 'error' => __("Group doesn't exist."), ]);
        }

        if($group->participants->contains($user)){
            $request->merge([
                "user" => $user,
                "group" => $group,
            ]);
    
            return $next($request);
        }

        return response()->json([ 'error' => __("You have no access rights."), ]);
    }
}
