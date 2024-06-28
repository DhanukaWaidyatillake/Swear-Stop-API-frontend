<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWhitelistedWordRequest;
use App\Http\Requests\UpdateWhitelistedWordRequest;
use App\Models\WhitelistedWord;
use App\Services\ApiResultTools;
use App\Services\ToastMessageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class WhitelistedWordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = WhitelistedWord::query()->where('user_id', $request->user()->id);

        $apiResultTools = new ApiResultTools($query, $request);

        return $apiResultTools->search()->order()->paginate(5);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreWhitelistedWordRequest $request)
    {
        $data = $request->validated();

        WhitelistedWord::create([
            'word' => $data['word'],
            'is_enabled' => true,
            'added_through' => 'dashboard',
            'user_id' => $request->user()->id
        ]);

        $toastMessageService=new ToastMessageService();
        $toastMessageService->showToastMessage('success','Word Added to Whitelist');

        return Redirect::route('manage_list');
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateWhitelistedWordRequest $request, WhitelistedWord $whitelistedWord)
    {
        $whitelistedWord->update([
            'is_enabled' => $request->validated('is_enabled')
        ]);
    }
}
