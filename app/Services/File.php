<?php
/**
 * Created by PhpStorm.
 * User: dungdc40
 * Date: 10/4/2019
 * Time: 6:01 PM
 */

namespace App\Services;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class File
{
    public static function moveUploadFileToPath($file, $path)
    {
        if ($file['name'] == '') return false;

        $file_name = $file['name'];
        if (!is_dir($path)) {
            mkdir($path);
        }

        $location = $path . $file_name;
        if (move_uploaded_file($file['tmp_name'], $location)) {
            return $location;
        } else {
            return false;
        }
    }

    public static function ifFileIsZip($file)
    {
        if (!$file) return false;
        return $file->extension() === 'zip';
    }

    public static function unzipFile($file, $extractPath) {
        $zip = new \ZipArchive;
        if ($zip->open($file)) {
            $zip->extractTo($extractPath);
            $zip->close();
        }
    }

    public static function listFileInFolder($path) {
        return Storage::files($path);
    }

    public static function getFileBinaryData($path) {
        $file = fopen($path, "rb");
        $data = fread($file, filesize($path));
        fclose($file);
        return $data;
    }

    public static function matchShortenLinksToData($shortenIds, $dataWithPosts) {
        foreach ($dataWithPosts as $i => $row) {
            if($i == 0) continue;
            $postId = $row[2];
            if(array_key_exists($postId, $shortenIds)) {
                $shortenLink = $shortenIds[$postId];

                if(isset($shortenLink['error'])) {
                    $dataWithPosts[$i][4] = $shortenIds[$postId]['error'];
                } else {
                    $dataWithPosts[$i][1] = $shortenLink['link'];
                }
            }
        }
        return $dataWithPosts;
    }

    public static function storeResultDataToFileCache($result, $key) {
        // store data to cache
        $data = Cache::get($key);
        if(empty($data)) {
            $data = [];
        }
        $data[time()] = $result;
        Cache::forever($key, $data);
    }
}