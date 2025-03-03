<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CoffeeListing;
use Illuminate\Support\Facades\Auth;

class FarmersDashboardController extends Controller {
    public function __construct() {
        $this->middleware('auth'); // Ensure authentication
    }

    // 🏡 Farmers Dashboard
    public function dashboard() {
        return view('pages.farmers');
    }

    public function create()
    {
        $user = Auth::user(); // Get the authenticated user
        return view('farmer.create', compact('user'));
    }

    // 📜 View Coffee Listings
    public function coffeeListings() {
        $coffeeListings = CoffeeListing::where('user_id', Auth::id())->get();
        return view('farmer.coffeelistings', compact('coffeeListings'));
    }

    // ➕ Add Coffee Listing (Form)
    public function createCoffee() {
        return view('farmer.create');
    }

    // 💾 Store Coffee Listing
    public function storeCoffee(Request $request) {
        $request->validate([
            'coffee_type' => 'required|in:Speciality,Robusta,Arabica',
            'quantity' => 'required|numeric|min:1',
            'price_per_kg' => 'required|numeric|min:1',
            'coffee_grade' => 'required|string',
            'wallet_number' => 'required|string',
        ]);

        CoffeeListing::create([
            'user_id' => Auth::id(),
            'coffee_type' => $request->coffee_type,
            'quantity' => $request->quantity,
            'price_per_kg' => $request->price_per_kg,
            'coffee_grade' => $request->coffee_grade,
            'status' => 'Available',
            'wallet_number' => $request->wallet_number,
        ]);

        return redirect()->route('farmer.coffeelistings')->with('success', 'Coffee listing added!');
    }

    // ✏️ Edit Coffee Listing (Form)
    public function editCoffee($id) {
        $listing = CoffeeListing::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        return view('farmer.edit', compact('listing'));
    }

    // 🔄 Update Coffee Listing
    public function updateCoffee(Request $request, $id) {
        $listing = CoffeeListing::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        // Prevent editing if sold
        if ($listing->status === 'Sold Out') {
            return redirect()->route('farmer.coffeelistings')->with('error', 'You cannot edit sold coffee.');
        }

        $request->validate([
            'coffee_type' => 'required|in:Speciality,Robusta,Arabica',
            'quantity' => 'required|numeric|min:1',
            'price_per_kg' => 'required|numeric|min:1',
            'coffee_grade' => 'required|string',
            'wallet_number' => 'required|string',
        ]);

        $listing->update($request->all());

        return redirect()->route('farmer.coffeelistings')->with('success', 'Coffee listing updated!');
    }

    // 📄 Profile Page
    public function profile() {
        return view('farmer.profile');
    }

    // 🌍 Communities Page
    public function communities() {
        return view('farmer.communities');
    }
}