<?php

namespace App\Http\Controllers;

use Excel;
use Illuminate\Support\Facades\Cache;
use App\Exports\CsvExport;

class FbRePortController extends Controller
{

    public function remove($id) {
        $reports = Cache::get('fbReports');
        unset($reports[$id]);
        Cache::forever('fbReports', $reports);
        return redirect()->route('home');
    }

    public function download($id) {
        $reports = Cache::get('fbReports');
        $report = $reports[$id];
        if(empty($report)) return null;

        return Excel::download(new CsvExport($report), 'Result.xlsx');
    }
}
