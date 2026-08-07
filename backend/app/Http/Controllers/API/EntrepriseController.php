<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\AuditLogService;
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

    public function show()
    {
        return response()->json([
            'success' => true,
            'data' => auth()->user()->entreprise
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'nom' => 'required|string',
            'secteur' => 'nullable|string',
            'taille' => 'required|integer',
            'email' => 'required|email',
            'telephone' => 'nullable|string',
            'adresse' => 'nullable|string',
        ]);

        $entreprise = auth()->user()->entreprise;
        $entreprise->update($request->only(['nom', 'secteur', 'taille', 'email', 'telephone', 'adresse']));

        AuditLogService::enregistrer(auth()->id(), 'Modification', 'Entreprise', $entreprise->id, "Modification des informations de l'entreprise.");

        return response()->json(['success' => true, 'message' => 'Entreprise mise à jour.', 'data' => $entreprise]);
    }
}
