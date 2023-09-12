<?php

namespace Tests\Feature;

use App\Models\Manuscript;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManuscriptControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_list_published_manuscripts(): void
    {

        $manuscript = Manuscript::factory()->create(['published' => 0]);

        $this->get('/')
            ->assertStatus(200)
            ->assertDontSee($manuscript->name);

        $manuscript->update(['published' => 1]);
        $this->get('/')
            ->assertSee($manuscript->name);
    }
}
