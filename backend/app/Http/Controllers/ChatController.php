<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    // ---- Probe with CHAT COMPLETIONS (no max tokens first) ----
    public function test()
    {
        $resp = Http::withHeaders([
            'Authorization' => 'Bearer '.env('OPENAI_API_KEY'),
            'Content-Type'  => 'application/json',
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model'    => env('OPENAI_MODEL', 'gpt-4o-mini'),
            'messages' => [
                ['role' => 'system', 'content' => 'Answer briefly.'],
                ['role' => 'user',   'content' => 'Who won the 2002 FIFA World Cup?']
            ],
//            'temperature' => 0.2
            // NOTE: no max_* field on purpose to avoid param mismatch
        ]);

        return response($resp->body(), $resp->status())
            ->header('Content-Type', 'application/json');
    }

    // ---- Main chat endpoint (CHAT COMPLETIONS) ----
    public function chat(Request $request)
    {

//        echo "<pre>";print_r($request);exit;
        $data = $request->validate([
            'messages' => 'required|array',
        ]);

        $system = [
            'role' => 'system',
            'content' => "You are WorldCupBot. Answer ONLY FIFA World Cup questions. Be concise and factual."
        ];

        $messages = array_merge([$system], $data['messages']);

        $resp = Http::withHeaders([
            'Authorization' => 'Bearer '.env('OPENAI_API_KEY'),
            'Content-Type'  => 'application/json',
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model'    => env('OPENAI_MODEL', 'gpt-4o-mini'),
            'messages' => $messages,
            // no temperature/max tokens until we confirm working
        ]);

        if (!$resp->ok()) {
            // Log the full error
            \Log::error('OpenAI chat error', [
                'status' => $resp->status(),
                'body'   => $resp->body(),
            ]);
            // Send raw error back to frontend for debugging
            return response()->json([
                'error' => true,
                'message' => $resp->json()
            ], $resp->status());
        }

        $json = $resp->json();
        $assistant = $json['choices'][0]['message'] ?? ['role'=>'assistant','content'=>'No reply'];
        return response()->json(['message' => $assistant]);
    }
}
