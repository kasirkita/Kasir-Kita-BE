<?php

namespace App\Helpers;

class Pages {

    public static function generate($data) : array
    {
        $pages = [];

        if ($data->hasPages()){

            if($data->currentPage() > 3) {
                $pages[] = 1;
            }
            
            if($data->currentPage() > 4) {
                $pages[] = '...';
            }

            foreach(range(1, $data->lastPage()) as $i) {

                if($i >= $data->currentPage() - 2 && $i <= $data->currentPage() + 2) {
                    $pages[] = $i;
                }
            }

            if($data->currentPage() < $data->lastPage() - 3) {
                $pages[] = '...';
            }
            
            if($data->currentPage() < $data->lastPage() - 2) {
                $pages[] = $data->lastPage();
            }
            
        }

        return $pages;
    }
}