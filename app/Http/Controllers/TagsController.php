<?php

namespace App\Http\Controllers;

use App\Models\TagsModel;
use Illuminate\Http\Request;

class TagsController extends Controller
{
    //
    public function getAllTags($accountNum, Request $request)
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
        $tags = TagsModel::where('Account', $accountNum)->paginate($page_size);

        $pagination = [
            'page' => $tags->currentPage(),
            'page_size' => $page_size,
            'size' => $tags->count(),
            // 'next_page' => $services->nextPageUrl(),
            // 'last_page' => $services->lastPage(),
            'filteredCount' => $tags->total(),
        ];

        return response()->json([
            'Tags' => $tags->items(),
            'Summary' => $pagination,
        ], 200);
    }

    public function getTagByID($accountNum, $templateID, Request $request)
    {
        // Filter templates by Account and templateID
        $tag = TagsModel::where('Account', $accountNum)
            ->where('id', $templateID)
            ->first();

        if ($tag) {
            return response()->json([
                'Tag' => $tag,
            ], 200);
        } else {
            return response()->json([
                'message' => 'Tag not found',
            ], 404);
        }
    }

    
}
