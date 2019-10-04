<?php

namespace App\Http\Controllers;

use App\Post;
use Facebook\Exceptions\FacebookSDKException;
use Illuminate\Http\Request;
use Excel;
use App\Imports\CsvImport;
use Socialite;
use app\Http\Controllers\app\Http\Controllers\SocialAuthFacebookController;
use Facebook\Facebook;

class PostController extends Controller
{

    /**
     * Show the step 1 Form for creating a new post.
     *
     * @return \Illuminate\Http\Response
     */
    public function createStep1(Request $request)
    {
        $post = $request->session()->get('post');
        return view('posts.create-step1', compact('post', $post));
    }

    /**
     * Post Request to store step1 info in session
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function postCreateStep1(Request $request)
    {
        $validatedData = $request->validate([
            'content' => 'required',
        ]);
        if (empty($request->session()->get('post'))) {
            $post = new Post();
            $post->fill($validatedData);
            $request->session()->put('post', $post);
        } else {
            $post = $request->session()->get('post');
            $post->fill($validatedData);
            $request->session()->put('post', $post);
        }

        return redirect('/posts/create-step2');
    }

    /**
     * Show the step 2 Form for creating a new post.
     *
     * @return \Illuminate\Http\Response
     */
    public function createStep2(Request $request)
    {
        $post = $request->session()->get('post');
        return view('posts.create-step2', compact('post', $post));
    }

    /**
     * Post Request to store step1 info in session
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function postCreateStep2(Request $request)
    {
        if (!isset($_POST["btnZip"])) {
            die;
        }

        if ($_FILES['zipFile']['name'] != '') {
            $file_name = $_FILES['zipFile']['name'];

            $array = explode(".", $file_name);
            $name = $array[0];
            $ext = $array[1];
            if ($ext == 'zip') {
                $path = 'upload/';
                if (!is_dir($path)) {
                    mkdir($path);
                }

                $location = $path . $file_name;
                if (move_uploaded_file($_FILES['zipFile']['tmp_name'], $location)) {
                    $zip = new \ZipArchive;
                    if ($zip->open($location)) {
                        $zip->extractTo($path);
                        $zip->close();
                    }

                    $arr = explode("-", $path . $name);
                    $files = scandir($arr[0]);
                    $csvOrderNumber = array_search("Link.csv", $files);

                    foreach ($files as $file) {
//                        unlink($arr[0] . '/' . $file);
                    }
//                    unlink($location);
//                    rmdir($path . $name);

                }
            }
        }


        // save csv file
        $csvFileName = "fileName-" . time() . '.csv';
        $request->postimg->storeAs('file', $csvFileName);
        $post = $request->session()->get('post');
        $post->csv = $fileName;
        // save it to session
        $request->session()->put('post', $post);

        $handle = fopen($_FILES["postimg"]["tmp_name"], 'r');
        $rows = Excel::toArray(new CsvImport, $request->file('postimg'));
        foreach ($rows[0] as $value) {
            if ($value[0] == 'URL') {
                continue;
            }
            $arr = explode('/', $value[0]);
            $photo = end($arr);
        }
        return redirect('/posts/create-step3');
    }

    /**
     * Show the Post Review page
     *
     * @return \Illuminate\Http\Response
     */
    public function removeImage(Request $request)
    {
        $post = $request->session()->get('post');
        $post->postImg = null;
        return view('posts.create-step2', compact('post', $post));
    }

    /**
     * Show the Post Review page
     *
     * @return \Illuminate\Http\Response
     */
    public function createStep3(Request $request)
    {
        $adminPages = session('adminPages');
        return view('posts.create-step3')->with('adminPages', $adminPages);;
    }

    public function getPageAccessToken()
    {

        $page_id = $_POST['page_access_id'];
        $fb = new Facebook([
            'app_id' => env('FACEBOOK_APP_ID'),
            'app_secret' => env('FACEBOOK_APP_SECRET'),
            'default_graph_version' => 'v2.10',
        ]);
        try {
            $client = new \GuzzleHttp\Client();
            $url = 'https://graph.facebook.com/' . $page_id . '/feed?message=456&from=' . $_POST['page_name'] . '&access_token=' . $_POST['page_access_token'];
            $res = $client->request('POST', $url);
            $body = $res->getBody();
            echo $body;
        } catch (FacebookSDKException $e) {
            dd($e); // handle exception
        }
    }
}
