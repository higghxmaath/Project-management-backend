<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Card;
use App\Models\Attachment;
use Illuminate\Http\Request;

class AttachmentController extends Controller
{
    public function store(Request $request, Card $card)
    {
        $validated = $request->validate([
            'filename' => 'required|string',
            'url' => 'required|string',
        ]);

        $attachment = $card->attachments()->create($validated);

        return response()->json($attachment);
    }
}
