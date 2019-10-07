<?php
/**
 * Created by PhpStorm.
 * User: dungdc40
 * Date: 10/4/2019
 * Time: 5:31 PM
 */

namespace App\Services;
use Illuminate\Support\Facades\Storage;
use App\Services\File;
use Facebook\Facebook;

class FacebookPageService
{
    public static $MAX_NUM_TRY = 2;


    public static function findPageById($pages, $id) {
        foreach ($pages as $page) {
            if($page['id'] == $id) {
                return $page;
            }
        }
        return false;
    }

    public static function postToPage($page, $content, $data, $directoryPath) {
        $files = File::listFileInFolder($directoryPath);
        $fileMapping = self::getMappingFileName($files);
        $fullPath = storage_path('app/' . $directoryPath);
        foreach ($data as $i => $row) {
            if ($i === 0) {
                continue;
            }

            $link = $row[0];
            $linkParts = explode('/', $link);
            $linkLastPart = strtoupper($linkParts[count($linkParts) - 1]);
            $postImage = null;

            // jpg or png file exist
            $imgJpg = strtoupper($linkLastPart. '.jpg');
            $imgPng = strtoupper($linkLastPart. '.png');
            if(array_key_exists($imgJpg, $fileMapping)) {
                $postImage = $fullPath . '/' . $fileMapping[$imgJpg];
            } else if(array_key_exists($imgPng, $fileMapping)) {
                $postImage = $fullPath . '/' . $fileMapping[$imgPng];
            }

            $postContent = ($content ? $content . ' ' : '' ) . $link;
            try {
                $postId = self::tryPost($page['id'], $postContent, $postImage, $page['access_token']);
                $data[$i][2] = $postId;
            } catch (\Exception $e) {
                $data[$i][2] = 0;
                $data[$i][3] = $e->getMessage();
            }
        }
        return $data;
    }

    // make file name uppercase and use it for mapping
    public static function getMappingFileName($data) {
        $files = [];
        foreach ($data as $file) {
            $fileParts = explode('/', $file);
            $fileName = strtoupper($fileParts[count($fileParts) - 1]);
            $files[strtoupper($fileName)] = $fileParts[count($fileParts) - 1];
        }
        return $files;
    }

    public static function tryPost($pageId, $content, $file, $token) {
        $fb = app(Facebook::class);

        for($i = 0; $i < self::$MAX_NUM_TRY; $i++) {
            try {

                if(!empty($file)) {
                    $postData = [
                        'caption' => $content,
                        'access_token' => $token,
                        'object_attachment' => $fb->fileToUpload($file)
                    ];
                    $res = $fb->post('/' . $pageId . '/photos', $postData);
                    $node = $res->getGraphNode();
                    return $node['post_id'];
                } else {
                    $res = $fb->post('/' . $pageId . '/feed', [
                        'message' => $content,
                        'access_token' => $token,
                    ]);
                    $node = $res->getGraphNode();
                    return $node['id'];
                }

            } catch(\Exception $e) {
                // reach max try
                if($i == self::$MAX_NUM_TRY - 1) {
                    throw new \Exception($e->getMessage());
                }
            }

        }
    }
}