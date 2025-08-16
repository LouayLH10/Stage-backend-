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
                'region_id' => 'required|exists:region,id',
                'nb_appartements' => 'required|integer',
                'surface' => 'required|numeric',
                'email' => 'required|email',
                'user_id' => 'required|exists:users,id',
                'type_id' => 'required|exists:type,id',
                'photo_couverture' => 'required|file|image|max:2048',
                'logo' => 'required|file|image|max:2048',

                'gallerie_images.*' => 'file|image|max:2048',
                'gallerie_videos.*' => 'file|mimetypes:video/mp4,video/avi,video/mov|max:51200',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // 📦 Upload files
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

            // 🧱 Create project
            $project = Project::create([
                'name' => $request->name,
                'address' => $request->address,
                'presentation' => $request->presentation,
                'region_id' => $request->region_id,
                'nb_appartements' => $request->nb_appartements,
                'surface' => $request->surface,
                'email' => $request->email,
                'user_id' => $request->user_id,
                'photo_couverture' => $photoCouverturePath,
                'logo' => $logoPath,
                'type_id' => $request->type_id,
                'gallerie_images' => json_encode($galleryImagesPaths),
                'gallerie_videos' => json_encode($galleryVideosPaths),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Project successfully added',
                'project' => $project
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Server error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

public function display()
{
    try {
       $projects = Project::with('user', 'region.ville', 'caracteristiques.option', 'type')
    ->orderBy('created_at', 'desc')
    ->get()
    ->map(function ($project) {
        return [
            'id' => $project->id,
            'name' => $project->name,
            'address' => $project->address,
            'presentation' => $project->presentation,
            'region_id' => $project->region_id,
            'region' => [
                "region_name" => $project->region->nom_region ?? null,
                "id_city"     => $project->region->ville_id ?? null,
                "city" => [
                    "city_name" => $project->region->ville->nom_ville ?? null,
                ],
            ],
            'type' => $project->type,
            'user' => $project->user,
            'apartments_count' => $project->nb_appartements, // traduit
            'surface' => $project->surface,
            'email' => $project->email,
            'user_id' => $project->user_id,
            'cover_photo' => $project->photo_couverture, // traduit
            'logo' => $project->logo,
            'gallery_images' => json_decode($project->gallerie_images, true), // traduit
            'gallery_videos' => json_decode($project->gallerie_videos, true), // traduit
            'created_at' => $project->created_at,
            'updated_at' => $project->updated_at,
        ];
    });


        return response()->json([
            'status' => 'success',
            'message' => 'Project list retrieved successfully',
            'projects' => $projects
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Error retrieving projects',
            'error' => $e->getMessage()
        ], 500);
    }
}

public function get_projectById($id)
{
    try {
        $project = Project::with('user', 'region', 'caracteristiques.option','type')->find($id);

        if (!$project) {
            return response()->json([
                'status' => 'error',
                'message' => 'Project not found',
            ], 404);
        }

        // On utilise $item dans la closure, pas $project
        $project = collect([$project])->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'address' => $item->address,
                'presentation' => $item->presentation,
                'region_id' => $item->region_id,
                'apartments_count' => $item->nb_appartements, // traduit
                'surface' => $item->surface,
                'email' => $item->email,
                'user_id' => $item->user_id,
                'cover_photo' => $item->photo_couverture, // traduit
                'logo' => $item->logo,
                'gallery_images' => json_decode($item->gallerie_images, true), // traduit
                'gallery_videos' => json_decode($item->gallerie_videos, true), // traduit
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at,
            ];
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Project retrieved successfully',
            'project' => $project
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Error retrieving project',
            'error' => $e->getMessage()
        ], 500);
    }
}

    public function getProjectByRegion(Request $request)
    {
        try {
            $regionId = $request->region_id;

            $projects = Project::with('user', 'region','type')
                ->where('region_id', $regionId)
                ->get();

            if ($projects->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No projects found in this region.',
                    'region_id' => $regionId
                ], 404);
            }
    $projects = $projects->map(function ($item) {
    return [
        'id' => $item->id,
        'name' => $item->name,
        'address' => $item->address,
        'presentation' => $item->presentation,
        'region_id' => $item->region_id,
        'region_name' => $item->region->nom_region ?? null, // traduit
        'apartments_count' => $item->nb_appartements, // traduit
        'surface' => $item->surface,
        'email' => $item->email,
        'user_id' => $item->user_id,
        'cover_photo' => $item->photo_couverture, // traduit
        'logo' => $item->logo,
        'gallery_images' => json_decode($item->gallerie_images, true), // traduit
        'gallery_videos' => json_decode($item->gallerie_videos, true), // traduit
        'created_at' => $item->created_at,
        'updated_at' => $item->updated_at,
        'user' => [
            'id' => $item->user->id ?? null,
            'name' => $item->user->name ?? null,
            'email' => $item->user->email ?? null,
            'phone_number' => $item->user->num_tel ?? null, // traduit
            'pages' => $item->user->pages ?? null,
            'created_at' => $item->user->created_at ?? null,
            'updated_at' => $item->user->updated_at ?? null,
        ],

    ];
});

            return response()->json([
                'status' => 'success',
                'message' => 'Projects retrieved successfully.',
                'projects' => $projects
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error retrieving projects.',
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
                    'message' => 'Project not found',
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string',
                'address' => 'sometimes|required|string',
                'presentation' => 'sometimes|required|string',
                'region_id' => 'sometimes|required|exists:region,id',
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
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Update files
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

            // Update simple fields
            $project->fill($request->only($project->getFillable()));
            $project->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Project successfully updated',
                'project' => $project
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error updating project',
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
                    'message' => 'Project not found',
                ], 404);
            }

            $project->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Project successfully deleted',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error deleting project',
                'error' => $e->getMessage()
            ], 500);
        }
    }

public function getProjectByCity(Request $request)
{
    try {
        $ville_id = $request->ville_id;

        $projects = Project::join('region', 'projects.region_id', '=', 'region.id')
            ->join('ville', 'region.ville_id', '=', 'ville.id')   
            ->where('ville.id', $ville_id)
            ->get();

        if ($projects->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No projects found in this city.',
                'city_id' => $ville_id
            ], 404);
        }

        // Transformation avec traduction des champs
        $projects = $projects->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'address' => $item->address,
                'presentation' => $item->presentation,
                'region_id' => $item->region_id,
                'apartments_count' => $item->nb_appartements, // traduit
                'surface' => $item->surface,
                'email' => $item->email,
                'user_id' => $item->user_id,
                'cover_photo' => $item->photo_couverture, // traduit
                'logo' => $item->logo,
                'gallery_images' => json_decode($item->gallerie_images, true), // traduit
                'gallery_videos' => json_decode($item->gallerie_videos, true), // traduit
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at,
                'city_name'=>$item->nom_ville,
            ];
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Projects retrieved successfully.',
            'projects' => $projects
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Error retrieving projects.',
            'error' => $e->getMessage()
        ], 500);
    }
}

    public function filterProjects(Request $request)
{
    if ($request->has('region_id')) {
        return $this->getProjectByRegion($request);
    } elseif ($request->has('ville_id')) {
        return $this->getProjectByCity($request);
    } else {
        return $this->display();
    }
}

}
