<?php

namespace App\Http\Controllers;

use App\Models\ActionLogsModel;
use App\Models\TemplatesModel;
use Illuminate\Http\Request;

class TemplatesController extends Controller
{
    //
    public function getAllTemplates($accountNum, Request $request)
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

        $query = TemplatesModel::where('Account', $accountNum);

        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where('Name', 'like', '%' . $searchTerm . '%');
        }

        if ($request->filled('sort')) {
            $sortField = $request->input('sort');

            $sortOrder = $request->input('order', 'asc');

            $query->orderBy($sortField, $sortOrder);
        }

        $templates = $query->paginate($page_size);

        $pagination = [
            'page' => $templates->currentPage(),
            'page_size' => $page_size,
            'size' => $templates->count(),
            'next_page' => $templates->nextPageUrl(),
            'filteredCount' => $templates->total(),
        ];

        return response()->json([
            'Templates' => $templates->items(),
            'Summary' => $pagination,
        ], 200);
    }




    public function getTemplateByID($accountNum, $templateID, Request $request)
    {
        // Filter templates by Account and templateID
        $template = TemplatesModel::where('Account', $accountNum)
            ->where('id', $templateID)
            ->first();

        if ($template) {
            return response()->json([
                'Template' => $template,
            ], 200);
        } else {
            return response()->json([
                'message' => 'Template not found',
            ], 404);
        }
    }

    public function createNewTemplate($accountNum, Request $request)
    {
        $request->validate([
            'Name' => 'required',
        ]);

        $templates = TemplatesModel::create([
            'Account' => $accountNum,
            'Name' => $request->Name

        ]);

        $preferred_username = $request->attributes->get('preferred_username');

        $actionLogs = ActionLogsModel::create([
            'accountNum' => $accountNum,
            'data' => json_encode($templates),
            'operation' => "Create Template",
            "userID" => $preferred_username

        ]);

        return response([
            'message' => "Template created successfully",
            'Templates' => $templates,
        ], 200);
    }

    public function updateTemplateByID($accountNum, $templateID, Request $request)
    {
        $template = TemplatesModel::where('Account', $accountNum)
            ->where('id', $templateID)
            ->first();

        if ($template) {
            $request->validate([
                'Name' => 'required',
            ]);

            $template->update([
                'Name' => $request->input('Name'),
            ]);

            $preferred_username = $request->attributes->get('preferred_username');

            $actionLogs = ActionLogsModel::create([
                'accountNum' => $accountNum,
                'data' => json_encode($template),
                'operation' => "Update Template",
                "userID" => $preferred_username

            ]);

            return response()->json([
                'Template' => $template,
            ], 200);
        } else {
            return response()->json([
                'message' => 'Template not found',
            ], 404);
        }
    }

    public function deleteTemplateByID($accountNum, $templateID, Request $request)
    {
        $template = TemplatesModel::where('Account', $accountNum)
            ->where('id', $templateID)
            ->first();

        if ($template) {
            $template->delete();

            $preferred_username = $request->attributes->get('preferred_username');

            $actionLogs = ActionLogsModel::create([
                'accountNum' => $accountNum,
                'data' => json_encode($template),
                'operation' => "Delete Template",
                "userID" => $preferred_username

            ]);

            return response()->json([
                'message' => 'Template deleted successfully',
            ], 200);
        } else {
            return response()->json([
                'message' => 'Template not found',
            ], 404);
        }
    }


}