<?php

namespace App\Http\Controllers\Admin;

use App\Models\NewsLetter;
use Illuminate\Http\Request;

class NewsLettersController extends Controller
{
    private $newsletter;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(NewsLetter $newsletter)
    {
        $this->middleware('auth:admin');
        $this->newsletter = $newsletter;


    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $data = $this->newsletter->allNewsletter();
            return view('admin.newsletters.index', compact('data'));
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
            return redirect()->back();
        }
    }

    public function show($id)
    {
        $data = $this->newsletter->findNewsletter($id);
        return view('admin.newsletters.show', compact('data'));
    }


    public function destroy($id)
    {
        try {
            $newsletter = $this->newsletter->deleteNewletter($id);
            return redirect()
                ->route('admin.newsletters.index')
                ->with('success', 'Newsletter has been deleted successfully.');
        }catch (\Exception $exception) {
            return redirect()
                ->back()
                ->with('error', $exception->getMessage());
        }
    }
}
