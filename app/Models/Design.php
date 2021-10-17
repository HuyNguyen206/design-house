<?php

namespace App\Models;

use App\Models\Traits\Likeable;
use App\Repositories\Contracts\CommentInterface;
use App\Repositories\Eloquent\Criteria\FilterByWhereIn;
use Cviebrock\EloquentTaggable\Taggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Design extends Model
{
    use HasFactory, Taggable, Likeable;

    protected $fillable = [
        'user_id',
        'team_id',
        'image',
        'title',
        'description',
        'slug',
        'close_to_comment',
        'is_live',
        'upload_success',
        'disk'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    private function getImageUrl($size = 'original')
    {
        $imageUrl = $size == 'original' ? $this->image : Str::replace('original', $size, $this->image);
        return Storage::disk($this->disk)->url($imageUrl);
    }

    public function getImagesAttribute()
    {
        return [
          'original_image' => $this->getImageUrl(),
          'large_image' => $this->getImageUrl('large'),
          'thumbnail_image' => $this->getImageUrl('thumbnail')
        ];
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    protected static function booted()
    {
      static::deleted(function($design) {
          $comments = $design->comments();
          $likedUsers = $design->likedUsers;
          if ($comments->count()) {
              $comments->delete();
          }

          if ($likedUsers->count()) {
              $design->likedUsers()->detach();
          }
      });
    }

    public function team()
    {
        return $this->belongsTo(Team::class);    }

}
