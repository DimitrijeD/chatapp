<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends Controller
{
    public function getRouteListLoop()
    {
        return view('responses.get_route_list');
    }

    public function createResponseFromGet()
    {
        return view('responses.get', [
            'response' => json_decode(file_get_contents(storage_path() . "/api_get_response.json"), true)
        ]);
    }

    public function createPost()
    {
        return view('responses.post', [
            'response' => json_decode(file_get_contents(storage_path() . "/api_post_response.json"), true),
            'payload' => json_decode(file_get_contents(storage_path() . "/api_post_payload.json"), true),
        ]);
    }
}
