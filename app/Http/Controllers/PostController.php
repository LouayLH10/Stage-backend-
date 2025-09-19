<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Project;

class PostController extends Controller
{
    public function generateFacebookPost(Request $request, $id)
    {
        try {
            // 1️⃣ Récupérer le projet directement depuis la DB avec les relations
            $project = Project::with('user', 'region.city', 'features.option', 'type')->find($id);

            if (!$project) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Project not found',
                ], 404);
            }

            // 2️⃣ Construire le prompt pour OpenRouter
            $features = $project->features->map(fn($f) => $f->option->option)->implode(', ');

            $userMessage = "Rédige une publication Facebook pour la résidence \"{$project->name}\" située à \"{$project->address}\". 
            Présentation : {$project->presentation}.
            Nombre d'appartements : {$project->numberOfAppartements}.
            Surface totale : {$project->surface} m².
            Caractéristiques : {$features}.
            Type : {$project->type->type}.
            ville : {$project->region->city->city}.
            region :{$project->region->region}.     
            email :{$project->email}.    
            Inclure un ton attractif et vendeur.
            ⚠️ Ne commence pas par une phrase introductive inutile et ne termine pas par des conseils ou hashtags génériques.";

            // 3️⃣ Appeler l’API OpenRouter avec timeout de 60 secondes
            $response = Http::timeout(60)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . env('DEEPSEEK'),
                    'Content-Type'  => 'application/json',
                ])
                ->post('https://openrouter.ai/api/v1/chat/completions', [
                    "model" => "deepseek/deepseek-chat-v3.1:free",
                    "messages" => [
                        [
                            "role" => "user",
                            "content" => $userMessage
                        ]
                    ]
                ]);

            if ($response->failed()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to generate text from OpenRouter',
                    'response' => $response->body()
                ], $response->status());
            }

            $result = $response->json();
            $generatedText = $result['choices'][0]['message']['content'] ?? 'No response';

            // 4️⃣ Nettoyer le texte : garder uniquement le paragraphe principal
            // Suppression de la première phrase si elle commence par "Voici" et suppression des hashtags/finalités
            $lines = explode("\n", $generatedText);
            $cleanedLines = array_filter($lines, fn($line) => !preg_match('/^(Voici|N\'hésitez pas)/i', trim($line)));
            $paragraph = implode("\n", $cleanedLines);

            // 5️⃣ Retourner le projet + texte Facebook nettoyé
            return response()->json([
                'status' => 'success',
                'project' => $project,
                'facebook_post' => trim($paragraph),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error retrieving project or generating text',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
