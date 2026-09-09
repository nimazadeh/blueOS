<?php

namespace App\Http\Controllers\Public;

use App\Domains\Media\Models\Media;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Public media delivery.
 *
 * Originals stay out of the webroot (private `media` disk). This route serves
 * files attached to published entities; library/unattached media is never
 * disclosed publicly. Blue Control operators may preview anything.
 */
class MediaController extends Controller
{
    public function __invoke(Media $media): StreamedResponse
    {
        $allowed = $media->isPubliclyVisible() || auth('admin')->check();

        abort_unless($allowed, 404);

        return Storage::disk($media->disk)->response(
            $media->path,
            $media->filename,
            [
                'Content-Type' => $media->mime_type,
                'Cache-Control' => 'public, max-age=31536000, immutable',
            ],
        );
    }
}
