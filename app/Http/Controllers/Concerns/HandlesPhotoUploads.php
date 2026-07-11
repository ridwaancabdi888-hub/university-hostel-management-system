<?php

namespace App\Http\Controllers\Concerns;

use App\Support\RemoteImageFetcher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

trait HandlesPhotoUploads
{
    /**
     * Resolve the photo path for a create/update request: an uploaded file
     * takes priority over a pasted URL; if neither is present, the existing
     * path (if any) is left untouched. Replaces the existing stored file
     * whenever a new one is provided.
     */
    private function resolvePhotoPath(Request $request, ?string $existingPath, string $directory): ?string
    {
        if ($request->hasFile('photo')) {
            if ($existingPath) {
                Storage::disk('public')->delete($existingPath);
            }

            return $request->file('photo')->store($directory, 'public');
        }

        if ($request->filled('photo_url')) {
            try {
                $path = RemoteImageFetcher::download($request->string('photo_url')->toString(), $directory);
            } catch (InvalidArgumentException $e) {
                throw ValidationException::withMessages(['photo_url' => $e->getMessage()]);
            }

            if ($existingPath) {
                Storage::disk('public')->delete($existingPath);
            }

            return $path;
        }

        return $existingPath;
    }
}
