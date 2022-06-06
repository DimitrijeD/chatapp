<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\ChatApp\Repos\ChatGroup\ChatGroupEloquentRepo;
use App\Models\ParticipantPivot;
use App\Models\ChatRole;

class CanSubmitMessage
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
        if(!$request->group_id)
            return response()->json(['errors' => __("Group doesn't exist.")], 404);
        
        if(!$user = auth()->user())
            return response()->json(['errors' => __("Unauthenticated")], 403);

        $group = $this->chatGroupRepo->first(
            ['id' => $request->group_id], 
            ['participants']
        );

        if(!$group)
            return response()->json(['errors' => __("Group doesn't exist.")], 404);

        foreach($group->participants as $participant){
            if($participant->id == $user->id){
                if($participant->pivot->participant_role == ChatRole::LISTENER)
                    return response()->json(['error' => __("You cannot chat in this chat group, but you can still see messages")]);
            }
        }
        
        return $next($request);
    }
}
