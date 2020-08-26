<?php

namespace App\Http\Controllers;

use App\ShortUrl;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use App\Http\Responses\SuccessResponse;
use App\Http\Requests\AddShortUrlRequest;
use App\Http\Responses\ErrorResponse;

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
        $data['user_id'] = $id = Auth::user()->id;
        $save = $this->addShortUrl->create($data);
        $short_url = URL::to("/") . '/shrturl/' . base_convert($save->id, 10, 36);
        $save->update(["short_url" => $short_url]);
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

    public function getLongUrl($code)
    {
        $id = base_convert($code, 36, 10);
        $redirect = $this->addShortUrl->where('id', $id)
            ->first('url');
        return $redirect ? redirect($redirect->url) : new ErrorResponse("Invalid Url");
    }
}
