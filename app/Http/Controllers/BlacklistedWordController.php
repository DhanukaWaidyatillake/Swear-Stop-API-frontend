<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBlacklistedWordRequest;
use App\Http\Requests\UpdateBlacklistedWordRequest;
use App\Models\BlacklistedWord;
use App\Services\ApiResultTools;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class BlacklistedWordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = BlacklistedWord::query()->where('user_id', $request->user()->id);

        $apiResultTools = new ApiResultTools($query, $request);

        return $apiResultTools->search()->order()->paginate(5);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBlacklistedWordRequest $request)
    {
        $data = $request->validated();

        BlacklistedWord::create([
            'word' => $data['word'],
            'is_enabled' => true,
            'added_through' => 'dashboard',
            'user_id' => $request->user()->id
        ]);

        return Redirect::route('manage_list');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBlacklistedWordRequest $request, BlacklistedWord $blacklistedWord)
    {
        $blacklistedWord->update([
            'is_enabled' => $request->validated('is_enabled')
        ]);
    }
}
