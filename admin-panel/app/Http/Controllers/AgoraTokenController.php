<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DateTime;
use DateTimeZone;
use App\Services\RtcTokenBuilder;

// Import the class correctly
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use App\Models\UrlTable;
use App\Models\AssessmentRequest;
use App\Models\User;

class AgoraTokenController extends Controller
{
    public function generateToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'channelName' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $appId = env('AGORA_APP_ID');
        $appCertificate = env('AGORA_APP_CERTIFICATE');
        $channelName = $request->channelName;
        $uid = 0; // If null, set to 0 (anonymous user)
        $role = 1;
        $expireTime = 3600; // Default expiration time: 1 hour

        $currentTimestamp = (new DateTime("now", new DateTimeZone('UTC')))->getTimestamp();
        $privilegeExpiredTs = $currentTimestamp + $expireTime;

        // Use the fully qualified class name now
        $token = RtcTokenBuilder::buildTokenWithUid($appId, $appCertificate, $channelName, $uid, $role, $privilegeExpiredTs);

        return response()->json([
            'channelName' => $channelName,
            'uid' => $uid,
            'role' => $role,
            'token' => $token
        ]);
    }

    public function createUrl(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'assessment_request_id' => 'required|integer',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
        
        // Generate random channel name
        $channelName = 'agora' . rand(100000, 999999);
        
        
        // Generate token directly by calling the method
        $tokenData = $this->generateToken(new Request(['channelName' => $channelName]))->getData();
        
        // Generate random URL
        $randomUrl = Str::random(32);

        // Find existing entry or create new one
        $urlTable = UrlTable::updateOrCreate(
            ['assessment_request_id' => $request->assessment_request_id],
            [
                'url' => $randomUrl,
                'channel' => $tokenData->channelName,
                'token' => $tokenData->token
            ]
        );

        return response()->json($urlTable, 201);
    }

    public function getUrl(Request $request)
    {
        $username = $request->username;
        //UrlTable refers to AssessmetRequest via id, AssessmentRequest refers to User viaid, so return the urltable id which has the user with the username
        $urlTable = UrlTable::whereHas('assessmentRequest.user', function ($query) use ($username) {
            $query->where('username', $username)->where('is_done', false);
        })->first();

        //return the url
        if ($urlTable) {
            return response()->json([
                'url' => $urlTable->url,
                'channel' => $urlTable->channel,
                'token' => $urlTable->token,
            ]);
        } else {
            return response()->json(['error' => 'URL not found'], 404);
        }
    }

    public function getResult(Request $request)
    {
        try {
            $username = $request->username;
            $user = User::where('username', $username)->first();
            $assessmentRequest = AssessmentRequest::where('user_id', $user->id)->orderBy('updated_at', 'desc')->first();
            $rating = $assessmentRequest->rating;

            return response()->json([
                "result" => $rating,
                "date" => optional($assessmentRequest->timeSlot)->date,
                "time" => optional($assessmentRequest->timeSlot)->start_time,
                "job" => optional(optional($assessmentRequest->user)->job)->jobType,
                "experience" => optional($assessmentRequest->user)->experience_level
            ], 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'URL not found'], 404);
        }

    }

}
