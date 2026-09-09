<?php

namespace App\Http\Controllers\Admin\Media;

use App\Core\Media\MediaService;
use App\Domains\Media\Http\Requests\UpdateMediaRequest;
use App\Domains\Media\Http\Requests\UploadMediaRequest;
use App\Domains\Media\Models\Media;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MediaController extends Controller
{
    public function __construct(
        private readonly MediaService $media,
    ) {
    }

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Media::class);

        return view('admin.media.index', [
            'media' => Media::query()
                ->with('model')
                ->latest()
                ->paginate(20)
                ->withQueryString(),
        ]);
    }

    public function store(UploadMediaRequest $request): RedirectResponse
    {
        // Library upload: stored unattached until an entity claims it.
        $this->media->attach(
            model: null,
            file: $request->file('file'),
            collection: $request->validated('collection'),
            altText: $request->validated('alt_text'),
        );

        return back()->with('status', __('blue.admin.flash.created'));
    }

    public function update(UpdateMediaRequest $request, Media $media): RedirectResponse
    {
        $media->update([
            'alt_text' => $request->validated('alt_text'),
        ]);

        return back()->with('status', __('blue.admin.flash.updated'));
    }

    public function destroy(Media $media): RedirectResponse
    {
        $this->authorize('delete', $media);

        $this->media->deleteFile($media);

        return back()->with('status', __('blue.admin.flash.deleted'));
    }
}
