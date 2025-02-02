<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Talk;
use App\Vote;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class TalkController extends Controller
{
    public function index(){
        $user = Auth::user();
    	$talks = Talk::orderBy('created_at','desc')-> get();
        // do a checking on user id here, i forget the syntax, can you write
        //i also dk le how
        $talksArray = $talks->toArray();

        $talks = [];
        foreach ($talksArray as $talkArray){
            $talkID = $talkArray['id'];
            $vote = Vote::where('talk_id', $talkID)
                ->where('user_id', $user->id)
                ->value('vote');
            // should be here wtf

            if(!empty($vote)) {

                $vote = ['vote' => $vote];
                $talks [] = array_merge($talkArray, $vote);

            } else {
                $vote = ['vote' => 0];
                $talks [] = array_merge($talkArray, $vote);
            }
        }

    	return view('talks.index', compact('talks'));
    }

    public function voteTalk(Request $request){
    	$talk_id = $request['talkId'];


    	$is_vote = $request['isVote'] === 'true';
    	$update = false;
    	$talk = Talk::find($talk_id);

    	if (!$talk) {
    		return null;
    	}

    	$user = Auth::user();

        // do validation here
        $voteCount = $user -> votes() -> count();
        if($voteCount >= 5 ){
            // return null;
            return response('limit', 401);
        }


    	// $vote = $user -> votes()-> where('talk_id', $talk_id)->first();
    	// if ($vote){
    	// 	$already_vote = $vote->vote;
    	// 	$update = true;
    	// 	if ($already_vote == $is_vote){
    	// 		$vote->delete(); // nd this devote
    	// 		return null;
    	// 	}
    	// } else{
    		$vote = new Vote();
    	// }


    	$vote->vote = $is_vote;
    	$vote->user_id = $user->id;
    	$vote->talk_id = $talk->id;
    	
    	if ($update){
    		$vote->update();
    	} else{
    		$vote->save();
    	}

    	return null;
    }
}
