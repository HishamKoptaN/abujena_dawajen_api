@extends('layouts.navbar')
@section('content')
    <div class="w-5/6 mx-auto pt-16">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl text-black font-extrabold ">Welcome <span class="font-medium">Kaytwolea Superadmin</span>
            </h1>
        </div>

        <div class="grid grid-cols-2 w-1/2 gap-6 mt-10">
            <div class="bg-gradient-to-r from-orange-500 to-[#eb4432] rounded-2xl shadow-2xl">{{ count($users) }}</div>
            <div class="bg-white shadow-2xl">{{ count($jobs) }}</div>
        </div>

    </div>
@endsection
