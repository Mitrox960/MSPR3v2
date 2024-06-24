<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Messagerie extends Model
{
    use HasFactory;

    // Définir le nom de la table explicitement si ce n'est pas le pluriel du nom du modèle
    protected $table = 'messagerie';

    // Définir les attributs pouvant être assignés en masse
    protected $fillable = ['id_utilisateur', 'message'];
}
