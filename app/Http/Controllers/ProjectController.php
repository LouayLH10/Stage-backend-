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
            'regionId' => 'required|exists:region,id',
            'numberOfAppartements' => 'required|integer',
            'surface' => 'required|numeric',
            'email' => 'required|email',
            'userId' => 'required|exists:users,id',
            'typeId' => 'required|exists:type,id',
            'coverphoto' => 'required|image|mimes:jpg,jpeg,png|max:10000',
            'logo' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'galleryimages.*' => 'nullable|image|mimes:jpg,jpeg,png|max:10000',
            'galleryvideos.*' => 'nullable|mimetypes:video/mp4,video/avi,video/mov|max:2000000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Vérifier que les dossiers de stockage existent
        $directories = ['projects/couvertures', 'projects/logos', 'projects/images', 'projects/videos'];
        foreach ($directories as $directory) {
            $path = storage_path('app/public/' . $directory);
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }
        }

        // 📦 Upload files avec vérification des erreurs
        $photoCouverturePath = $request->file('coverphoto')->store('projects/couvertures', 'public');
        if ($photoCouverturePath === false) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to upload cover photo'
            ], 500);
        }

        $logoPath = $request->file('logo')->store('projects/logos', 'public');
        if ($logoPath === false) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to upload logo'
            ], 500);
        }

        $galleryImagesPaths = [];
        if ($request->hasFile('galleryimages')) {
            foreach ($request->file('galleryimages') as $img) {
                $path = $img->store('projects/images', 'public');
                if ($path === false) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Failed to upload gallery image'
                    ], 500);
                }
                $galleryImagesPaths[] = $path;
            }
        }

        $galleryVideosPaths = [];
        if ($request->hasFile('galleryvideos')) {
            foreach ($request->file('galleryvideos') as $video) {
                $path = $video->store('projects/videos', 'public');
                if ($path === false) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Failed to upload gallery video'
                    ], 500);
                }
                $galleryVideosPaths[] = $path;
            }
        }

        // 🧱 Create project
        $project = Project::create([
            'name' => $request->name,
            'address' => $request->address,
            'presentation' => $request->presentation,
            'regionId' => $request->regionId,
            'numberOfAppartements' => $request->numberOfAppartements,
            'surface' => $request->surface,
            'email' => $request->email,
            'userId' => $request->userId,
            'coverphoto' => $photoCouverturePath,
            'logo' => $logoPath,
            'typeId' => $request->typeId,
            'galleryimages' => !empty($galleryImagesPaths) ? json_encode($galleryImagesPaths) : null,
            'galleryvideos' => !empty($galleryVideosPaths) ? json_encode($galleryVideosPaths) : null,
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
       $projects = Project::with('user', 'region.city', 'Features.option', 'type')
    ->orderBy('created_at', 'desc')
    ->get();
   

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
        $project = Project::with('user', 'region', 'Features.option','type')->find($id);

        if (!$project) {
            return response()->json([
                'status' => 'error',
                'message' => 'Project not found',
            ], 404);
        }

        // On utilise $item dans la closure, pas $project
      

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
            $regionId = $request->regionId;

            $projects = Project::with('user', 'region','type')
                ->where('regionId', $regionId)
                ->get();

            if ($projects->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No projects found in this region.',
                    'regionId' => $regionId
                ], 404);
            }
    $projects = $projects->map(function ($item) {
    return [
        'id' => $item->id,
        'name' => $item->name,
        'address' => $item->address,
        'presentation' => $item->presentation,
        'regionId' => $item->regionId,
        'region_name' => $item->region->region ?? null, // traduit
        'apartments_count' => $item->numberOfAppartements, // traduit
        'surface' => $item->surface,
        'email' => $item->email,
        'userId' => $item->userId,
        'cover_photo' => $item->coverphoto, // traduit
        'logo' => $item->logo,
        'gallery_images' => json_decode($item->galleryimages, true), // traduit
        'gallery_videos' => json_decode($item->galleryvideos, true), // traduit
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
            'regionId' => 'sometimes|required|exists:region,id',
            'numberOfAppartements' => 'sometimes|required|integer',
            'surface' => 'sometimes|required|numeric',
            'email' => 'sometimes|required|email',
            'userId' => 'sometimes|required|exists:users,id',

            'coverphoto' => 'nullable|file|image|max:2048',
            'logo' => 'nullable|file|image|max:2048',
            'galleryimages.*' => 'nullable|file|image|max:2048',
            'galleryvideos.*' => 'nullable|file|mimetypes:video/mp4,video/avi,video/mov|max:51200',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Mettre à jour les champs standards
        $project->fill($request->only([
            'name', 'address', 'presentation', 'regionId', 
            'numberOfAppartements', 'surface', 'email', 'userId'
        ]));

        // Update files
        if ($request->hasFile('coverphoto')) {
            // Supprimer l'ancienne image si elle existe
            if ($project->coverphoto) {
                Storage::disk('public')->delete($project->coverphoto);
            }
            $project->coverphoto = $request->file('coverphoto')->store('projects/couvertures', 'public');
        }

        if ($request->hasFile('logo')) {
            // Supprimer l'ancien logo s'il existe
            if ($project->logo) {
                Storage::disk('public')->delete($project->logo);
            }
            $project->logo = $request->file('logo')->store('projects/logos', 'public');
        }

        if ($request->hasFile('galleryimages')) {
            // Supprimer les anciennes images si elles existent
            if ($project->galleryimages) {
                $oldImages = json_decode($project->galleryimages, true);
                foreach ($oldImages as $oldImage) {
                    Storage::disk('public')->delete($oldImage);
                }
            }
            
            $imgPaths = [];
            foreach ($request->file('galleryimages') as $img) {
                $imgPaths[] = $img->store('projects/images', 'public');
            }
            $project->galleryimages = json_encode($imgPaths);
        }

        if ($request->hasFile('galleryvideos')) {
            // Supprimer les anciennes vidéos si elles existent
            if ($project->galleryvideos) {
                $oldVideos = json_decode($project->galleryvideos, true);
                foreach ($oldVideos as $oldVideo) {
                    Storage::disk('public')->delete($oldVideo);
                }
            }
            
            $videoPaths = [];
            foreach ($request->file('galleryvideos') as $vid) {
                $videoPaths[] = $vid->store('projects/videos', 'public');
            }
            $project->galleryvideos = json_encode($videoPaths);
        }

        // Sauvegarder toutes les modifications
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
        $cityId = $request->cityId;

        $projects = Project::join('region', 'projects.regionId', '=', 'region.id')
            ->join('city', 'region.cityId', '=', 'city.id')   
            ->where('city.id', $cityId)
            ->get();

        if ($projects->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No projects found in this city.',
                'city_id' => $cityId
            ], 404);
        }

        // Transformation avec traduction des champs
        $projects = $projects->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'address' => $item->address,
                'presentation' => $item->presentation,
                'regionId' => $item->regionId,
                'apartments_count' => $item->numberOfAppartements, // traduit
                'surface' => $item->surface,
                'email' => $item->email,
                'userId' => $item->userId,
                'cover_photo' => $item->coverphoto, // traduit
                'logo' => $item->logo,
                'gallery_images' => json_decode($item->galleryimages, true), // traduit
                'gallery_videos' => json_decode($item->galleryvideos, true), // traduit
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at,
                'city_name'=>$item->city,
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
    if ($request->has('regionId')) {
        return $this->getProjectByRegion($request);
    } elseif ($request->has('cityId')) {
        return $this->getProjectByCity($request);
    } else {
        return $this->display();
    }
}

}
