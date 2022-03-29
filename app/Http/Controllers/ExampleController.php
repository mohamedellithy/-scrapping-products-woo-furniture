<?php

namespace App\Http\Controllers;
use App\Post;
class ExampleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function testDb(){
        $data = Post::where('post_type','product')->get();
        return $data;
    }
}
