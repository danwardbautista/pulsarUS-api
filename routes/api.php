<?php

use App\Http\Controllers\ActionLogsController;
use App\Http\Controllers\DomainFilterController;
use App\Http\Controllers\FirewallRulesController;
use App\Http\Controllers\ServicesController;
use App\Http\Controllers\TagsController;
use App\Http\Controllers\TemplatesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => 'token-validation'], function () {
    Route::get('/example', [ServicesController::class, 'example']);
    
    Route::get('/{accountNum}/services', [ServicesController::class, 'getAllServices']);

    //TEMPLATES ENDPOINT
    Route::get('/{accountNum}/templates', [TemplatesController::class, 'getAllTemplates']);
    Route::get('/{accountNum}/templates/{templateID}', [TemplatesController::class, 'getTemplateByID']);
    Route::post('/{accountNum}/templates', [TemplatesController::class, 'createNewTemplate']);
    Route::put('/{accountNum}/templates/{templateID}', [TemplatesController::class, 'updateTemplateByID']);
    Route::delete('/{accountNum}/templates/{templateID}', [TemplatesController::class, 'deleteTemplateByID']);

    //TAGS ENDPOINT
    Route::get('/{accountNum}/tags', [TagsController::class, 'getAllTags']);
    Route::get('/{accountNum}/tags/{tagID}', [TagsController::class, 'getTagByID']);
    Route::post('/{accountNum}/tags', [TagsController::class, 'createNewTag']);
    Route::put('/{accountNum}/tags/{tagID}', [TagsController::class, 'updateTagByID']);
    Route::delete('/{accountNum}/tags/{tagID}', [TagsController::class, 'deleteTagByID']);

    //FIREWALL RULES ENDPOINT
    Route::get('/{accountNum}/Firewall/Profiles', [FirewallRulesController::class, 'getAllFirewallRules']);
    Route::get('/{accountNum}/Firewall/Profiles/{uid}', [FirewallRulesController::class, 'getFirewallRuleByID']);
    Route::post('/{accountNum}/Firewall/Profiles', [FirewallRulesController::class, 'createFirewallRule']);
    Route::put('/{accountNum}/Firewall/Profiles/{uid}', [FirewallRulesController::class, 'updateFirewallRule']);
    Route::delete('/{accountNum}/Firewall/Profiles/{uid}', [FirewallRulesController::class, 'deleteFirewallRule']);

    //DOMAIN FILTER ENDPOINT
    Route::get('/{accountNum}/DomainFilter/Profiles', [DomainFilterController::class, 'getAllDomainFilter']);
    Route::get('/{accountNum}/DomainFilter/Profiles/{uid}', [DomainFilterController::class, 'getDomainFilterByID']);
    Route::post('/{accountNum}/DomainFilter/Profiles', [DomainFilterController::class, 'createDomainFilter']);
    Route::put('/{accountNum}/DomainFilter/Profiles/{uid}', [DomainFilterController::class, 'updateDomainFilter']);
    Route::delete('/{accountNum}/DomainFilter/Profiles/{uid}', [DomainFilterController::class, 'deleteDomainFilter']);

    //ACTION LOGS ENDPOINT
    Route::get('/{accountNum}/Activities', [ActionLogsController::class, 'getAllActionLogs']);
    Route::get('/{accountNum}/Activities/Profile', [ActionLogsController::class, 'getAccountNumActionLogs']);
});

