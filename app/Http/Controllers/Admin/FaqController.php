<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\StoreFaqRequest;
use App\Http\Requests\Admin\UpdateFaqRequest;
use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FaqController extends Controller
{
    private $faq;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Faq $faq)
    {
        $this->middleware('auth:admin');
        $this->faq = $faq;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $data = $this->faq->allFaq();
            return view('admin.faqs.index', compact('data'));
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
        $data = new Faq();
        $form = [
            'type' => 'create',
            'heading' => 'Add Faq',
            'method' => 'POST',
            'action' => route('admin.faq.store'),
            'cancel_url' => route('admin.faq.index')
        ];
        return view('admin.faqs.form', compact('data', 'form'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreFaqRequest $request)
    {
        try{
            DB::beginTransaction();
            $data = $request->except(
                [
                    '_method',
                    '_token',
                    'id'
                ]
            );
            $this->faq->storeFaq($data);
            DB::commit();
        }catch (\Exception $exception) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', $exception->getMessage());
        }
        return redirect()
            ->route('admin.faq.index')
            ->with('success', 'Faq has been added successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Gallery  $gallery
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = $this->faq->findFaq($id);
        return view('admin.faqs.show', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $gallery
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = $this->faq->findFaq($id);
        $form = [
            'type' => 'create',
            'heading' => 'Edit Faq',
            'method' => 'PUT',
            'action' => route('admin.faq.update', $id),
            'cancel_url' => route('admin.faq.index')
        ];
        return view('admin.faqs.form', compact('data', 'form'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $gallery
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateFaqRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->except(
                [
                    '_method',
                    '_token',
                ]
            );
            $this->faq->whereUpdate(['id' => $request->id], $data);
            DB::commit();
            return redirect()
                ->route('admin.faq.index')
                ->with('success', 'Faq has been updated successfully.');
        } catch (\Exception $exception) {
            DB::rollBack();

            return redirect()
                ->back()
                ->with('errors', $exception->getMessage());
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
            $faq = $this->faq->deleteFaq($id);
            return redirect()
                ->route('admin.faq.index')
                ->with('success', 'Faq has been deleted successfully.');
        }catch (\Exception $exception) {
            return redirect()
                ->back()
                ->with('error', $exception->getMessage());
        }
    }
}
