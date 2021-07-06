<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth'])->except(['index', 'show']); //except를 사용하면 'index'페이지는 로그인 하지않아도 접속가능
    }

    public function show(Request $request, $id) //Request를 먼저 적어야 한다
    {
        // dd($request->page);
        $page = $request->page;
        $post = Post::find($id);

        return view('posts.show', compact('post', 'page'));
    }

    public function index()
    {
        // $posts = Post::orderBy('created_at', 'desc')->get(); //가장 최근것을 먼저 보여줌
        // $posts = Post::latest()->get(); //가장 최근 것
        // dd($posts[0]->created_at);
        // $posts = Post::paginate(5); //한 페이지에 5개씩 보여줌, paginate는 항상 마지막에 써야함
        $posts = Post::latest()->paginate(5); //페이지 내림차순
        // dd($posts);
        return view('posts.index', ['posts' => $posts]);
    }

    public function create()
    {
        // dd('OK');
        return view('posts.create');
    }

    public function store(Request $request)
    {
        // $request->input['title'];
        // $request->input['content'];

        $title = $request->title;
        $content = $request->content;

        $request->validate([
            'title' => 'required|min:3', //title이 최소 3자 이상
            'content' => 'required',
            'imageFile' => 'image|max:2000'
        ]);

        //dd($request);

        //DB에 저장
        $post = new Post();
        $post->title = $title;
        $post->content = $content;
        $post->user_id = Auth::user()->id;

        //File 처리
        //내가 원하는 파일시스템 상의 위치에 원하는 이름으로
        //파일을 저장하고
        if ($request->file('imageFile')) {
            $post->image = $this->uploadPostImage($request);
        }

        $post->save();
        //결과 뷰를 반환
        return redirect('/posts/index');
        // $posts = Post::paginate(5);
        // return view('posts.index', ['posts' => $posts]); //refresh할 경우 다시 데이터가 저장

    }
    public function uploadPostImage($request)
    {
        $name = $request->file('imageFile')->getClientOriginalName();
        //$name = galaxy.jpg

        $extension = $request->file('imageFile')->extension();
        // $extension = 'jpg'

        //galaxy.jpg
        //galaxy_123ajdkflajf.jpg
        $nameWithoutExtension = Str::of($name)->basename('.' . $extension);
        //$nameWithoutExtension = 'galaxy';

        // dd($nameWithoutExtension);
        // dd($name . 'extension:' . $extension);
        $fileName = $nameWithoutExtension . '_' . time() . '.' . $extension;
        //$fileName = 'galaxy'.'_'.'123453543'.'jpg';

        // dd($fileName);
        $request->file('imageFile')->storeAs('public/images', $fileName);
        //$request->imageFile
        //그 파일 이름을 칼럼에 저장
        return $fileName;
    }

    public function edit(Post $post)
    {
        //$post = Post::find($id); //primary key만 가능
        // $post = Post::where('id', $id)->first();
        // dd($post);
        //수정 폼 생성
        return view('posts.edit')->with('post', $post);
    }
    public function update(Request $request, $id) //라우터 파라미터보다 앞에 인덱션 받는 객체가 와야한다
    {
        //validation
        $request->validate([
            'title' => 'required|min:3',
            'content' => 'required',
            'imageFile' => 'image|max:2000'
        ]);
        $post = Post::find($id);
        //이미지 파일 수정. 파일 시스템에서
        if ($request->file('imageFile')) {
            $imagePath = 'public/image/' . $post->image;
            Storage::delete($imagePath);
            $post->image = $this->uploadPostImage($request);
        }
        //게시글을 데이터베이스에서 수정
        $post->title = $request->title;
        $post->content = $request->content;
        $post->save();

        return redirect()->route('post.show', ['id' => $id]);
    }
    public function destroy($id)
    {
        //파일 시스템에서 이미지 파일 삭제
        //게시글을 데이터베이스에서 삭제
    }
}