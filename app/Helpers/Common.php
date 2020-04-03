<?php
namespace App\Helpers;

use Illuminate\Support\Str;
use App\Category;
use App\Unit;
use App\Permission;
use App\Role;
use App\Setting;
use App\Log;

class Common {

    public static function formattedNumber($value) {
        $setting = Setting::first();
        if (is_numeric($value) && floor($value) != $value) {
            return $setting->currency.number_format($value, 2, !empty($setting->decimal_separator) ? $setting->decimal_separator : '' , !empty($setting->thousand_separator) ? $setting->thousand_separator : '');
        } else {
            return $setting->currency.number_format($value, 0, !empty($setting->decimal_separator) ? $setting->decimal_separator : '' , !empty($setting->thousand_separator) ? $setting->thousand_separator : '');
        }
    }

    public static function Log($message) {

        $log = new Log;
        $log->message = $message;
        $log->origin = request()->headers->get('origin');
        $log->ip = request()->server('REMOTE_ADDR');
        $log->user_agent = request()->server('HTTP_USER_AGENT');
        $log->save();

        return true;
    }

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