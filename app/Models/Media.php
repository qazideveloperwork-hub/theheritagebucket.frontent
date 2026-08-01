<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $table = 'media';

    protected function casts(): array
    {
        return [
            'variants' => 'array',
        ];
    }

    // The `media` table (and its uploaded files) belong to the admin app, so
    // URLs are built against the admin app's own storage, not this app's.
    public function url(): string
    {
        return rtrim(config('services.admin.url'), '/').'/storage/'.$this->path;
    }

    public function variantUrl(string $size): string
    {
        $path = $this->variants[$size] ?? null;

        return rtrim(config('services.admin.url'), '/').'/storage/'.($path ?? $this->path);
    }

    public function thumbnailUrl(): string
    {
        return $this->variantUrl('thumbnail');
    }

    public function mediumUrl(): string
    {
        return $this->variantUrl('medium');
    }

    public function largeUrl(): string
    {
        return $this->variantUrl('large');
    }
}
