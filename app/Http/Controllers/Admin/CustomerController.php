<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerGroup;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(): View
    {
        $customers = Customer::query()->with('group')->latest()->paginate(20);

        return view('admin.customers.index', compact('customers'));
    }

    public function create(): View
    {
        return view('admin.customers.create', ['groups' => CustomerGroup::query()->orderBy('name')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email'],
            'phone' => ['nullable', 'string', 'max:50'],
            'customer_group_id' => ['nullable', 'uuid', 'exists:customer_groups,id'],
        ]);

        $customer = Customer::create($data + ['is_active' => true]);

        return redirect()->route('admin.customers.edit', $customer)->with('status', __('messages.customer_created'));
    }

    public function edit(Customer $customer): View
    {
        return view('admin.customers.edit', [
            'customer' => $customer,
            'groups' => CustomerGroup::query()->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email'],
            'phone' => ['nullable', 'string', 'max:50'],
            'customer_group_id' => ['nullable', 'uuid', 'exists:customer_groups,id'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $customer->update($data + ['is_active' => $request->boolean('is_active')]);

        return redirect()->route('admin.customers.index')->with('status', __('messages.customer_updated'));
    }
}


