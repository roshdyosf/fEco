<?php

namespace App\Http\Controllers;

use App\Http\Requests\FamilyRequest;
use App\Http\Requests\test;
use App\Models\Family;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Services\FamilyService;

class FamilyController extends Controller
{
    public function __construct(private FamilyService $familyService) {}
    // Show family setup page
    public function showSetup()
    {
        /** @var User $user */
        $user = Auth::user();
        if ($user->family_id) {
            return redirect()->route('dashboard');
        }
        return Inertia::render('Family/Setup');
    }


    // Family creation logic
    public function store(FamilyRequest $request)
    {

        $data = $request->validated();
        $family = $this->familyService->createFamily($data, Auth::user());

        return redirect()->route('dashboard')->with('success', 'Family created successfully! Your invite code is: ' . $family->invite_code);
    }

    // Join family using invite code
    public function join(FamilyRequest $request)
    {
        $data = $request->validated();
        $joined = $this->familyService->joinFamily($request->invite_code, Auth::user());
        if (!$joined) {
            return back()->withErrors(['invite_code' => 'Invalid invite code.']);
        }
        return redirect()->route('dashboard');
    }

    // Show family settings page
    public function settings()
    {
        $user = Auth::user();
        $family = $user->family;

        // Retrieve family members with only the necessary fields
        $members = $family->users()->select('id', 'name', 'email')->get();

        return Inertia::render('Family/Settings', [
            'family' => $family,
            'members' => $members
        ]);
    }

    //regenerate invite code (allowed for family-head only)
    public function regenerateInviteCode()
    {
        $this->familyService->regenerateCode(Auth::user()->family);

        return back()->with('success', 'Invite code regenerated successfully.');
    }

    // Remove a family member (allowed for family-head only)
    public function removeMember(Request $request, User $member)
    {
        $removed = $this->familyService->removeMember($member, Auth::user());
        
        if (!$removed) {
            abort(403, 'Unauthorized action.');
        }
        return back()->with('success', 'Member removed successfully.');
    }
}
