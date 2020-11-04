<?php

namespace App\Http\Controllers\Web\Admin;

//use App\Data\Models\AnnualReport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AnnualReports extends Controller
{
    public function index(Request $request)
    {
        return $this->view('admin.annualReports.index');     
    }
}
