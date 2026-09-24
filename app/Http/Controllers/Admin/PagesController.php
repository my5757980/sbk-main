<?php

namespace App\Http\Controllers\Admin;

use App\Models\Page;
use App\Models\MediaFile;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\StorePageRequest;
use App\Http\Requests\Admin\UpdatePageRequest;
use Illuminate\Support\Facades\DB;
use mysql_xdevapi\Exception;

class PagesController extends Controller
{
    private $pageRepository;
    private $mediaFileRepository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:admin');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Page::all();

        return view('admin.pages.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = new Page();

        $form = [
            'type' => 'create',
            'heading' => 'Create Page',
            'method' => 'POST',
            'action' => route('admin.pages.store'),
            'cancel_url' => route('admin.pages.index')
        ];

        return view('admin.pages.form', compact('form', 'data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePageRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->except([
                '_token',
                '_method',
                'image',
                'previous_image'
            ]);

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $data['image'] = $this->uploadFile($file, 'page');
            }

            $data['slug'] = generatePageUniqueSlug($request->slug);

            Page::create($data);

            DB::commit();
            return redirect()
                ->route('admin.pages.index')
                ->with('success', 'Page has been added successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()
                ->route('admin.pages.create')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Page  $page
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = Page::getPageDetailsWithMedia(['pages.id' => $id]);

        if (!$data)
        {
            return redirect()
                ->route('admin.pages.index')
                ->with('error', 'Page deos not exist.');
        }

        return view('admin.pages.show', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $page
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = Page::getPageDetailsWithMedia(['pages.id' => $id]);


        if (!$data)
        {
            return redirect()
                ->route('admin.pages.index')
                ->with('error', 'Page deos not exist.');
        }
        $form = [
            'type' => 'edit',
            'heading' => 'Edit Page',
            'method' => 'PUT',
            'action' => route('admin.pages.update', $data->id),
            'cancel_url' => route('admin.pages.index')
        ];

        return view('admin.pages.form', compact('form', 'data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $page
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePageRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $data = $request->except([
                '_token',
                '_method',
                'page_id',
                'previous_image',
                'image'
            ]);

            $page = Page::find($id);

            if (!$page)
            {
                return redirect()
                    ->back()
                    ->with('error', 'Page deos not exist.');
            }

            // Slug of seeder based pages, need not to update,
            // as they are created from seeder.
//            if ($page->is_system_page == '1') {
//                unset($data['slug']);
//            } else {
//            }
            $data['slug'] = generatePageUniqueSlug($request->page_title, $page->slug);

            $previousimage = $request->previous_image;

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $data['image'] = $this->updateFile($file, $previousimage, 'name');
            } else {
                $data['image'] = $previousimage;
            }

            $data['is_system_page'] = (isset($request->is_system_page) && $request->is_system_page == 1) ? $request->is_system_page : 0;

            $page->update($data);
            DB::commit();
            return redirect()
                ->route('admin.pages.index')
                ->with('success', 'Page updated sucessfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->route('admin.pages.create')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Page  $page
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Page::where('id', $id)->delete();

        return redirect()
            ->route('admin.pages.index')
            ->with('success', 'Page was removed successfully!');
    }
}
