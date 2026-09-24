<?php

namespace App\Http\Controllers\admin;

use Mail;
use App\Http\Requests\Admin\StoreAdministartorRequest;
use App\Http\Requests\Admin\UpdateAdministartorRequest;
use App\Http\Controllers\Admin\Controller;
use App\Models\Admin;
use App\Models\AdminRole;
use Illuminate\Http\Request;

class AdministratorsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Admin::with('role')->get();

        return view('admin.administrators.index',compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = AdminRole::orderBy('name')->get();

        return view('admin.administrators.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAdministartorRequest $request)
    {
        $data = $request->except([
            '_token',
            '_method',
            'image'
        ]);

        //move | upload file on server
         if ($request->hasFile('image')) {
            $file          = $request->file('image');
            $extension     = $file->getClientOriginalExtension();
            $filename      = 'admin-profile-'.time() . '.' . $extension;
            $file->move(uploadsDir('admin'), $filename);
            $data['image'] = $filename;
        }

        $password         = generateRandomString(8);
        $data['password'] = bcrypt($password);

            if ($data['email'] != '') {
                Mail::send(
                    'emails.admin.created',
                    [
                        'data'     => $data,
                        'password' => $password,
                    ],
                    function ($message) use ($data) {
                        $email   = $data['email'];
                        $message->to($email, $email);
                        $message->replyTo(config('mail.from.address'), config('mail.from.name'));
                        $subject = "Account created.";
                        $message->subject($subject);
                    }
                );
            }

        // generate-random-8digits-password (send in mail & store in DB).

        $admin = Admin::create($data);

        return redirect()
            ->route('admin.administrators.edit', $admin->id)
            ->with('success', 'Administrator has been added successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = Admin::find($id);

        return view('admin.administrators.show', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
      $data = Admin::find($id);
      $roles = AdminRole::orderBy('name')->get();
        if (auth('admin')->user()->is_system_admin == 1 || auth('admin')
            ->user()->id == $data->id || $data->is_system_admin == 0)
        {
            return view('admin.administrators.edit', compact('data', 'roles'));
        }
        else
        {
            return redirect()
                ->route('admin.administrators.index')
                ->with('error', 'You can not change other admin details.');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAdministartorRequest $request, $id)
    {
        $admin = Admin::findOrFail($id);

        if (auth('admin')->user()->is_system_admin == 1 || auth('admin')
    ->user()->id == $admin->id || $admin->is_system_admin == 0)
     {

        $data = $request->except([
            '_token',
            '_method',
            'email',
            'previous_image',
            'image',
            'password',
            'password_confirmation'
        ]);

        if ((int) $admin->is_system_admin === 1 && auth('admin')->id() !== $admin->id) {
            unset($data['admin_role_id']);
        }

        //move | upload file on server
         if ($request->hasFile('image')) {
            $file      = $request->file('image');
            $extension = $file->getClientOriginalExtension();
            $filename  = 'admin-profile-'.time() . '.' . $extension;
            $file->move(uploadsDir('admin'), $filename);

            if ($request->previous_image != '' && file_exists(uploadsDir('admin') . $request->previous_image)) {
                unlink(uploadsDir('admin') . $request->previous_image);
            }

            $data['image'] = $filename;
        }

        if (isset($request->password) && $request->password !='') {
            $data['password'] = bcrypt($request->password);
        }

        Admin::where('id', $id)->update($data);

        return redirect()
            ->back()
            ->with('success', 'Administrator has been updated successfully.');
        }
         return redirect()
                ->route('admin.administrators.index')
                ->with('error', 'You can not change other admin details.');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = Admin::find($id);

        if (auth('admin')->id() != $id && auth('admin')->user()->is_system_admin != 1) {
            return redirect()
                ->route('admin.administrators.index')
                ->with('error', 'You can not delete admin.');
        }

        if ($data->image != '' && file_exists(uploadsDir('admin') . $data->filename)) {
            unlink(uploadsDir('admin') . $data->image);
        }

        Admin::where('id', $id)->delete();

        return redirect()
            ->route('admin.administrators.index')
            ->with('success', 'Administrator was removed successfully!');
    }
    public function EditProfile()
    {
        $data = Admin::find(auth('admin')->user()->id);
        return view('admin.profile-update', compact('data'));
    }
}
