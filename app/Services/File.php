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

    public static function unzipFile($file, $extractPath)
    {
        $zip = new \ZipArchive;
        if ($zip->open($file)) {
            $zip->extractTo($extractPath);
            $zip->close();
        }
    }

    public static function listFileInFolder($path)
    {
        return Storage::files($path);
    }

    public static function getFileBinaryData($path)
    {
        $file = fopen($path, "rb");
        $data = fread($file, filesize($path));
        fclose($file);
        return $data;
    }

    public static function matchShortenLinksToData($rows, $dataWithPosts)
    {
        foreach ($rows as $i => $row) {
            if ($i == 0) continue;
            $rows[$i][1] = $dataWithPosts[$i - 1]['link'];
            $rows[$i][2] = $dataWithPosts[$i - 1]['id'];
        }
        return $rows;
    }

    public static function storeResultDataToFileCache($result, $key)
    {
        // store data to cache
        $data = Cache::get($key);
        if (empty($data)) {
            $data = [];
        }
        $data[time()] = $result;
        Cache::forever($key, $data);
    }
}
