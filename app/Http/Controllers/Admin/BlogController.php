<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\StoreBlogRequest;
use App\Http\Requests\Admin\UpdateBlogRequest;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BlogController extends Controller
{
    private $blog;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Blog $blog)
    {
        $this->middleware('auth:admin');
        $this->blog = $blog;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $data = $this->blog->allBlog();
            return view('admin.blogs.index', compact('data'));
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = new Blog();

        $form = [
            'type' => 'create',
            'heading' => 'Create Blog',
            'method' => 'POST',
            'action' => route('admin.blog.store'),
            'cancel_url' => route('admin.blog.index')
        ];

        return view('admin.blogs.form', compact('data', 'form'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreBlogRequest $request)
    {
        try{
            DB::beginTransaction();
            $data = $request->except(
                [
                    '_method',
                    '_token',
                ]
            );
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $data['image'] = $this->uploadFile($file, 'blog');
            }

            $data['slug'] = generateBlogUniqueSlug($request->slug);

            $this->blog->storeBlog($data);
            DB::commit();
        }catch (\Exception $exception) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', $exception->getMessage());
        }
        return redirect()
            ->route('admin.blog.index')
            ->with('success', 'Blog has been added successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Gallery  $gallery
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = $this->blog->findBlog($id);
        return view('admin.blogs.show', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $gallery
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = $this->blog->findBlog($id);
        $form = [
            'type' => 'create',
            'heading' => 'Edit Blog',
            'method' => 'PUT',
            'action' => route('admin.blog.update', $id),
            'cancel_url' => route('admin.blog.index')
        ];
        return view('admin.blogs.form', compact('data', 'form'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $gallery
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateBlogRequest $request)
    {
        try {
            DB::beginTransaction();

            $blog = Blog::findOrFail($request->id);

            $data = $request->except(
                [
                    '_method',
                    '_token',
                    'previous_image'
                ]
            );

            $data['slug'] = generateBlogUniqueSlug($request->slug, $blog->slug);

            $previousimage = $request->previous_image;

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $data['image'] = $this->updateFile($file, $previousimage, 'blog');
            } else {
                $data['image'] = $previousimage;
            }

            $blog->update($data);

            DB::commit();
            return redirect()
                ->route('admin.blog.index')
                ->with('success', 'Blog has been updated successfully.');
        } catch (\Exception $exception) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', $exception->getMessage());
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MediaFile  $mediaFile
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $blog = $this->blog->deleteBlog($id);
            return redirect()
                ->route('admin.blog.index')
                ->with('success', 'Blog has been deleted successfully.');
        }catch (\Exception $exception) {
            return redirect()
                ->back()
                ->with('error', $exception->getMessage());
        }
    }
}
