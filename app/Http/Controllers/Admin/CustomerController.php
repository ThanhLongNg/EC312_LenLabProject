<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class CustomerController extends Controller
{
    // Danh sách khách hàng
    public function index()
    {
        $customers = User::all();
        return view('admin.customers.index_simple', compact('customers'));
    }

    // Form thêm khách hàng
    public function create()
    {
        return view('admin.customers.create_simple');
    }

    // Lưu khách hàng mới
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'gender'   => 'required'
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => bcrypt($request->password),
            'gender'   => $request->gender,
        ]);

        return redirect()->route('admin.customers.index')
            ->with('success', 'Thêm khách hàng thành công!');
    }

    // Xóa khách hàng
    public function destroy($id)
    {
        User::findOrFail($id)->delete();

        return redirect()->route('admin.customers.index')
            ->with('success', 'Xóa khách hàng thành công!');
    }
}
