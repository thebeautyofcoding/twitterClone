<?php

namespace App\Http\Controllers;

use App\Models\Post;

use App\Models\User;

use Illuminate\Http\Request;

use App\Http\Resources\UserResource;
use ProtoneMedia\LaravelCrossEloquentSearch\Search;
class SearchController extends Controller
{
    public function searchUsers(Request $request)
    {
        $keyword = $request->value;
        $userId = auth()->id();

        $results = Search::new()

            ->add(User::where('id', '!=', $userId), [
                'username',
                'firstname',
                'lastname',
            ])

            ->beginWithWildcard()

            ->get($keyword);

        return response(['users' => UserResource::collection($results)]);
    }

    public function searchPosts(Request $request)
    {
        $keyword = $request->value;

        $results = Search::new()

            ->add(Post::class, ['content'])
            ->beginWithWildcard()
            ->get($keyword);

        return response(['posts' => $results]);
    }
}
