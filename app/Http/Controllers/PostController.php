<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
  public function addNewPost(Request $request)
  {

    $fields = Validator::make($request->all(), [
      'title' => 'required|string|max:255',
      'content' => 'required|string',

    ]);

    if ($fields->fails()) {
      return response()->json(['errors' => $fields->errors()], 403);
    }

    try {

      $post = new Post();
      $post->title = $request->title;
      $post->content = $request->content;
      $post->user_id = auth()->user()->id;
      $post->save();

      return response()->json([
        'message' => 'Post created successfully',
        'post' => $post
      ], 201); // 201 created
    } catch (\Exception $e) {
      return response()->json(['error' => $e->getMessage()], 403);
    }
  }

  //edit post

  public function editPost(Request $request)
  {

    $fields = Validator::make($request->all(), [
      'title' => 'required|string|max:255',
      'content' => 'required|string',
      'post_id' => 'required|integer'

    ]);

    if ($fields->fails()) {
      return response()->json(['errors' => $fields->errors()], 403);
    }

    try {

      $post_data = Post::find($request->post_id);

      if (!$post_data) {
        return response()->json(['error' => 'Post not found'], 404);
      }

      $updated_post = $post_data->update([
        'title' => $request->title,
        'content' => $request->content
      ]);


      return response()->json([
        'message' => 'Post updated successfully',
        'updatePost' => $updated_post
      ], 200);
    } catch (\Exception $e) {
      return response()->json(['error' => $e->getMessage()], 403);
    }
  }

  //Another approach to edit post 

  public function editPost2(Request $request, $post_id)
  {

    $fields = Validator::make($request->all(), [
      'title' => 'required|string|max:255',
      'content' => 'required|string',
    ]);

    if ($fields->fails()) {
      return response()->json(['errors' => $fields->errors()], 403);
    }

    try {

      $post_data = Post::find($post_id);

      if (!$post_data) {
        return response()->json(['error' => 'Post not found'], 404);
      }

      $updated_post = $post_data->update([
        'title' => $request->title,
        'content' => $request->content
      ]);


      return response()->json([
        'message' => 'Post updated successfully',
        'updatePost' => $updated_post
      ], 200);
    } catch (\Exception $e) {
      return response()->json(['error' => $e->getMessage()], 403);
    }
  }


  // Get all posts
  public function getAllPosts()
  {

    try {
      $posts = Post::all();
      return response()->json([
        'posts' => $posts
      ], 200);
    } catch (\Exception $e) {
      return response()->json(['error' => $e->getMessage()], 403);
    }
  }


  // fetch single post by id
  public function getPost($post_id)
  {
    try {
      $post = Post::find($post_id);
      if (!$post) {
        return response()->json(['error' => 'Post not found'], 404);
      }
      return response()->json([
        'post' => $post
      ], 200);
    } catch (\Exception $e) {
      return response()->json(['error' => $e->getMessage()], 403);
    }
  }

  // delete post 
  public function deletePost($post_id)
  {
    try {
      $post = Post::find($post_id);
      if (!$post) {
        return response()->json(['error' => 'Post not found'], 404);
      }
      $post->delete();
      return response()->json([
        'message' => 'Post deleted successfully'
      ], 200);
    } catch (\Exception $e) {
      return response()->json(['error' => $e->getMessage()], 403);
    }
  }
}
