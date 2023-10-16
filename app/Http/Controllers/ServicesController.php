<?php

namespace App\Http\Controllers;

use App\Models\ServicesModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;


class ServicesController extends Controller
{
    public function example(Request $request)
    {
        return response()->json([
            'message' => "This is an example API call.",
        ], 200);
    }

    public function getAllServices($accountNum, Request $request)
    {
        // Specify the number of items per page.
        if ($request->filled('page_size')) {
            $page_size = intval($request->input('page_size'));
            if ($page_size > 1000) {
                return response([
                    'message' => "Page size too high, 1000 page size is the current limit",
                ], 400);
            }
        } else {
            $page_size = 100;
        }

        // Filter services by accountNum
        $services = ServicesModel::where('accountNum', $accountNum)->paginate($page_size);

        $pagination = [
            'page' => $services->currentPage(),
            'page_size' => $page_size,
            'size' => $services->count(),
            // 'next_page' => $services->nextPageUrl(),
            // 'last_page' => $services->lastPage(),
            'filteredCount' => $services->total(),
        ];

        return response()->json([
            'Services' => $services->items(),
            'Summary' => $pagination,
            'id' => $accountNum
        ], 200);
    }


}