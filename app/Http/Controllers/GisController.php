<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GisService;

class GisController extends Controller
{
    protected $gisService;

    public function __construct(GisService $gisService)
    {
        $this->gisService = $gisService;
    }

    // Login API: Pass username and password, get sessionKey
    public function login(Request $request)
    {
        $this->validate($request, [
            'user' => 'required|string',
            'pswd' => 'required|string'
        ]);
        $response = $this->gisService->login($request->user, $request->pswd);
        return response()->json($response);
    }

    // Logout API: Pass sessionKey to logout
    public function logout(Request $request)
    {
        $this->validate($request, [
            'sessionKey' => 'required|string'
        ]);
        $response = $this->gisService->logout($request->sessionKey);
        return response()->json($response);
    }

    // Get OLT Status API
    public function getOltStatus(Request $request)
    {
        $this->validate($request, [
            'sessionKey' => 'required|string'
        ]);
        $oltcode = $request->input('oltcode', null);
        $response = $this->gisService->getOltStatus($request->sessionKey, $oltcode);
        return response()->json($response);
    }

    // Get ONT Status API
    public function getOntStatus(Request $request)
    {
        $this->validate($request, [
            'sessionKey' => 'required|string'
        ]);
        $oltcode = $request->input('oltcode', null);
        $ontcode = $request->input('ontcode', null);
        $response = $this->gisService->getOntStatus($request->sessionKey, $oltcode, $ontcode);
        return response()->json($response);
    }
}
