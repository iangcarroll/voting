<?php

namespace App\Http\Controllers;

use App\Candidate;
use App\Vote;
use Auth;
use Illuminate\Http\Request;
use Session;

class VoteController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $candidates = $this->shuffle(nextTerm()->candidates()->get()->all());

        return view('vote.cast')
            ->withCandidates($candidates)
            ->withCount(0);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'vote' => 'required|array|vote_count|vote_unique|sane_votes',
        ]);
        
        $user = $request->user();
        
        if ($user->uuid === null) {
            $user->update([
                'uuid' => uuid(),
            ]);
        }
        
        foreach ($request->get('vote') as $vote) {
            Vote::create([
                'candidate_id' => Candidate::findOrFail($vote)->id,
                'user_id'      => $user->id,
                'term_id'      => nextTerm()->id,
            ]);
        }

        Session::flash('message', 'Your votes were successfully counted.');

        return redirect('/');
    }
    
    /**
     * Randomize the given array.
     *
     * @param array $candidates
     *
     * @return array
     */
    protected function shuffle(array $candidates)
    {
        /* A list of numbers we've already generated, so that we don't overwrite existing elements. */
        $used = [];
        /* A list of candidates that have been randomly distributed. */
        $result = [];
        
        while (count($result) < count($candidates)) {
            /* Retrieve a zero-indexed number to pull from our list of candidates. */ 
            $int = random_int(0, count($candidates) - 1);
            
            /* Don't duplicate candidates. */
            if (! isset($used[$int])) {
                $result[]   = $candidates[$int];
                $used[$int] = true;
            }
        }
        
        return $result;
    }
}
