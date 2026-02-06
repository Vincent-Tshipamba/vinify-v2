<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\University;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UniversityController extends Controller
{

    public function index()
    {
        return view('client.university.form');
    }

    /**
     * Créer une université.
     */
    public function store(Request $request)
    {
        $request->validate([
            'university_name' => 'required|string|unique:universities,name',
            'description' => 'nullable|string',
            'address' => 'nullable|string',
            'phone' => 'nullable|string',
            'subscription_id' => 'required|exists:subscriptions,id',
            'admin_name' => 'required|string',
            'admin_email' => 'required|email|unique:users,email',
            'admin_password' => 'required|min:8'
        ]);

        // Vérifier l’abonnement
        $subscription = Subscription::find($request->subscription_id);
        if (!$subscription) {
            return response()->json(['message' => 'Abonnement invalide.'], 400);
        }

        // Créer l'université
        $university = University::create([
            'name' => $request->university_name,
            'description' => $request->description,
            'address' => $request->address,
            'phone' => $request->phone,
            'subscription_id' => $request->subscription_id
        ]);

        // Créer l'administrateur
        $admin = User::create([
            'name' => $request->admin_name,
            'email' => $request->admin_email,
            'password' => Hash::make($request->admin_password),
            'university_id' => $university->id
        ]);

        // Lier l’université à son administrateur
        $university->admin_id = $admin->id;
        $university->save();

        return response()->json([
            'message' => 'Université et administrateur créés avec succès.',
            'university' => $university,
            'admin' => $admin
        ], 201);
    }
    /**
     * Afficher une université spécifique.
     */
    public function show($id)
    {
        $university = University::with('admin', 'subscription')->find($id);

        if (!$university) {
            return response()->json(['message' => 'Université non trouvée.'], 404);
        }

        return response()->json($university);
    }

    /**
     * Modifier une université.
     */
    public function update(Request $request, $id)
    {
        $university = University::find($id);

        if (!$university) {
            return response()->json(['message' => 'Université non trouvée.'], 404);
        }

        $request->validate([
            'name' => 'required|string|unique:universities,name,' . $id,
            'description' => 'nullable|string',
            'address' => 'nullable|string',
            'phone' => 'nullable|string',
            'subscription_id' => 'required|exists:subscriptions,id',
        ]);

        $university->update($request->all());

        return response()->json(['message' => 'Université mise à jour avec succès.', 'university' => $university]);
    }

    /**
     * Supprimer une université.
     */
    public function destroy($id)
    {
        $university = University::find($id);

        if (!$university) {
            return response()->json(['message' => 'Université non trouvée.'], 404);
        }

        // Vérifier si l'université a des utilisateurs avant suppression
        if ($university->users()->count() > 0) {
            return response()->json(['message' => 'Impossible de supprimer une université qui a des utilisateurs.'], 400);
        }

        $university->delete();

        return response()->json(['message' => 'Université supprimée avec succès.']);
    }
}
