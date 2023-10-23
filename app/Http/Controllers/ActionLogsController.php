<?php

namespace App\Http\Controllers;

use App\Models\ActionLogsModel;
use Illuminate\Http\Request;

class ActionLogsController extends Controller
{
    //
    public function getAccountNumActionLogs($accountNum, Request $request)
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

        $query = ActionLogsModel::where('accountNum', $accountNum);

        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where('data', 'like', '%' . $searchTerm . '%')
                ->orWhere('operation', 'like', '%' . $searchTerm . '%')
                ->orWhere('userID', 'like', '%' . $searchTerm . '%')
                ->orWhere('uID', 'like', '%' . $searchTerm . '%');
        }

        if ($request->filled('sort')) {
            $sortField = $request->input('sort');

            $sortOrder = $request->input('order', 'asc');

            $query->orderBy($sortField, $sortOrder);
        }

        $actionLogs = $query->paginate($page_size);

        $pagination = [
            'page' => $actionLogs->currentPage(),
            'page_size' => $page_size,
            'size' => $actionLogs->count(),
            'next_page' => $actionLogs->nextPageUrl(),
            // 'last_page' => $services->lastPage(),
            'filteredCount' => $actionLogs->total(),
        ];

        return response()->json([
            'Activities' => $actionLogs->items(),
            'Summary' => $pagination,
        ], 200);
    }

    public function getAllActionLogs($accountNum, Request $request)
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
        $query = ActionLogsModel::query();

        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where('data', 'like', '%' . $searchTerm . '%')
                ->orWhere('operation', 'like', '%' . $searchTerm . '%')
                ->orWhere('userID', 'like', '%' . $searchTerm . '%')
                ->orWhere('uID', 'like', '%' . $searchTerm . '%');
        }

        if ($request->filled('sort')) {
            $sortField = $request->input('sort');

            $sortOrder = $request->input('order', 'asc');

            $query->orderBy($sortField, $sortOrder);
        }

        $actionLogs = $query->paginate($page_size);

        $pagination = [
            'page' => $actionLogs->currentPage(),
            'page_size' => $page_size,
            'size' => $actionLogs->count(),
            'next_page' => $actionLogs->nextPageUrl(),
            // 'last_page' => $services->lastPage(),
            'filteredCount' => $actionLogs->total(),
        ];

        return response()->json([
            'Activities' => $actionLogs->items(),
            'Summary' => $pagination,
        ], 200);
    }
}
