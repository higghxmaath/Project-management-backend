<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadController extends Controller
{
    public function presign(Request $request)
    {
        $data = $request->validate([
            'filename' => 'required|string',
            'mime'     => 'required|string',
        ]);

        $disk = Storage::disk('s3');

        $key = 'uploads/' . Str::uuid() . '-' . $data['filename'];

        $client = $disk->getClient();
        $bucket = config('filesystems.disks.s3.bucket');

        $command = $client->getCommand('PutObject', [
            'Bucket'      => $bucket,
            'Key'         => $key,
            'ContentType' => $data['mime'],
            'ACL'         => 'private',
        ]);

        $request = $client->createPresignedRequest($command, '+10 minutes');

        return response()->json([
            'upload_url' => (string) $request->getUri(),
            'file_key'   => $key,
        ]);
    }
}
