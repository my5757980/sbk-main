<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\StoreTestimonialRequest;
use App\Http\Requests\Admin\UpdateTestimonialRequest;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TestimonialController extends Controller
{
    private $testimonial;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Testimonial $testimonial)
    {
        $this->middleware('auth:admin');
        $this->testimonial = $testimonial;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $data = $this->testimonial->allTestimonial();
            return view('admin.testimonials.index', compact('data'));
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
        $data = new Testimonial();
        $form = [
            'type' => 'create',
            'heading' => 'Add Testimonial',
            'method' => 'POST',
            'action' => route('admin.testimonial.store'),
            'cancel_url' => route('admin.testimonial.index')
        ];
        return view('admin.testimonials.form', compact('form', 'data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTestimonialRequest $request)
    {
        DB::beginTransaction();
        try{
            $data = $request->except(
                [
                    '_method',
                    '_token',
                    'previous_image',
                    'image'
                ]
            );
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $data['image'] = $this->uploadFile($file, 'testimonial');
            }
            $this->testimonial->storeTestimonial($data);
            DB::commit();
            return redirect()
                ->route('admin.testimonial.index')
                ->with('success', 'Testimonial has been added successfully.');
        }catch (\Exception $exception) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', $exception->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Gallery  $gallery
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = $this->testimonial->findTestimonial($id);
        return view('admin.testimonials.show', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $gallery
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = $this->testimonial->findTestimonial($id);

        $form = [
            'type' => 'create',
            'heading' => 'Edit Testimonial',
            'method' => 'PUT',
            'action' => route('admin.testimonial.update', $id),
            'cancel_url' => route('admin.testimonial.index')
        ];

        return view('admin.testimonials.form', compact('data', 'form'));
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
            $testimonial = $this->testimonial->deleteTestimonial($id);
            return redirect()
                ->route('admin.testimonial.index')
                ->with('success', 'Testimonial has been deleted successfully.');
        }catch (\Exception $exception) {
            return redirect()
                ->back()
                ->with('error', $exception->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $gallery
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTestimonialRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->except(
                [
                    '_method',
                    '_token',
                    'previous_image',
                    'image'
                ]
            );

            $previousimage = $request->previous_image;

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $data['image'] = $this->updateFile($file, $previousimage, 'name');
            } else {
                $data['image'] = $previousimage;
            }

            $this->testimonial->whereUpdate(['id' => $request->id], $data);
            DB::commit();
            return redirect()
                ->route('admin.testimonial.index')
                ->with('success', 'Testimonial has been updated successfully.');
        } catch (\Exception $exception) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', $exception->getMessage());
        }
    }
}

