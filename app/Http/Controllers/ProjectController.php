<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\Project;

class ProjectController extends Controller
{
    public function store(Request $request)
    {
        try {
            // ✅ Validation
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'address' => 'required|string',
                'presentation' => 'required|string',
                'ville_id' => 'required|exists:ville,id',
                'nb_appartements' => 'required|integer',
                'surface' => 'required|numeric',
                'email' => 'required|email',
                'user_id' => 'required|exists:users,id',

                'photo_couverture' => 'required|file|image|max:2048',
                'logo' => 'required|file|image|max:2048',

                'gallerie_images.*' => 'file|image|max:2048',
                'gallerie_videos.*' => 'file|mimetypes:video/mp4,video/avi,video/mov|max:51200',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Échec de la validation',
                    'errors' => $validator->errors()
                ], 422);
            }

            // 📦 Upload fichiers
            $photoCouverturePath = $request->file('photo_couverture')->store('projects/couvertures', 'public');
            $logoPath = $request->file('logo')->store('projects/logos', 'public');

            $galleryImagesPaths = [];
            if ($request->hasFile('gallerie_images')) {
                foreach ($request->file('gallerie_images') as $img) {
                    $galleryImagesPaths[] = $img->store('projects/images', 'public');
                }
            }

            $galleryVideosPaths = [];
            if ($request->hasFile('gallerie_videos')) {
                foreach ($request->file('gallerie_videos') as $video) {
                    $galleryVideosPaths[] = $video->store('projects/videos', 'public');
                }
            }

            // 🧱 Création du projet
            $project = Project::create([
                'name' => $request->name,
                'address' => $request->address,
                'presentation' => $request->presentation,
                'ville_id' => $request->ville_id,
                'nb_appartements' => $request->nb_appartements,
                'surface' => $request->surface,
                'email' => $request->email,
                'user_id' => $request->user_id,
                'photo_couverture' => $photoCouverturePath,
                'logo' => $logoPath,
                'gallerie_images' => json_encode($galleryImagesPaths),
                'gallerie_videos' => json_encode($galleryVideosPaths),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Projet ajouté avec succès',
                'project' => $project
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur serveur',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function display()
    {
        try {
            $projects = Project::with('user', 'ville', 'caracteristiques.option')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'status' => 'success',
                'message' => 'Liste des projets récupérée avec succès',
                'projects' => $projects
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de la récupération des projets',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function get_projectById($id)
    {
        try {
            $project = Project::with('user', 'ville', 'caracteristiques.option')->find($id);

            if (!$project) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Projet non trouvé',
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Projet récupéré avec succès',
                'project' => $project
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de la récupération du projet',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getProjectByCity(Request $request)
    {
        try {
            $villeId = $request->ville_id;

            $projects = Project::with('user', 'ville')
                ->where('ville_id', $villeId)
                ->get();

            if ($projects->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Aucun projet trouvé dans cette ville.',
                    'ville_id' => $villeId
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Projets récupérés avec succès.',
                'projects' => $projects
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de la récupération des projets.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
public function update(Request $request, $id)
{
    try {
        $project = Project::find($id);

        if (!$project) {
            return response()->json([
                'status' => 'error',
                'message' => 'Projet non trouvé',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string',
            'address' => 'sometimes|required|string',
            'presentation' => 'sometimes|required|string',
            'ville_id' => 'sometimes|required|exists:ville,id',
            'nb_appartements' => 'sometimes|required|integer',
            'surface' => 'sometimes|required|numeric',
            'email' => 'sometimes|required|email',
            'user_id' => 'sometimes|required|exists:users,id',

            'photo_couverture' => 'nullable|file|image|max:2048',
            'logo' => 'nullable|file|image|max:2048',
            'gallerie_images.*' => 'nullable|file|image|max:2048',
            'gallerie_videos.*' => 'nullable|file|mimetypes:video/mp4,video/avi,video/mov|max:51200',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Échec de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        // Mise à jour des fichiers
        if ($request->hasFile('photo_couverture')) {
            $project->photo_couverture = $request->file('photo_couverture')->store('projects/couvertures', 'public');
        }

        if ($request->hasFile('logo')) {
            $project->logo = $request->file('logo')->store('projects/logos', 'public');
        }

        if ($request->hasFile('gallerie_images')) {
            $imgPaths = [];
            foreach ($request->file('gallerie_images') as $img) {
                $imgPaths[] = $img->store('projects/images', 'public');
            }
            $project->gallerie_images = json_encode($imgPaths);
        }

        if ($request->hasFile('gallerie_videos')) {
            $videoPaths = [];
            foreach ($request->file('gallerie_videos') as $vid) {
                $videoPaths[] = $vid->store('projects/videos', 'public');
            }
            $project->gallerie_videos = json_encode($videoPaths);
        }

        // Mise à jour des champs simples
        $project->fill($request->only($project->getFillable())); // utilise le fillable proprement
        $project->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Projet modifié avec succès',
            'project' => $project
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Erreur lors de la mise à jour',
            'error' => $e->getMessage()
        ], 500);
    }
}
public function destroy($id)
{
    try {
        $project = Project::find($id);

        if (!$project) {
            return response()->json([
                'status' => 'error',
                'message' => 'Projet non trouvé',
            ], 404);
        }

        $project->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Projet supprimé avec succès',
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Erreur lors de la suppression',
            'error' => $e->getMessage()
        ], 500);
    }
}

}
