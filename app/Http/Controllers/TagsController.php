<?php

namespace App\Http\Controllers;

use App\Models\TagsModel;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;

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

    public function getTagByID($accountNum, $tagID, Request $request)
    {
        $tag = TagsModel::where('Account', $accountNum)
            ->where('id', $tagID)
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

    public function createNewTag($accountNum, Request $request)
    {
        $request->validate([
            'Name' => 'required',
            'Hex' => 'required',
        ]);

        $uuid = Uuid::uuid4()->toString();

        $tags = TagsModel::create([
            'uuid' => $uuid,
            'Account' => $accountNum,
            'Name' => $request->Name,
            'Hex' => $request->Hex
        ]);

        return response([
            'message' => "Tag created successfully",
            'Tags' => $tags
        ], 200);
    }

    public function updateTagByID($accountNum, $tagID, Request $request)
    {
        $tag = TagsModel::where('Account', $accountNum)
            ->where('id', $tagID)
            ->first();

        if ($tag) {
            $request->validate([
                'Name' => 'required',
                'Hex' => 'required',
            ]);

            $tag->update([
                'Name' => $request->input('Name'),
                'Hex' => $request->input('Hex')
            ]);

            return response()->json([
                'Tag' => $tag,
            ], 200);
            
        } else {
            return response()->json([
                'message' => 'Tag not found',
            ], 404);
        }
    }

    public function deleteTagByID($accountNum, $tagID, Request $request)
    {
        $tag = TagsModel::where('Account', $accountNum)
            ->where('id', $tagID)
            ->first();

        if ($tag) {
            $tag -> delete();

            return response()->json([
                'message' => 'Template deleted successfully',
            ], 200);
            
        } else {
            return response()->json([
                'message' => 'Tag not found',
            ], 404);
        }
    }

}
