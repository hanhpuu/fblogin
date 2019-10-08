<?php

namespace App\Http\Controllers;

use App\Services\Bitly;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\CsvImport;
use App\Exports\CsvExport;
use App\Services\FacebookPageService;
use App\Services\File;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{

    /**
     * Show the step 1 Form for creating a new post.
     * @param $request Request object
     * @return \Illuminate\Http\Response
     */
    public function step1(Request $request)
    {
        if ($request->isMethod('get')) {
            $post = $request->session()->get('post');
            return view('posts.step1', compact('post', $post));
        } else if ($request->isMethod('post')) {
            $request->session()->put('content', $request->input('content'));
            return redirect()->route('step2');
        }

    }

    /**
     * Show the Post Review page
     * @param $request Request object
     * @return \Illuminate\Http\Response
     */
    public function step2(Request $request)
    {
        $pages = $request->session()->get('fbPages');

        if (!$pages) {
            return redirect()->route('step1')->with('error', 'Can not get fan page list, please try logout and login again');
        }

        if ($request->isMethod('get')) {
            return view('posts.step2')->with('pages', $pages);
        } else if ($request->isMethod('post')) {
            $pageId = $request->input('page_id');
            $page = FacebookPageService::findPageById($pages, $pageId);
            if ($page !== false) {
                $request->session()->put('page', $page);
                return redirect()->route('step3');
            } else {
                // force to choose one page to post to
                return view('posts.step2')->with('pages', $pages);
            }
        }
    }

    /**
     * Show the step 2 Form for creating a new post.
     * @param $request Request object
     * @return \Illuminate\Http\Response
     */
    public function step3(Request $request)
    {

        if ($request->isMethod('get')) {
            return view('posts.step3');
        } else if ($request->isMethod('post')) {
            set_time_limit(0);
            try {
                // copy file to storage
                $directory = time();
                $directoryPath = 'upload/' . $directory;
                Storage::makeDirectory($directoryPath);
                foreach ($request->file('file') as $file) {
                    $file->storeAs($directoryPath, $file->getClientOriginalName());
                }

                // read csv file
                $path = storage_path('app/' . $directoryPath);
                $rows = Excel::toArray(new CsvImport, $path . '/Link.csv');

                $shortenLinks = Bitly::getShortenLinkByFbIds($rows[0], $request->session()->get('bitlyToken'));
                $dataWithPosts = FacebookPageService::postToPage($request->session()->get('page'), $request->session()->get('content'), $rows[0], $shortenLinks, $directoryPath);
                $finalData = File::matchShortenLinksToData($rows[0], $dataWithPosts);

                File::storeResultDataToFileCache($dataWithPosts, 'fbReports');

                // remove upload directory
                try {
                    Storage::deleteDirectory($directoryPath);
                } catch (\Exception $e) {

                }

                return Excel::download(new CsvExport($finalData), 'Result.csv');

            } catch (\Exception $e) {
                dd($e->getMessage());
                return redirect()->route('step3')->with('error', $e->getMessage());
            }
        }
    }

    public function clearUploadFolder()
    {
        // remove upload directory
        try {
            Storage::deleteDirectory('upload');
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
        return redirect()->route('home');
    }
}
