<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use Illuminate\Http\Request;

class RegistrationController extends Controller
{
    public function create()
    {
        return view('registration.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'weeks'        => ['required', 'array', 'min:1'],
            'weeks.*'      => ['integer', 'between:1,4'],
            'days'         => ['required', 'array', 'min:1'],
            'days.*'       => ['string', 'in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu'],
            'budget'       => ['required', 'integer', 'min:50000', 'max:500000'],
        ]);

        Registration::create($validated);

        return redirect('/success');
    }

    public function success()
    {
        return view('registration.success');
    }
}
