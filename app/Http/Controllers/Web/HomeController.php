<?php
namespace App\Http\Controllers\Web;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Inertia\Inertia;
use App\Models\ExchangeRate;

class HomeController extends Controller
{
    public function index()
    {
        $records = ExchangeRate::select('*')->orderBy('ordering', 'asc')->get();
       
        return Inertia::render('Home', [
            'records' => $records,
        ]);



    }
}
