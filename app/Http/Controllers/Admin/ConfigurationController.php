<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Configuration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ConfigurationController extends Controller
{
    public function index(Request $request)
    {
        $query = Configuration::query();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        if ($request->filled('country_id')) {
            $query->where('country_id', $request->country_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('unique_id', 'like', "%{$search}%")
                  ->orWhere('value', 'like', "%{$search}%");
            });
        }

        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $configurations = $query->paginate(15)->withQueryString();

        $stats = [
            'total'   => Configuration::count(),
            'active'  => Configuration::where('is_active', true)->count(),
            'inactive'=> Configuration::where('is_active', false)->count(),
            'types'   => Configuration::select('type')->distinct()->pluck('type')->toArray(),
        ];

        $countries = \App\Models\Country::orderBy('name')->get();

        return view('admin.configurations.index', compact('configurations', 'stats', 'countries'));
    }

    public function create()
    {
        $countries = \App\Models\Country::orderBy('name')->get();
        return view('admin.configurations.create', compact('countries'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'      => 'required|string|max:255',
            'unique_id'  => [
                'nullable', 'string', 'max:255',
                // \Illuminate\Validation\Rule::unique('configurations')->where(function ($query) use ($request) {
                //     return $query->where('country_id', $request->country_id ?? null);
                // }),
                // removed unique rule - handled via upsert below
            ],
            'type'       => 'required|in:integer,string,array,boolean,json,text,file',
            'value'      => 'required_unless:type,file',
            'files'      => 'required_if:type,file|array',
            'files.*'    => 'file|max:10240',
            'country_id' => 'nullable|exists:countries,id',
            'is_active'  => 'boolean',
        ]);

        if (empty($validated['unique_id'])) {
            $validated['unique_id'] = Str::slug($validated['title'], '_');
        }

        $filesData = null;

        if ($validated['type'] === 'file' && $request->hasFile('files')) {
            $uploadedFiles = [];
            foreach ($request->file('files') as $file) {
                $path = $file->store('configurations', 'public');
                $uploadedFiles[] = [
                    'path' => $path,
                    'url'  => Storage::disk('public')->url($path),
                    'name' => $file->getClientOriginalName(),
                ];
            }
            $validated['value'] = $uploadedFiles[0]['path'];
            $filesData = $uploadedFiles;
        } elseif (in_array($validated['type'], ['array', 'json'])) {
            if (is_string($validated['value'])) {
                $decoded = json_decode($validated['value'], true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $validated['value'] = $decoded;
                }
            }
        }

        // Configuration::create([
        //     'unique_id'  => $validated['unique_id'],
        //     'title'      => $validated['title'],
        //     'type'       => $validated['type'],
        //     'value'      => $validated['value'],
        //     'files'      => $filesData,
        //     'is_active'  => $validated['is_active'] ?? true,
        //     'country_id' => $validated['country_id'] ?? null,
        // ]);
        $existing = Configuration::where('unique_id', $validated['unique_id'])
    ->where('country_id', $validated['country_id'] ?? null)
    ->first();

if ($existing && $validated['type'] === 'file') {
    $existingFiles = $existing->files ?? [];
    if ($filesData) {
        $existingFiles = array_merge($existingFiles, $filesData);
    }
    $existing->update([
        'type'       => $validated['type'],
        'value'      => $existingFiles[0]['path'] ?? $existing->value,
        'files'      => $existingFiles,
        'is_active'  => $validated['is_active'] ?? true,
        'country_id' => $validated['country_id'] ?? null,
    ]);
} elseif ($existing) {
    $existing->update([
        'type'       => $validated['type'],
        'value'      => $validated['value'],
        'files'      => null,
        'is_active'  => $validated['is_active'] ?? true,
        'country_id' => $validated['country_id'] ?? null,
    ]);
} else {
    Configuration::create([
        'unique_id'  => $validated['unique_id'],
        'title'      => $validated['title'],
        'type'       => $validated['type'],
        'value'      => $validated['value'],
        'files'      => $filesData,
        'is_active'  => $validated['is_active'] ?? true,
        'country_id' => $validated['country_id'] ?? null,
    ]);
}

        return redirect()->route('admin.configurations.index')
            ->with('success', 'Configuration created successfully!');
    }



    public function edit(Configuration $configuration)
    {
        $countries = \App\Models\Country::orderBy('name')->get();
        return view('admin.configurations.edit', compact('configuration', 'countries'));
    }

    public function update(Request $request, Configuration $configuration)
    {
        $validated = $request->validate([
            // 'unique_id'  => [
            //     'nullable', 'string', 'max:255',
            //     \Illuminate\Validation\Rule::unique('configurations')->where(function ($query) use ($request) {
            //         return $query->where('country_id', $request->country_id ?? null);
            //     })->ignore($configuration->id),
            // ],
            'unique_id'  => [
                'nullable', 'string', 'max:255',
                \Illuminate\Validation\Rule::unique('configurations')->where(function ($query) use ($request) {
                    if ($request->country_id) {
                        $query->where('country_id', $request->country_id);
                    } else {
                        $query->whereNull('country_id');
                    }
                    return $query;
                })->ignore($configuration->id),
            ],
            'type'       => 'required|in:integer,string,array,boolean,json,text,file',
            'value'      => 'required_unless:type,file',
            'files'      => 'nullable|array',
            'files.*'    => 'file|max:10240',
            'country_id' => 'nullable|exists:countries,id',
            'is_active'  => 'boolean',
        ]);

        $filesData = $configuration->files;

        // if ($validated['type'] === 'file' && $request->hasFile('files')) {
        //     // Delete old files
        //     foreach ($configuration->files ?? [] as $oldFile) {
        //         if (Storage::disk('public')->exists($oldFile['path'])) {
        //             Storage::disk('public')->delete($oldFile['path']);
        //         }
        //     }
        //     $uploadedFiles = [];
        //     foreach ($request->file('files') as $file) {
        //         $path = $file->store('configurations', 'public');
        //         $uploadedFiles[] = [
        //             'path' => $path,
        //             'url'  => Storage::disk('public')->url($path),
        //             'name' => $file->getClientOriginalName(),
        //         ];
        //     }
        //     $validated['value'] = $uploadedFiles[0]['path'];
        //     $filesData = $uploadedFiles;
        // } elseif ($validated['type'] === 'file') {
        //     $validated['value'] = $configuration->value;
        if ($validated['type'] === 'file') {
    $existingFiles = $configuration->files ?? [];

    // Remove individually selected files
    $removeFiles = $request->input('remove_files', []);
    foreach ($existingFiles as $oldFile) {
        if (in_array($oldFile['path'], $removeFiles)) {
            if (Storage::disk('public')->exists($oldFile['path'])) {
                Storage::disk('public')->delete($oldFile['path']);
            }
        }
    }
    $existingFiles = array_values(array_filter($existingFiles, fn($f) => !in_array($f['path'], $removeFiles)));

    // Append new uploads
    if ($request->hasFile('files')) {
        foreach ($request->file('files') as $file) {
            $path = $file->store('configurations', 'public');
            $existingFiles[] = [
                'path' => $path,
                'url'  => Storage::disk('public')->url($path),
                'name' => $file->getClientOriginalName(),
            ];
        }
    }

    $filesData = $existingFiles;
    $validated['value'] = !empty($filesData) ? $filesData[0]['path'] : $configuration->value;
        } elseif (in_array($validated['type'], ['array', 'json'])) {
            if (is_string($validated['value'])) {
                $decoded = json_decode($validated['value'], true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $validated['value'] = $decoded;
                }
            }
        }

        $configuration->update([
            'unique_id'  => $validated['unique_id'],
            'type'       => $validated['type'],
            'value'      => $validated['value'],
            'files'      => $filesData,
            'is_active'  => $validated['is_active'] ?? $configuration->is_active,
            'country_id' => $validated['country_id'] ?? $configuration->country_id,
        ]);

        return redirect()->route('admin.configurations.index')
            ->with('success', 'Configuration updated successfully!');
    }

    public function destroy(Configuration $configuration)
    {
        if ($configuration->type === 'file') {
            foreach ($configuration->files ?? [] as $file) {
                if (Storage::disk('public')->exists($file['path'])) {
                    Storage::disk('public')->delete($file['path']);
                }
            }
        }

        $configuration->delete();

        return redirect()->route('admin.configurations.index')
            ->with('success', 'Configuration deleted successfully!');
    }

    public function toggleStatus(Configuration $configuration)
    {
        $configuration->update(['is_active' => !$configuration->is_active]);
        $status = $configuration->fresh()->is_active ? 'activated' : 'deactivated';
        return redirect()->back()->with('success', "Configuration {$status} successfully!");
    }

    public function print(Request $request)
    {
        $query = Configuration::query();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('unique_id', 'like', "%{$search}%")
                  ->orWhere('value', 'like', "%{$search}%");
            });
        }

        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $configurations = $query->get();

        return view('admin.configurations.print', compact('configurations'));
    }
}
