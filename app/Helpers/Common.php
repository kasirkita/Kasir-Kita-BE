<?php
namespace Helpers\Common;

use Illuminate\Support\Str;
use App\Category;
use App\Unit;
use App\Permission;
use App\Role;

class Common {

    public static function createSlug($title, $type, $id = 0)
    {
        $slug = Str::slug($title);
        $allSlugs = Common::getRelatedSlugs($slug, $type, $id);
        
        if (! $allSlugs->contains('slug', $slug)){
            return $slug;
        }

        for ($i = 1; $i <= 10; $i++) {
            $newSlug = $slug.'-'.$i;
            if (! $allSlugs->contains('slug', $newSlug)) {
                return $newSlug;
            }
        }

        throw new \Exception('Can not create a unique slug');
    }

    protected static function getRelatedSlugs($slug, $type, $id = 0)
    {
        if ($type == 'permission') {
            return Permission::select('slug')->where('slug', 'like', $slug.'%')
                ->where('_id', '<>', $id)
                ->get();
        }

        if ($type == 'role') {
            return Role::select('slug')->where('slug', 'like', $slug.'%')
                ->where('_id', '<>', $_id)
                ->get();
        }

        if ($type == 'category') {
            return Category::select('slug')->where('slug', 'like', $slug.'%')
                ->where('_id', '<>', $id)
                ->get();
        }

        if ($type == 'unit') {
            return Unit::select('slug')->where('slug', 'like', $slug.'%')
                ->where('_id', '<>', $id)
                ->get();
        }
    }
}