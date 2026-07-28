<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\RegisterEntrepriseRequest;
use Illuminate\Support\Facades\DB;
use App\Models\Entreprise;
use App\Models\User;
use App\Services\MailService;
use App\Services\OtpService;

class RegisterController extends Controller
{
    public function register(RegisterEntrepriseRequest $request)
    {

        DB::beginTransaction();

        try{

        $entreprise=Entreprise::create([

        'nom'=>$request->nom,

        'secteur'=>$request->secteur,

        'taille'=>$request->taille,

        'email'=>$request->email_entreprise,

        'telephone'=>$request->telephone,

        'adresse'=>$request->adresse

        ]);

        $otp=OtpService::generer();

        $user=User::create([

        'nom'=>$request->nom_admin,

        'prenom'=>$request->prenom_admin,

        'email'=>$request->email_admin,

        'password'=>bcrypt($request->password),

        'entreprise_id'=>$entreprise->id,

        'otp'=>$otp,

        'otp_expire_at'=>now()->addMinutes(10)

        ]);

        $user->assignRole('Admin');

        MailService::envoyerOtp(

        $user->email,

        $otp

        );

        DB::commit();

        return response()->json([

        'message'=>'Entreprise créée. Vérifiez votre email.'

        ],201);

        }catch(\Throwable $e){

        DB::rollBack();

        throw $e;

        }

    }
}
