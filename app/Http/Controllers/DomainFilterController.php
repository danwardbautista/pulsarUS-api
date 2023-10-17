<?php

namespace App\Http\Controllers;

use App\Models\ActionLogsModel;
use App\Models\DomainFilterModel;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;

class DomainFilterController extends Controller
{
    public function getAllDomainFilter($accountNum, Request $request)
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
        $domainFilters = DomainFilterModel::paginate($page_size);

        $pagination = [
            'page' => $domainFilters->currentPage(),
            'page_size' => $page_size,
            'size' => $domainFilters->count(),
            'next_page' => $domainFilters->nextPageUrl(),
            'filteredCount' => $domainFilters->total(),
        ];

        $transformedDomainFilters = [];
        foreach ($domainFilters->items() as $domainFilter) {
            $domains = explode(', ', $domainFilter->domains);

            $filterLists = $domainFilter->filterLists ? json_decode($domainFilter->filterLists, true) : [];
            $transformedDomainFilters[] = [
                'uid' => $domainFilter->uid,
                'label' => $domainFilter->label,
                'customData' => json_decode($domainFilter->customData, true),
                'removed' => (bool) $domainFilter->removed,
                'creationDate' => $domainFilter->created_at,
                'lastModificationDate' => $domainFilter->updated_at,
                'domains' => $domains,
                'filterLists' => $filterLists,
                'version' => $domainFilter->id,
                'firewallVersion' => $domainFilter->firewallVersion,
            ];
        }

        return response()->json([
            'Profiles' => $transformedDomainFilters,
            'Summary' => $pagination,
        ], 200);
    }

    public function getDomainFilterByID($accountNum, $uid, Request $request)
    {

        $domainFilters = DomainFilterModel::where('uid', $uid)->first();

        if (!$domainFilters) {
            return response([
                'message' => "No domain filter with the specified uid found.",
            ], 404);
        }



        $domains = explode(', ', $domainFilters->domains);

        $filterLists = $domainFilters->filterLists ? json_decode($domainFilters->filterLists, true) : [];
        $transformedDomainFilters = [
            'uid' => $domainFilters->uid,
            'label' => $domainFilters->label,
            'customData' => json_decode($domainFilters->customData, true),
            'removed' => (bool) $domainFilters->removed,
            'creationDate' => $domainFilters->created_at,
            'lastModificationDate' => $domainFilters->updated_at,
            'domains' => $domains,
            'filterLists' => $filterLists,
            'version' => $domainFilters->id,
            'firewallVersion' => $domainFilters->firewallVersion,
        ];


        return response()->json([
            'Profile' => $transformedDomainFilters,
            // 'Summary' => $pagination,
        ], 200);
    }
    //
    public function createDomainFilter(Request $request, $accountNum)
    {
        $uuid = Uuid::uuid4()->toString();

        $validatedData = $request->validate([
            'Name' => 'required|string',
            'Description' => 'nullable|string',
            'Domains' => 'array',
        ]);

        $domainHostnames = collect($validatedData['Domains'])->pluck('Hostname')->join(', ');

        $data = [
            'uid' => $uuid,
            'label' => $validatedData['Name'],
            'customData' => json_encode(['description' => $validatedData['Description'], 'AccountNumber' => $accountNum]),
            'removed' => false,
            'domains' => $domainHostnames,
        ];

        $domainFilter = DomainFilterModel::create($data);

        $preferred_username = $request->attributes->get('preferred_username');

        $actionLogs = ActionLogsModel::create([
            'accountNum' => $accountNum,
            'data' => json_encode($data),
            'operation' => "Create Domain Filter",
            "userID" => $preferred_username,
            "uID" => $uuid

        ]);

        return response([
            'message' => "Domain filter created successfully",
            'Profile' => $domainFilter
        ], 200);
    }


    public function updateDomainFilter(Request $request, $accountNum, $uid)
    {
        $validatedData = $request->validate([
            'Name' => 'required|string',
            'Description' => 'nullable|string',
            'Domains' => 'array',
        ]);

        $domainHostnames = collect($validatedData['Domains'])->pluck('Hostname')->join(', ');

        $data = [
            'label' => $validatedData['Name'],
            'customData' => json_encode(['description' => $validatedData['Description'], 'AccountNumber' => $accountNum]),
            'domains' => $domainHostnames,
        ];

        $domainFilter = DomainFilterModel::where('uid', $uid)->first();

        if ($domainFilter) {
            $domainFilter->update($data);

            $preferred_username = $request->attributes->get('preferred_username');

            $actionLogs = ActionLogsModel::create([
                'accountNum' => $accountNum,
                'data' => json_encode($data),
                'operation' => "Update Domain Filter",
                "userID" => $preferred_username,
                "uID" => $domainFilter->uuid

            ]);

            return response([
                'message' => "Domain Filter updated successfully",
                'Profile' => $data
            ], 200);
        } else {
            return response([
                'error' => "Domain filter with UID $uid not found",
            ], 404);
        }
    }

    public function deleteDomainFilter(Request $request, $accountNum, $uid)
    {
        $domainFilter = DomainFilterModel::where('uid', $uid)->first();

        if ($domainFilter) {
            $domainFilter->delete();

            $preferred_username = $request->attributes->get('preferred_username');

            $actionLogs = ActionLogsModel::create([
                'accountNum' => $accountNum,
                'data' => json_encode($domainFilter),
                'operation' => "Delete Domain Filter",
                "userID" => $preferred_username,
                "uID" => $domainFilter->uuid

            ]);

            return response([
                'message' => "Domain Filter deleted successfully",
            ], 200);
        } else {
            return response([
                'error' => "Domain filter with UID $uid not found",
            ], 404);
        }
    }

}