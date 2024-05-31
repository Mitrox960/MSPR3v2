<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Plante;

class PlantsController extends Controller
{
    public function createPlant(Request $request)
    {
        // V�rifier si l'utilisateur est connect�
        if (!Auth::check()) {
            return response()->json(['message' => 'Aucun utilisateur connecte']);


        }    // G�rez l'upload de l'image
        /*  $image = base64_encode(file_get_contents($request->file('image')->path()));
            $plante = $utilisateur->plantes()->create([
                'nom' => $validatedData['nom'],
                'image' => $image,
                'description' => $validatedData['description'],
                'conseil_entretien' => $validatedData['conseil_entretien'],
            ]);*/

        $validatedData = $request->validate([
            'nom' => 'required|string|max:255',
            //'image' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'description' => 'required|string|max:255',
            'conseil_entretien' => 'required|string|max:255',
        ]);

        try {
            // R�cup�rez l'utilisateur actuellement authentifi�
            $utilisateur = Auth::user();

            $image = base64_encode(file_get_contents($request->file('image')->path()));
            // Cr�er une nouvelle plante associ�e � cet utilisateur
            $plante = $utilisateur->plantes()->create([
                'nom' => $validatedData['nom'],
                //'image' => $validatedData['image'],
                'image' => $image,
                'description' => $validatedData['description'],
                'conseil_entretien' => $validatedData['conseil_entretien'],
            ]);

            // Retourner les d�tails de la plante cr��e
            return response()->json($plante, 201); // 201 signifie Created
        } catch (\Exception $e) {
            // En cas d'erreur, retourner une r�ponse JSON avec le message d'erreur appropri�
            return response()->json(['error' => 'Une erreur s\'est produite lors de la cr�ation de la plante.', 'message' => $e->getMessage()], 500);
        }
    }
    public function getUserPlants()
    {
        try {
            if (!Auth::check()) {
                return response()->json(['message' => 'Aucun utilisateur connecte']);
            }

            $utilisateur = Auth::user();

            $plantes = $utilisateur->plantes;

            // D�coder les images encod�es en base64 avant de les renvoyer
            foreach ($plantes as $plante) {
                // V�rifier si l'image est encod�e en base64
                if (strpos($plante->image, 'data:image/jpeg;base64,') === 0) {
                    // Supprimer le pr�fixe "data:image/jpeg;base64,"
                    $imageData = substr($plante->image, strpos($plante->image, ',') + 1);
                    // D�coder l'image base64 en format binaire
                    $decodedImage = base64_decode($imageData);
                    // Enregistrer l'image d�cod�e dans un fichier temporaire
                    $tempImagePath = tempnam(sys_get_temp_dir(), 'plant_image');
                    file_put_contents($tempImagePath, $decodedImage);
                    // Convertir l'image en JPEG
                    $jpegImagePath = tempnam(sys_get_temp_dir(), 'plant_image') . '.jpg';
                    $image = imagecreatefromjpeg($tempImagePath);
                    imagejpeg($image, $jpegImagePath, 100);
                    imagedestroy($image);
                    // Lire le contenu de l'image JPEG
                    $jpegImageData = file_get_contents($jpegImagePath);
                    // Supprimer les fichiers temporaires
                    unlink($tempImagePath);
                    unlink($jpegImagePath);
                    // Mettre � jour l'image de la plante avec l'image JPEG
                    $plante->image = $jpegImageData;
                }
            }

            return response()->json($plantes, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Une erreur s\'est produite lors de la r�cup�ration des plantes de l\'utilisateur.', 'message' => $e->getMessage()], 500);
        }
    }

    public function postPlant(Plante $plante)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Aucun utilisateur connecte'], 401);
        }

        try {

            $plante->update(['postee' => true]);

            return response()->json(['message' => 'Plante postee'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Une erreur s\'est produite lors de la mise � jour de la plante', 'message' => $e->getMessage()], 500);
        }
    }

    public function removePlant(Plante $plante)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Aucun utilisateur connecte'], 401);
        }

        try {

            $plante->update(['postee' => false]);

            return response()->json(['message' => 'Plante retiree'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Une erreur s\'est produite lors de la mise � jour de la plante', 'message' => $e->getMessage()], 500);
        }
    }

    public function deletePlant(Plante $plante)
    {
        try {
            if ($plante->id_utilisateur !== auth()->id()) {
                return response()->json([
                    'error' => 'Vous n\'etes pas autorise a supprimer cette plante.'
                ], 403);
            }

            $plante->delete();

            return response()->json(['message' => 'La plante a ete supprimee avec succes.'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Une erreur s\'est produite lors de la suppression de la plante.', 'message' => $e->getMessage()], 500);
        }
    }
    public function allPlants()
    {
        try {
            // R�cup�rer toutes les plantes avec les informations sur le propri�taire
            $plantes = Plante::with(['utilisateur.adresse'])->where('postee', true)->get();
            // D�coder les images encod�es en base64 avant de les renvoyer
            foreach ($plantes as $plante) {
                // V�rifier si l'image est encod�e en base64
                if (strpos($plante->image, 'data:image/jpeg;base64,') === 0) {
                    // Supprimer le pr�fixe "data:image/jpeg;base64,"
                    $imageData = substr($plante->image, strpos($plante->image, ',') + 1);
                    // D�coder l'image base64 en format binaire
                    $decodedImage = base64_decode($imageData);
                    // Enregistrer l'image d�cod�e dans un fichier temporaire
                    $tempImagePath = tempnam(sys_get_temp_dir(), 'plant_image');
                    file_put_contents($tempImagePath, $decodedImage);
                    // Convertir l'image en JPEG
                    $jpegImagePath = tempnam(sys_get_temp_dir(), 'plant_image') . '.jpg';
                    $image = imagecreatefromjpeg($tempImagePath);
                    imagejpeg($image, $jpegImagePath, 100);
                    imagedestroy($image);
                    // Lire le contenu de l'image JPEG
                    $jpegImageData = file_get_contents($jpegImagePath);
                    // Supprimer les fichiers temporaires
                    unlink($tempImagePath);
                    unlink($jpegImagePath);
                    // Mettre � jour l'image de la plante avec l'image JPEG
                    $plante->image = $jpegImageData;
                }
            }

            return response()->json($plantes);
        } catch (\Exception $e) {
            // Journalisez l'erreur
            \Log::error('Erreur lors de la r�cup�ration des plantes : ' . $e->getMessage());
            // Retournez une r�ponse d'erreur avec le message complet de l'exception
            return response()->json(['message' => 'Une erreur est survenue lors de la r�cup�ration des plantes.', 'error' => $e->getMessage()], 500);
        }
    }
}
