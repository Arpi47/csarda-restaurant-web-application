<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactInformation;
use App\Models\ContactSetting;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        $socialLinks = ContactSetting::orderBy('sort_order')->get();
        $contactInformation = ContactInformation::first();

        return view(
            'admin.contact.index',
            compact(
                'socialLinks',
                'contactInformation'
            )
        );
    }

    public function updateInformation(Request $request)
    {
        $validated = $request->validate([
            'phone' => [
                'required',
                'string',
                'max:50',
            ],
            'email' => [
                'required',
                'email',
                'max:255',
            ],
        ]);

        $contactInformation = ContactInformation::first();

        $contactInformation->update([
            'phone' => $validated['phone'],
            'email' => $validated['email'],
        ]);

        return redirect()
            ->route('admin.contact.index')
            ->with(
                'success',
                __('messages.contact_information_updated')
            );
    }

    public function storeSocial(Request $request)
    {
        $validated = $request->validate([
            'platform' => [
                'required',
                'string',
                'max:50',
                'unique:contact_settings,platform',
            ],
            'url' => [
                'required',
                'url',
                'max:255',
            ],
        ]);

        ContactSetting::create([
            'platform' => $validated['platform'],
            'url' => $validated['url'],
            'sort_order' => ContactSetting::max('sort_order') + 1,
            'is_active' => true,
        ]);

        return redirect()
            ->route('admin.contact.index')
            ->with('success', __('messages.contact_social_created'));
    }

    public function editSocial(ContactSetting $contactSetting)
    {
        return view('admin.contact.edit', compact('contactSetting'));
    }

    public function updateSocial(Request $request, ContactSetting $contactSetting)
    {
        $validated = $request->validate([
            'platform' => [
                'required',
                'string',
                'max:50',
                'unique:contact_settings,platform,'.$contactSetting->id,
            ],
            'url' => [
                'required',
                'url',
                'max:255',
            ],
            'is_active' => [
                'nullable',
                'boolean',
            ],
        ]);

        $contactSetting->update([
            'platform' => $validated['platform'],
            'url' => $validated['url'],
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.contact.index')
            ->with('success', __('messages.contact_social_updated'));
    }

    public function destroySocial(ContactSetting $contactSetting)
    {
        $contactSetting->delete();

        ContactSetting::orderBy('sort_order')
            ->get()
            ->each(function ($item, $index) {
                $item->update([
                    'sort_order' => $index + 1,
                ]);
            });

        return redirect()
            ->route('admin.contact.index')
            ->with('success', __('messages.contact_social_deleted'));
    }

    public function reorderSocial(Request $request)
    {
        $validated = $request->validate([
            'items' => [
                'required',
                'array',
            ],
            'items.*.id' => [
                'required',
                'integer',
                'exists:contact_settings,id',
            ],
            'items.*.sort_order' => [
                'required',
                'integer',
                'min:1',
            ],
        ]);

        foreach ($validated['items'] as $item) {
            ContactSetting::where('id', $item['id'])
                ->update([
                    'sort_order' => $item['sort_order'],
                ]);
        }

        return response()->json([
            'success' => true,
        ]);
    }
}
