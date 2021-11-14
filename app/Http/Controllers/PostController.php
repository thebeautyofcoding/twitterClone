<?php

namespace App\Http\Controllers;

use App\Events\LikeEvent;
use App\Events\PostPostedEvent;
use App\Events\RetweetEvent;
use App\Events\UnlikeEvent;
use App\Http\Resources\PostLikeResource;
use App\Models\Post;
use App\Models\User;
use App\Models\PostLike;
use App\Models\Retweet;
use App\Models\PostReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Http\Resources\PostResource;
use App\Http\Resources\ReplyResource;
use App\Http\Resources\UserResource;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = PostResource::collection(Post::orderBy('id', 'DESC')->get());
        return response()->json(['posts' => $posts]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, User $user)
    {
        $post = Post::create([
            'content' => $request->content,
            'user_id_posted_by' => $user->id,
        ]);

        $post->save();
        broadcast(new PostPostedEvent(new PostResource($post)))->toOthers();
        $post->user_id_posted_by = $post->user;
        return response()->json(['post' => new PostResource($post)], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($postId)
    {
        $post = new PostResource(Post::find($postId));
        return response()->json(['post' => $post], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    public function likeOrUnlike(Request $request)
    {
        $postId = $request->postId;

        $user = auth()->user();

        $post = Post::find($postId);

        $postLike = $user->post_like
            ->where('user_id', $user->id)
            ->where('post_id', $postId)
            ->first();

        if (empty($postLike)) {
            $postLike = PostLike::create([
                'post_id' => $postId,
                'user_id' => $user->id,
            ]);

            $postLike->save();
            $post->post_like = [$postLike];
            $post->post_likes_post = PostLike::where('post_id', $postId)->get();
            $postLikes = $postLike->where('post_id', $postId)->count();
            $post->post_like_count = $postLikes;
            $post->replies = $post->postReplies;
            $post->user_id_posted_by = $post->user;
            $post->retweet_count = $post->retweets->count();
            $post->retweet = $post->retweets;
            $post->retweetedByAuthUser = $post->retweets
                ->where('user_id', $user->id)
                ->first();
            $post->post_like_by = PostLike::where('post_id', $postId)
                ->where('user_id', auth()->id())
                ->get()
                ->first()
                ->user()
                ->first();
            broadcast(new LikeEvent(new PostResource($post)))->toOthers();
            return response()->json(['post' => $post, 'liked' => true], 200);
        } else {
            $postLike->delete();

            $post->post_likes_post = PostLike::where('post_id', $postId)->get();
            $post->post_like_count = PostLike::where('post_id', $postId)
                ->get()
                ->count();
            $post->replies = $post->postReplies;
            $post->user_id_posted_by = $post->user;
            $post->retweet_count = $post->retweets->count();
            $post->retweet = $post->retweets;
            broadcast(new LikeEvent(new PostResource($post)))->toOthers();
            return response()->json(
                ['post' => new PostResource($post), 'liked' => false],
                200
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        $isDeleted = $post->delete();
        return response()->json(['deleted' => $isDeleted], 200);
    }

    public function retweet(Request $request)
    {
        $postId = $request->postId;
        $user = auth()->user();
        $post = Post::find($postId);

        $didAuthUserRetweetIt = Retweet::where('user_id', $user->id)
            ->where('post_id', $postId)
            ->first();

        if ($didAuthUserRetweetIt) {
            $didAuthUserRetweetIt->delete();
            $post->retweet_count = $post->retweets
                ->where('post_id', $postId)
                ->count();

            broadcast(new RetweetEvent(new PostResource($post)))->toOthers();
            return response()->json(['post' => new PostResource($post)], 200);
        } else {
            $post->retweetedByAuthUser = null;
            $postContent = $post->content;
            $retweet = Retweet::create([
                'data' => $postContent,
                'post_id' => $postId,
                'user_id' => $user->id,
            ]);
            $retweet->save();
            $post->retweetedByAuthUser = Retweet::where(
                'user_id',
                $user->id
            )->first();
            $post->retweetedByAuthUser->user_id = $post->user;
            $post->retweet_count = $post->retweets
                ->where('post_id', $postId)
                ->count();
            broadcast(new RetweetEvent(new PostResource($post)))->toOthers();
            return response()->json(['post' => new PostResource($post)], 201);
        }
    }

    public function replyToPost(Request $request)
    {
        $user = auth()->user();

        $newReply = PostReply::create([
            'content' => $request->input('reply.content'),
            'user_id' => $user->id,
            'post_id' => $request->input('reply.belongsToPost'),
        ]);
        $newReply->save();

        return response()->json(
            [
                'post' => new PostResource(
                    Post::find($request->input('reply.belongsToPost'))
                ),
            ],
            201
        );
    }

    public function allRepliesOfPost(Request $request, $postId)
    {
        $repliesOfPost = PostReply::where('post_id', $postId)->get();
        $replies = [];
        foreach ($repliesOfPost as $reply) {
            $replies[] = $reply->user;
        }
        $post = Post::find($request->postId);
        $post->replies = $repliesOfPost;

        return response()->json(
            [
                'postWithReplies' => new PostResource($post),
            ],
            200
        );
    }

    public function getReplies(Request $request, $username)
    {
        $user = User::where('username', $username)->first();
        $posts = [];
        foreach ($user->postReplies as $rep) {
            $posts[] = $rep->post;
        }

        return response()->json([
            'replies' => PostResource::collection($posts),
        ]);
    }
}
