<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        return auth()->user()->entreprise->setting;
    }

    public function update(Request $request)
    {

        $setting=auth()->user()
            ->entreprise
            ->setting;

        $setting->update(

            $request->only(

                'langue',

                'theme',

                'assistant'

            )

        );

        return $setting;

    }
}
