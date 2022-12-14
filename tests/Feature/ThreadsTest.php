<?php

namespace Tests\Feature;

use App\Models\Channel;
use App\Models\Reply;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ThreadsTest extends TestCase
{
    use RefreshDatabase;

    private Thread $thread;

    protected function setUp(): void
    {
        parent::setUp();

        $this->thread = Thread::factory()->create([
            'user_id' => User::factory()->create()->id,
            'channel_id' => Channel::factory()->create()->id,
        ]);
    }

    public function test_user_can_browse_threads()
    {
        $this->get(route('threads.index'))
            ->assertStatus(200)
            ->assertSeeText($this->thread->title);

        $this->get(route('threads.show', ['channel' => $this->thread->channel, 'thread' => $this->thread]))
            ->assertOk()
            ->assertSeeText($this->thread->title);
    }

    public function test_user_can_browse_single_thread()
    {
        $this->get(route('threads.show', ['channel' => $this->thread->channel, 'thread' => $this->thread]))
            ->assertOk()
            ->assertSee($this->thread->title);
    }

    public function test_user_can_read_replies_that_are_associated_with_a_thread()
    {
        $reply = Reply::factory()->create([
            'thread_id' => $this->thread->id,
            'user_id' => $this->thread->creator->id,
        ]);

        $this->get(route('threads.show', ['channel' => $this->thread->channel, 'thread' => $this->thread]))
            ->assertOk()
            ->assertSee($reply->body);
    }

    public function test_user_can_filter_threads_by_channel()
    {
        $channel = Channel::factory()->create();
        $threadInChannel = Thread::factory()->create([
            'channel_id' => $channel->id,
        ]);

        $this->get(route('threads.channel.index', $channel))
            ->assertOk()
            ->assertSee($threadInChannel->title)
            ->assertDontSee($this->thread->title);
    }

    public function test_user_can_filter_threads_by_username()
    {
        $otherUser = User::factory()->create();

        $otherThread = Thread::factory()->create([
            'user_id' => $otherUser->id,
            'channel_id' => Channel::factory()->create()->id,
        ]);

        $this->get(route('threads.index', ['username' => $otherUser->name]))
            ->assertOk()
            ->assertSee($otherThread->title)
            ->assertDontSee($this->thread->title);
    }

    public function test_user_can_filter_threads_by_popularity()
    {
        $createThreadWithReplies = static function (int $times) {
            $user = User::factory()->create();
            $thread = Thread::factory()->create([
                'user_id' => $user->id,
                'channel_id' => Channel::factory()->create()->id,
                'replies_count' => $times,
            ]);

            Reply::factory($times)->create([
                'user_id' => $user->id,
                'thread_id' => $thread->id,
            ]);

            return $thread;
        };

        for ($i = 1; $i <= 3; $i++) {
            $createThreadWithReplies($i);
        }

        $response = $this->getJson(route('threads.index', ['popularity' => 'desc']))->json();

        $this->assertEquals([3, 2, 1, 0], array_column($response, 'replies_count'));
    }

    public function test_user_can_filter_threads_by_unanswered()
    {
        $threadWithReplies = Thread::factory()->create(['replies_count' => 1]);
        Reply::factory()->create(['thread_id' => $threadWithReplies->id]);

        $this->get(route('threads.index', ['unanswered' => 1]))
            ->assertOk()
            ->assertSee($this->thread->title)
            ->assertSee($this->thread->body)
            ->assertDontSee($threadWithReplies->title)
            ->assertDontSee($threadWithReplies->body);
    }

    public function test_user_can_subscribe_a_thread()
    {
        /** @var Authenticatable $user */
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('threads.subscriptions.store', $this->thread));

        $this->assertEquals(1, $this->thread->fresh()->subscriptions->count());
    }

    public function test_user_can_unsubscribe_from_thread()
    {
        /** @var Authenticatable $user */
        $user = User::factory()->create();

        $this->actingAs($user)
            ->withoutExceptionHandling()
            ->post(route('threads.subscriptions.store', $this->thread));

        $this->actingAs($user)
            ->withoutExceptionHandling()
            ->delete(route('threads.subscriptions.destroy', $this->thread));

        $this->assertEquals(0, $this->thread->fresh()->subscriptions->count());
    }
}
