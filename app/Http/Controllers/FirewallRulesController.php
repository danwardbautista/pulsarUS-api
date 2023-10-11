<?php

namespace App\Http\Controllers;

use App\Models\FirewallRulesModel;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Arr;

class FirewallRulesController extends Controller
{
    public function getAllFirewallRules($accountNum, Request $request)
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
        $firewallRules = FirewallRulesModel::paginate($page_size);

        $pagination = [
            'page' => $firewallRules->currentPage(),
            'page_size' => $page_size,
            'size' => $firewallRules->count(),
            'next_page' => $firewallRules->nextPageUrl(),
            'filteredCount' => $firewallRules->total(),
        ];

        // Transform the rules
        $transformedFirewallRules = [];
        foreach ($firewallRules->items() as $firewallRule) {
            $transformedFirewallRules[] = [
                'uid' => $firewallRule->uid,
                'label' => $firewallRule->label,
                'customData' => json_decode($firewallRule->customData, true),
                'removed' => (bool) $firewallRule->removed,
                'creationDate' => $firewallRule->created_at,
                'lastModificationDate' => $firewallRule->updated_at,
                'inbound' => json_decode($firewallRule->inbound, true),
                'outbound' => json_decode($firewallRule->outbound, true),
            ];
        }

        return response()->json([
            'Profile' => $transformedFirewallRules,
            'Summary' => $pagination,
        ], 200);
    }

    public function getFirewallRuleByID($accountNum, Request $request, $uid)
    {
        // Retrieve the first firewall rule that matches the UID
        $firewallRule = FirewallRulesModel::where('uid', $uid)->first();

        if (!$firewallRule) {
            return response([
                'message' => "No firewall rule with the specified uid found.",
            ], 404);
        }

        // Transform the firewall rule
        $transformedFirewallRule = [
            'uid' => $firewallRule->uid,
            'label' => $firewallRule->label,
            'customData' => json_decode($firewallRule->customData, true),
            'removed' => (bool) $firewallRule->removed,
            'creationDate' => $firewallRule->created_at,
            'lastModificationDate' => $firewallRule->updated_at,
            'inbound' => json_decode($firewallRule->inbound, true),
            'outbound' => json_decode($firewallRule->outbound, true),
        ];

        return response()->json([
            'Profile' => $transformedFirewallRule,
            // 'Summary' => $pagination,
        ], 200);
    }

    public function createFirewallRule(Request $request, $accountNum)
    {
        $uuid = Uuid::uuid4()->toString();

        $validatedData = $request->validate([
            'Name' => 'required|string',
            'Description' => '|string',
            'OutboundDefault' => 'required|string',
            'OutboundExceptions' => 'array',
            'InboundDefault' => 'required|string',
            'InboundExceptions' => 'array',
        ]);

        $transformOutbound = function ($exception) {
            return [
                'label' => $exception['OutboundDescription'],
                'protocol' => (int) $exception['OutboundProtocol'],
                'destPortRange' => [
                    'from' => (int) $exception['OutboundPortRange'],
                    'toInclusive' => (int) $exception['OutboundPortRange'],
                ],
                'remoteAddresses' => [$exception['OutboundIPPrefix']],
                'displayPort' => (int) $exception['OutboundPortRange'],
                'parsedProtocol' => $this->GetPreset(intval($exception['OutboundProtocol']), $exception['OutboundPortRange']),
            ];
        };

        $transformInbound = function ($exception) {
            return [
                'label' => $exception['InboundDescription'],
                'protocol' => (int) $exception['InboundProtocol'],
                'destPortRange' => [
                    'from' => (int) $exception['InboundPortRange'],
                    'toInclusive' => (int) $exception['InboundPortRange'],
                ],
                'remoteAddresses' => [$exception['InboundIPPrefix']],
                'displayPort' => (int) $exception['InboundPortRange'],
                'parsedProtocol' => 'tcp',
            ];
        };

        $data = [
            'uid' => $uuid,
            'label' => $validatedData['Name'],
            'customData' => json_encode(['description' => $validatedData['Description'], 'AccountNumber' => $accountNum]),
            'removed' => false,
            'outbound' => json_encode([
                'defaultAction' => $validatedData['OutboundDefault'],
                'exceptions' => array_map($transformOutbound, $validatedData['OutboundExceptions']),
            ]),
            'inbound' => json_encode([
                'defaultAction' => $validatedData['InboundDefault'],
                'exceptions' => array_map($transformInbound, $validatedData['InboundExceptions']),
            ]),
        ];

        FirewallRulesModel::create($data);

        return response([
            'message' => "Firewall rule created successfully",
            'Profile' => $data
        ], 200);
    }

    public function updateFirewallRule(Request $request, $accountNum, $uid)
    {
        $validatedData = $request->validate([
            'Name' => 'required|string',
            'Description' => 'string',
            'OutboundDefault' => 'required|string',
            'OutboundExceptions' => 'array',
            'InboundDefault' => 'required|string',
            'InboundExceptions' => 'array',
        ]);

        $transformOutbound = function ($exception) {
            return [
                'label' => $exception['OutboundDescription'],
                'protocol' => (int) $exception['OutboundProtocol'],
                'destPortRange' => [
                    'from' => (int) $exception['OutboundPortRange'],
                    'toInclusive' => (int) $exception['OutboundPortRange'],
                ],
                'remoteAddresses' => [$exception['OutboundIPPrefix']],
                'displayPort' => (int) $exception['OutboundPortRange'],
                'parsedProtocol' => 'tcp',
            ];
        };

        $transformInbound = function ($exception) {
            return [
                'label' => $exception['InboundDescription'],
                'protocol' => (int) $exception['InboundProtocol'],
                'destPortRange' => [
                    'from' => (int) $exception['InboundPortRange'],
                    'toInclusive' => (int) $exception['InboundPortRange'],
                ],
                'remoteAddresses' => [$exception['InboundIPPrefix']],
                'displayPort' => (int) $exception['InboundPortRange'],
                'parsedProtocol' => 'tcp',
            ];
        };

        $data = [
            'uid' => $uid,
            'label' => $validatedData['Name'],
            'customData' => json_encode(['description' => $validatedData['Description'], 'AccountNumber' => $accountNum]),
            'outbound' => json_encode([
                'defaultAction' => $validatedData['OutboundDefault'],
                'exceptions' => array_map($transformOutbound, $validatedData['OutboundExceptions']),
            ]),
            'inbound' => json_encode([
                'defaultAction' => $validatedData['InboundDefault'],
                'exceptions' => array_map($transformInbound, $validatedData['InboundExceptions']),
            ]),
        ];

        $firewallRule = FirewallRulesModel::where('uid', $uid)->first();

        if ($firewallRule) {
            $firewallRule->update($data);
            return response([
                'message' => "Firewall rule updated successfully",
                'Profile' => $data
            ], 200);
        } else {
            return response([
                'message' => "Firewall rule with UID $uid not found",
            ], 404);
        }
    }

    public function deleteFirewallRule(Request $request, $accountNum, $uid)
    {
        $firewallRule = FirewallRulesModel::where('uid', $uid)->first();

        if ($firewallRule) {
            $firewallRule->delete();
            return response([
                'message' => "Firewall rule with UID $uid deleted successfully",
            ], 200);
        } else {
            return response([
                'message' => "Firewall rule with UID $uid not found",
            ], 404);
        }
    }

    public function GetPreset($protocol, $port) {

        // HTTP
        if ($protocol == '6' && $port == '80') return 'http';
        if ($protocol == '6' && $port == '443') return 'https';

        // HTTP (UDP)
        if ($protocol == '17' && $port == '80') return 'http-udp';
        if ($protocol == '17' && $port == '443') return 'https-udp';
    
        // FTP
        if ($protocol == '6' && $port == '21') return 'ftp';
        // SSH
        if ($protocol == '6' && $port == '22') return 'ssh';
        // Telnet
        if ($protocol == '6' && $port == '23') return 'telnet';
        // POP3
        if ($protocol == '6' && $port == '110') return 'pop3';
        // SMTP
        if ($protocol == '6' && $port == '25') return 'smtp';
        // ntp
        if ($protocol == '17' && $port == '123') return 'ntp';
        // snmp
        if ($protocol == '17' && $port == '161') return 'snmp';
        // sip
        if ($protocol == '6' && $port == '5060') return 'sip';

        return 'unknown';
    }

}