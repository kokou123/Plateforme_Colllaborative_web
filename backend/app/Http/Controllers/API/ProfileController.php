<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{

    public function me(Request $request)
    {
        return response()->json(

            $request->user()->load(
                'roles',
                'entreprise',
                'equipe'
            )

        );
    }

    public function update(Request $request)
    {

        $request->validate([

            'nom'=>'required',

            'prenom'=>'required',

            'photo'=>'nullable|image'

        ]);

        $user=$request->user();

        if($request->hasFile('photo')){

            $path=$request->file('photo')
                ->store('avatars','public');

            $user->photo=$path;

        }

        $user->nom=$request->nom;

        $user->prenom=$request->prenom;

        $user->save();

        return response()->json($user);

    }

}