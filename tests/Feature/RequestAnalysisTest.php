<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RequestAnalysisTest extends TestCase
{
    use RefreshDatabase;

    public function test_request_analysis_page_loads()
    {
        $response = $this->get('/demander-analyse');
        $response->assertStatus(200);
    }
}
