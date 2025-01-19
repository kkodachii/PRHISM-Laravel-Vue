<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    Auth::check();
    return  $user->id ===  User::find($id)->id;
});

Broadcast::channel('generic.name', function ($user) {
    return  Auth::check();
});
