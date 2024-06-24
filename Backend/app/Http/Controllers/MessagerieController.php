<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Messagerie;

class MessagerieController extends Controller
{
    /**
     * Récupérer les messages d'un utilisateur spécifique.
     *
     * @param int $id_user
     * @return \Illuminate\Http\Response
     */
    public function getUserMessage($id_user)
    {
        // Récupérer les messages de l'utilisateur avec l'id donné
        $messages = Messagerie::where('id_utilisateur', $id_user)->get();

        // Retourner les messages en réponse JSON
        return response()->json($messages);
    }
}
