<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EntrepriseController extends Controller
{
    public function store(StoreEntrepriseRequest $request)
    {

        DB::beginTransaction();

        try{

        $entreprise=Entreprise::create(

        $request->validated()

        );

        /*
        Le créateur devient ADMIN.

        On créera son compte à l'étape OTP.
        */

        DB::commit();

        return response()->json([

        'message'=>'Entreprise créée.',

        'data'=>new EntrepriseResource($entreprise)

        ],201);

        }catch(\Exception $e){

        DB::rollBack();

        throw $e;

        }

    }
}
