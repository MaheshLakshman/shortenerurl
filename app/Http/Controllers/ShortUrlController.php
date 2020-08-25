<?php

namespace App\Http\Controllers;

use App\ShortUrl;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Responses\SuccessResponse;
use App\Http\Requests\AddShortUrlRequest;

class ShortUrlController extends Controller
{
    private $addShortUrl;

    public function __construct(ShortUrl $addShortUrl)
    {
        $this->addShortUrl = $addShortUrl;
    }
    public function store(AddShortUrlRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = Auth::user()->id;
        $data['short_url'] = Str::random(8);
        $this->addShortUrl->create($data);
        return new SuccessResponse("Saved successfully..!");
    }

    public function get(Request $request)
    {
        $urls = $this->addShortUrl->where('user_id', Auth::user()->id)
            ->orderBy('created_at', 'desc')
            ->get();
        $json_data = array(
            'draw' => intval($request->input('draw')),
            'recordsTotal' => count($urls),
            'recordsFiltered' => count($urls),
            'data' => $urls
        );
        return $json_data;
    }
}
