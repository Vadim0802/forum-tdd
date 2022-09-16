<?php

namespace Tests\Unit;

use App\Models\Reply;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class ThreadTest extends TestCase
{
    use RefreshDatabase;

    private Thread $thread;

    protected function setUp(): void
    {
        parent::setUp();

        $this->thread = Thread::factory()->create([
            'user_id' => User::factory()->create()->id,
        ]);
    }

    public function test_a_thread_has_replies()
    {
        $this->assertInstanceOf(Collection::class, $this->thread->replies);
    }

    public function test_a_thread_has_creator()
    {
        $this->assertInstanceOf(User::class, $this->thread->creator);
    }

    public function test_a_thread_can_add_reply()
    {
        Reply::factory()->create([
            'thread_id' => $this->thread->id,
        ]);

        $this->assertCount(1, $this->thread->replies);
    }
}
